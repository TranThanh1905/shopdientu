<?php
// app/Http/Controllers/Admin/AdminOrderController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\InventoryTransaction;
use App\Models\OrderConfirmationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class AdminOrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng
     */
    public function index(Request $request)
    {
        $query = Order::with(['orderDetails.product', 'user']);

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo ngày
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Tìm kiếm theo mã đơn, tên, email, phone, mã KH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('customer_code', 'like', "%{$search}%")
                  ->orWhere('fullname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $orders = $query->paginate(20);

        // Thống kê tổng quan
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'shipping' => Order::where('status', 'shipping')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'returned' => Order::where('status', 'returned')->count(),
            'damaged' => Order::where('status', 'damaged')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show($id)
    {
        $order = Order::with(['orderDetails.product.category', 'user'])->findOrFail($id);
        
        // Tính toán thống kê đơn hàng
        $orderStats = [
            'total_items' => $order->orderDetails->sum('quantity'),
            'total_cost' => $order->orderDetails->sum(function($detail) {
                return $detail->purchase_price * $detail->quantity;
            }),
            'total_revenue' => $order->final_amount,
            'total_profit' => $order->orderDetails->sum(function($detail) {
                return ($detail->final_price - $detail->purchase_price) * $detail->quantity;
            }),
        ];

        return view('admin.orders.show', compact('order', 'orderStats'));
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,shipping,completed,cancelled,returned,damaged',
            'note' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $order = Order::with('orderDetails.product')->findOrFail($id);
            $oldStatus = $order->status;
            $newStatus = $request->status;

            // Xử lý logic kho hàng khi thay đổi trạng thái
            $this->handleInventoryOnStatusChange($order, $oldStatus, $newStatus);

            // Cập nhật trạng thái
            $order->update([
                'status' => $newStatus,
                'note' => $request->note ? ($order->note . "\n" . now()->format('d/m/Y H:i') . ": " . $request->note) : $order->note
            ]);

            OrderConfirmationLog::create([
                'order_id' => $order->id,
                'confirmed_by' => Auth::id(),
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'note' => $request->note,
                'ip_address' => $request->ip()
            ]);

            // Cập nhật thống kê khách hàng nếu hoàn thành
            if ($newStatus === 'completed' && $order->user) {
                $order->user->updatePurchaseStats();
            }

            DB::commit();

            return redirect()->route('admin.orders.show', $id)
                ->with('success', 'Cập nhật trạng thái thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Cập nhật thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Xử lý kho hàng khi thay đổi trạng thái
     */
    private function handleInventoryOnStatusChange($order, $oldStatus, $newStatus)
    {
        // Từ pending -> confirmed: Trừ kho
        if ($oldStatus === 'pending' && $newStatus === 'confirmed') {
            foreach ($order->orderDetails as $detail) {
                $inventory = $detail->product->inventory;
                
                if (!$inventory || !$inventory->reduceStock($detail->quantity)) {
                    throw new \Exception("Sản phẩm '{$detail->product->name}' không đủ tồn kho!");
                }

                // Ghi log xuất kho
                InventoryTransaction::create([
                    'product_id' => $detail->product_id,
                    'user_id' => Auth::id(),
                    'type' => 'out',
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->final_price,
                    'note' => "Xuất kho cho đơn hàng {$order->order_code}",
                    'order_id' => $order->id
                ]);
            }
        }

        // Từ confirmed/shipping -> cancelled: Hoàn kho
        if (in_array($oldStatus, ['confirmed', 'shipping']) && $newStatus === 'cancelled') {
            foreach ($order->orderDetails as $detail) {
                $inventory = $detail->product->inventory;
                $inventory->increment('quantity_in_stock', $detail->quantity);
                $inventory->decrement('quantity_sold', $detail->quantity);

                // Ghi log nhập lại kho
                InventoryTransaction::create([
                    'product_id' => $detail->product_id,
                    'user_id' => Auth::id(),
                    'type' => 'returned',
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->purchase_price,
                    'note' => "Hoàn kho do hủy đơn {$order->order_code}",
                    'order_id' => $order->id
                ]);
            }
        }

        // Từ completed -> returned: Hoàn kho và đánh dấu trả hàng
        if ($oldStatus === 'completed' && $newStatus === 'returned') {
            foreach ($order->orderDetails as $detail) {
                $inventory = $detail->product->inventory;
                $inventory->returnStock($detail->quantity);

                // Ghi log trả hàng
                InventoryTransaction::create([
                    'product_id' => $detail->product_id,
                    'user_id' => Auth::id(),
                    'type' => 'returned',
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->purchase_price,
                    'note' => "Khách trả hàng - Đơn {$order->order_code}",
                    'order_id' => $order->id
                ]);
            }
        }

        // Từ bất kỳ -> damaged: Đánh dấu hàng hỏng
        if ($newStatus === 'damaged') {
            foreach ($order->orderDetails as $detail) {
                $inventory = $detail->product->inventory;
                
                // Nếu chưa xuất kho thì trừ kho và đánh dấu hỏng
                if ($oldStatus === 'pending') {
                    $inventory->markAsDamaged($detail->quantity);
                } else {
                    // Nếu đã xuất kho, chỉ tăng số lượng hỏng
                    $inventory->increment('quantity_damaged', $detail->quantity);
                }

                // Ghi log hàng hỏng
                InventoryTransaction::create([
                    'product_id' => $detail->product_id,
                    'user_id' => Auth::id(),
                    'type' => 'damaged',
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->purchase_price,
                    'note' => "Hàng hỏng/lỗi - Đơn {$order->order_code}",
                    'order_id' => $order->id
                ]);
            }
        }
    }

    /**
     * Xử lý trả hàng với lý do
     */
    public function processReturn(Request $request, $id)
    {
        $request->validate([
            'return_reason' => 'required|string',
            'refund_amount' => 'nullable|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            $order = Order::with('orderDetails.product')->findOrFail($id);

            if (!in_array($order->status, ['completed', 'shipping'])) {
                return back()->with('error', 'Chỉ có thể trả hàng cho đơn đã hoàn thành hoặc đang giao!');
            }

            // Hoàn kho
            foreach ($order->orderDetails as $detail) {
                $inventory = $detail->product->inventory;
                $inventory->returnStock($detail->quantity);

                InventoryTransaction::create([
                    'product_id' => $detail->product_id,
                    'user_id' => Auth::id(),
                    'type' => 'returned',
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->purchase_price,
                    'note' => "Trả hàng: " . $request->return_reason,
                    'order_id' => $order->id
                ]);
            }

            // Cập nhật đơn hàng
            $order->update([
                'status' => 'returned',
                'return_reason' => $request->return_reason
            ]);

            // Cập nhật thống kê khách hàng
            if ($order->user) {
                $order->user->updatePurchaseStats();
            }

            DB::commit();

            return redirect()->route('admin.orders.show', $id)
                ->with('success', 'Đã xử lý trả hàng thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xử lý trả hàng thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Đánh dấu hàng hỏng/lỗi
     */
    public function markDamaged(Request $request, $id)
    {
        $request->validate([
            'return_reason' => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            $order = Order::with('orderDetails.product')->findOrFail($id);

            // Xử lý kho
            foreach ($order->orderDetails as $detail) {
                $inventory = $detail->product->inventory;
                
                if ($order->status === 'pending') {
                    $inventory->markAsDamaged($detail->quantity);
                } else {
                    $inventory->increment('quantity_damaged', $detail->quantity);
                }

                InventoryTransaction::create([
                    'product_id' => $detail->product_id,
                    'user_id' => Auth::id(),
                    'type' => 'damaged',
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->purchase_price,
                    'note' => "Hàng hỏng: " . $request->return_reason,
                    'order_id' => $order->id
                ]);
            }

            $order->update([
                'status' => 'damaged',
                'return_reason' => $request->return_reason
            ]);

            DB::commit();

            return redirect()->route('admin.orders.show', $id)
                ->with('success', 'Đã đánh dấu đơn hàng bị hỏng/lỗi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Thao tác thất bại: ' . $e->getMessage());
        }
    }

    /**
     * In hóa đơn (PDF)
     */
    public function printInvoice($id)
    {
        $order = Order::with('orderDetails.product')->findOrFail($id);
        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));
        return $pdf->download('hoa-don-' . $order->order_code . '.pdf');
    }

    /**
     * Xem trước hóa đơn
     */
    public function previewInvoice($id)
    {
        $order = Order::with('orderDetails.product')->findOrFail($id);
        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * Xóa đơn hàng
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);

            // Chỉ cho phép xóa đơn hàng đã hủy hoặc chờ xử lý
            if (!in_array($order->status, ['pending', 'cancelled'])) {
                return redirect()->route('admin.orders.index')
                    ->with('error', 'Chỉ có thể xóa đơn hàng đang chờ xử lý hoặc đã hủy!');
            }

            // Xóa các bản ghi liên quan
            InventoryTransaction::where('order_id', $order->id)->delete();
            $order->orderDetails()->delete();
            $order->delete();

            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with('success', 'Xóa đơn hàng thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.orders.index')
                ->with('error', 'Xóa đơn hàng thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Xuất báo cáo đơn hàng
     */
    public function export(Request $request)
    {
        $query = Order::with('orderDetails.product');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->get();

        $csv = "Mã đơn,Mã KH,Tên khách hàng,SĐT,Email,Tổng tiền,Giảm giá,Thanh toán,Trạng thái,Ngày đặt\n";

        foreach ($orders as $order) {
            $csv .= implode(',', [
                $order->order_code,
                $order->customer_code ?? 'Guest',
                '"' . $order->fullname . '"',
                $order->phone,
                $order->email,
                $order->total_amount,
                $order->discount_amount,
                $order->final_amount,
                $order->status_label,
                $order->created_at->format('d/m/Y H:i')
            ]) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="orders-' . date('Y-m-d') . '.csv"');
    }
}