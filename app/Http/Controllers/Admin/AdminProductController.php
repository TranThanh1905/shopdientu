<?php
// app/Http/Controllers/Admin/AdminProductController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'inventory']);

        // Lọc theo danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(15);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Hiển thị form thêm sản phẩm
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Lưu sản phẩm mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            
            // GIÁ NHẬP - GIÁ BÁN
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:purchase_price',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            
            'image' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'specifications' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            
            // SỐ LƯỢNG NHẬP KHO BAN ĐẦU
            'initial_stock' => 'nullable|integer|min:0'
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm',
            'category_id.required' => 'Vui lòng chọn danh mục',
            'purchase_price.required' => 'Vui lòng nhập giá nhập',
            'selling_price.required' => 'Vui lòng nhập giá bán',
            'selling_price.gte' => 'Giá bán phải lớn hơn hoặc bằng giá nhập',
            'discount_percent.max' => 'Phần trăm giảm giá không được vượt quá 100%',
        ]);

        DB::beginTransaction();
        try {
            // Tạo sản phẩm
            $product = Product::create([
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'purchase_price' => $validated['purchase_price'],
                'selling_price' => $validated['selling_price'],
                'discount_percent' => $validated['discount_percent'] ?? 0,
                'image' => $validated['image'],
                'description' => $validated['description'],
                'specifications' => $validated['specifications'],
                'status' => $validated['status']
            ]);

            // Tạo bản ghi inventory
            Inventory::create([
                'product_id' => $product->id,
                'quantity_in_stock' => $validated['initial_stock'] ?? 0,
                'quantity_sold' => 0,
                'quantity_damaged' => 0,
                'quantity_returned' => 0
            ]);

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Thêm sản phẩm thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Thêm sản phẩm thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị chi tiết sản phẩm
     */
    public function show($id)
    {
        $product = Product::with(['category', 'inventory', 'inventoryTransactions.user'])
            ->findOrFail($id);
        
        // Thống kê
        $stats = [
            'total_sold' => $product->inventory->quantity_sold ?? 0,
            'total_revenue' => $product->orderDetails()
                ->whereHas('order', function($q) {
                    $q->whereIn('status', ['completed', 'shipping', 'confirmed']);
                })
                ->sum(DB::raw('quantity * final_price')),
            'total_profit' => $product->orderDetails()
                ->whereHas('order', function($q) {
                    $q->whereIn('status', ['completed', 'shipping', 'confirmed']);
                })
                ->sum(DB::raw('quantity * (final_price - purchase_price)')),
        ];

        return view('admin.products.show', compact('product', 'stats'));
    }

    /**
     * Hiển thị form sửa sản phẩm
     */
    public function edit($id)
    {
        $product = Product::with('inventory')->findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Cập nhật sản phẩm
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            
            // GIÁ NHẬP - GIÁ BÁN
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:purchase_price',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            
            'image' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'specifications' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm',
            'category_id.required' => 'Vui lòng chọn danh mục',
            'purchase_price.required' => 'Vui lòng nhập giá nhập',
            'selling_price.required' => 'Vui lòng nhập giá bán',
            'selling_price.gte' => 'Giá bán phải lớn hơn hoặc bằng giá nhập',
            'discount_percent.max' => 'Phần trăm giảm giá không được vượt quá 100%',
        ]);

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Cập nhật sản phẩm thành công!');
    }

    /**
     * Xóa sản phẩm
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($id);

            // Kiểm tra có đơn hàng liên quan không
            if ($product->orderDetails()->count() > 0) {
                return redirect()->route('admin.products.index')
                    ->with('error', 'Không thể xóa sản phẩm đã có trong đơn hàng!');
            }

            // Xóa inventory trước
            $product->inventory()->delete();
            
            // Xóa inventory transactions
            $product->inventoryTransactions()->delete();
            
            // Xóa sản phẩm
            $product->delete();

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Xóa sản phẩm thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.products.index')
                ->with('error', 'Xóa sản phẩm thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật nhanh giá và discount
     */
    public function quickUpdatePrice(Request $request, $id)
    {
        $validated = $request->validate([
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:purchase_price',
            'discount_percent' => 'nullable|numeric|min:0|max:100'
        ]);

        $product = Product::findOrFail($id);
        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật giá thành công!',
            'data' => [
                'purchase_price' => number_format($product->purchase_price),
                'selling_price' => number_format($product->selling_price),
                'discount_percent' => $product->discount_percent,
                'final_price' => number_format($product->final_price),
                'profit_per_unit' => number_format($product->profit_per_unit)
            ]
        ]);
    }

    /**
     * Áp dụng discount hàng loạt
     */
    public function bulkDiscount(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'discount_percent' => 'required|numeric|min:0|max:100'
        ]);

        $count = Product::whereIn('id', $validated['product_ids'])
            ->update(['discount_percent' => $validated['discount_percent']]);

        return redirect()->route('admin.products.index')
            ->with('success', "Đã áp dụng giảm giá {$validated['discount_percent']}% cho {$count} sản phẩm!");
    }

    /**
     * Xuất báo cáo sản phẩm
     */
    public function export()
    {
        $products = Product::with(['category', 'inventory'])->get();

        $csv = "STT,Tên sản phẩm,Danh mục,Giá nhập,Giá bán,Giảm giá (%),Giá cuối,Lợi nhuận/sp,Tồn kho,Đã bán,Trạng thái\n";

        foreach ($products as $index => $product) {
            $csv .= implode(',', [
                $index + 1,
                '"' . $product->name . '"',
                '"' . $product->category->name . '"',
                $product->purchase_price,
                $product->selling_price,
                $product->discount_percent,
                $product->final_price,
                $product->profit_per_unit,
                $product->stock,
                $product->inventory->quantity_sold ?? 0,
                $product->status
            ]) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="products-' . date('Y-m-d') . '.csv"');
    }

    /**
     * Tính toán lợi nhuận dự kiến
     */
    public function calculateProfit(Request $request)
    {
        $validated = $request->validate([
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'quantity' => 'required|integer|min:1'
        ]);

        $finalPrice = $validated['selling_price'];
        if (isset($validated['discount_percent']) && $validated['discount_percent'] > 0) {
            $finalPrice = $validated['selling_price'] * (1 - $validated['discount_percent'] / 100);
        }

        $profitPerUnit = $finalPrice - $validated['purchase_price'];
        $totalProfit = $profitPerUnit * $validated['quantity'];
        $profitMargin = ($profitPerUnit / $finalPrice) * 100;

        return response()->json([
            'final_price' => $finalPrice,
            'profit_per_unit' => $profitPerUnit,
            'total_profit' => $totalProfit,
            'profit_margin' => round($profitMargin, 2)
        ]);
    }
}