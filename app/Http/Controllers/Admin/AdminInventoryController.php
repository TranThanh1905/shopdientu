<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryImport;
use App\Models\InventoryImportDetail;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminInventoryController extends Controller
{
    /**
     * Tổng quan kho hàng
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'inventory']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('stock_status')) {
            $query->whereHas('inventory', function ($q) use ($request) {
                match ($request->stock_status) {
                    'out_of_stock' => $q->where('quantity_in_stock', 0),
                    'low_stock'    => $q->where('quantity_in_stock', '>', 0)->where('quantity_in_stock', '<=', 10),
                    'in_stock'     => $q->where('quantity_in_stock', '>', 10),
                    default        => null,
                };
            });
        }

        $products = $query->latest()->paginate(20);

        // Thống kê tổng kho
        $summary = [
            'total_products'   => Product::count(),
            'out_of_stock'     => Inventory::where('quantity_in_stock', 0)->count(),
            'low_stock'        => Inventory::where('quantity_in_stock', '>', 0)->where('quantity_in_stock', '<=', 10)->count(),
            'total_sold'       => Inventory::sum('quantity_sold'),
            'total_damaged'    => Inventory::sum('quantity_damaged'),
            'total_returned'   => Inventory::sum('quantity_returned'),
        ];

        return view('admin.inventory.index', compact('products', 'summary'));
    }

    // ========== NHẬP HÀNG ==========

    /**
     * Danh sách phiếu nhập kho
     */
    public function importList(Request $request)
    {
        $imports = InventoryImport::with(['createdBy', 'details'])
            ->latest()
            ->paginate(15);

        return view('admin.inventory.import-list', compact('imports'));
    }

    /**
     * Form tạo phiếu nhập kho
     */
    public function importCreate()
    {
        $products = Product::with('inventory')->where('status', 'active')->get();
        return view('admin.inventory.import-create', compact('products'));
    }

    /**
     * Lưu phiếu nhập kho (trạng thái nháp)
     */
    public function importStore(Request $request)
    {
        $request->validate([
            'note'                => 'nullable|string',
            'products'            => 'required|array|min:1',
            'products.*.id'       => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price'    => 'required|numeric|min:0',
        ], [
            'products.required'            => 'Phải chọn ít nhất 1 sản phẩm để nhập kho',
            'products.*.quantity.required' => 'Số lượng không được bỏ trống',
            'products.*.price.required'    => 'Giá nhập không được bỏ trống',
        ]);

        DB::beginTransaction();
        try {
            $totalValue = 0;

            foreach ($request->products as $item) {
                $totalValue += $item['quantity'] * $item['price'];
            }

            $import = InventoryImport::create([
                'import_code' => InventoryImport::generateCode(),
                'created_by'  => Auth::id(),
                'total_value' => $totalValue,
                'note'        => $request->note,
                'status'      => 'draft',
            ]);

            foreach ($request->products as $item) {
                InventoryImportDetail::create([
                    'inventory_import_id' => $import->id,
                    'product_id'          => $item['id'],
                    'quantity'            => $item['quantity'],
                    'unit_price'          => $item['price'],
                    'total_price'         => $item['quantity'] * $item['price'],
                    'note'                => $item['note'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.inventory.import.show', $import->id)
                ->with('success', "Đã tạo phiếu nhập {$import->import_code}. Vui lòng xác nhận để nhập vào kho.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Tạo phiếu nhập thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Xem chi tiết phiếu nhập
     */
    public function importShow($id)
    {
        $import = InventoryImport::with(['details.product', 'createdBy'])->findOrFail($id);
        return view('admin.inventory.import-show', compact('import'));
    }

    /**
     * Xác nhận phiếu nhập → cộng thực vào kho
     */
    public function importConfirm($id)
    {
        DB::beginTransaction();
        try {
            $import = InventoryImport::with('details.product')->findOrFail($id);

            if ($import->status !== 'draft') {
                return back()->with('error', 'Phiếu nhập này đã được xử lý!');
            }

            foreach ($import->details as $detail) {
                // Tạo hoặc lấy inventory
                $inventory = Inventory::firstOrCreate(
                    ['product_id' => $detail->product_id],
                    ['quantity_in_stock' => 0]
                );

                $inventory->addStock($detail->quantity);

                // Ghi giao dịch kho
                InventoryTransaction::create([
                    'product_id' => $detail->product_id,
                    'user_id'    => Auth::id(),
                    'type'       => 'in',
                    'quantity'   => $detail->quantity,
                    'unit_price' => $detail->unit_price,
                    'note'       => "Nhập kho theo phiếu {$import->import_code}",
                ]);
            }

            $import->update(['status' => 'confirmed']);

            DB::commit();

            return redirect()->route('admin.inventory.import.list')
                ->with('success', "Phiếu nhập {$import->import_code} đã được xác nhận. Kho đã cập nhật!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xác nhận thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Hủy phiếu nhập
     */
    public function importCancel($id)
    {
        $import = InventoryImport::findOrFail($id);

        if ($import->status !== 'draft') {
            return back()->with('error', 'Chỉ có thể hủy phiếu nháp!');
        }

        $import->update(['status' => 'cancelled']);

        return redirect()->route('admin.inventory.import.list')
            ->with('success', 'Đã hủy phiếu nhập kho!');
    }

    // ========== KIỂM HÀNG (STOCK CHECK) ==========

    /**
     * Trang kiểm hàng tổng quan
     */
    public function stockCheck(Request $request)
    {
        $query = Product::with(['category', 'inventory']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->get()->map(function ($product) {
            $inv = $product->inventory;
            return [
                'id'               => $product->id,
                'name'             => $product->name,
                'category'         => $product->category->name ?? '-',
                'quantity_in_stock' => $inv->quantity_in_stock ?? 0,
                'quantity_sold'     => $inv->quantity_sold ?? 0,
                'quantity_damaged'  => $inv->quantity_damaged ?? 0,
                'quantity_returned' => $inv->quantity_returned ?? 0,
                'status'           => match(true) {
                    ($inv->quantity_in_stock ?? 0) === 0  => 'out_of_stock',
                    ($inv->quantity_in_stock ?? 0) <= 10  => 'low_stock',
                    default                               => 'in_stock',
                },
            ];
        });

        $categories = \App\Models\Category::all();

        return view('admin.inventory.stock-check', compact('products', 'categories'));
    }

    // ========== CÁC CHỨC NĂNG CŨ GIỮ NGUYÊN ==========

    public function stockIn($productId)
    {
        $product = Product::with('inventory')->findOrFail($productId);
        return view('admin.inventory.stock-in', compact('product'));
    }

    public function processStockIn(Request $request, $productId)
    {
        $validated = $request->validate([
            'quantity'   => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'note'       => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $product   = Product::findOrFail($productId);
            $inventory = Inventory::firstOrCreate(
                ['product_id' => $product->id],
                ['quantity_in_stock' => 0]
            );

            $inventory->addStock($validated['quantity']);

            InventoryTransaction::create([
                'product_id' => $product->id,
                'user_id'    => Auth::id(),
                'type'       => 'in',
                'quantity'   => $validated['quantity'],
                'unit_price' => $validated['unit_price'],
                'note'       => $validated['note'],
            ]);

            DB::commit();

            return redirect()->route('admin.inventory.index')
                ->with('success', "Đã nhập {$validated['quantity']} sản phẩm vào kho!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Nhập kho thất bại: ' . $e->getMessage());
        }
    }

    public function transactions($productId)
    {
        $product      = Product::with('inventory')->findOrFail($productId);
        $transactions = InventoryTransaction::where('product_id', $productId)
            ->with(['user', 'order'])
            ->latest()
            ->paginate(20);

        return view('admin.inventory.transactions', compact('product', 'transactions'));
    }

    public function markDamaged(Request $request, $productId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'note'     => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $product   = Product::findOrFail($productId);
            $inventory = $product->inventory;

            if (!$inventory || $inventory->quantity_in_stock < $validated['quantity']) {
                return back()->with('error', 'Số lượng tồn kho không đủ!');
            }

            $inventory->markAsDamaged($validated['quantity']);

            InventoryTransaction::create([
                'product_id' => $product->id,
                'user_id'    => Auth::id(),
                'type'       => 'damaged',
                'quantity'   => $validated['quantity'],
                'unit_price' => $product->purchase_price,
                'note'       => $validated['note'],
            ]);

            DB::commit();

            return back()->with('success', 'Đã đánh dấu hàng hỏng!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Thao tác thất bại: ' . $e->getMessage());
        }
    }
}