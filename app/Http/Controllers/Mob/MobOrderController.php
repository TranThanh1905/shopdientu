<?php

namespace App\Http\Controllers\Mob;

use App\Http\Controllers\Controller;
use App\Models\OrderConfirmationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\InventoryTransaction;

class MobOrderController extends Controller
{
    /**
     * Danh sách đơn hàng (chỉ xem, chỉ xác nhận)
     */
    public function index(Request $request)
    {
        $query = Order::with(['orderDetails.product', 'user', 'confirmationLogs.confirmedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('fullname', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(20);

        $stats = [
            'pending'   => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'shipping'  => Order::where('status', 'shipping')->count(),
        ];

        return view('mob.orders.index', compact('orders', 'stats'));
    }

    /**
     * Xem chi tiết đơn hàng
     */
    public function show($id)
    {
        $order = Order::with([
            'orderDetails.product.category',
            'user',
            'confirmationLogs.confirmedBy',
        ])->findOrFail($id);

        return view('mob.orders.show', compact('order'));
    }

    /**
     * Xác nhận đơn hàng (pending -> confirmed)
     * Mob CHỈ được xác nhận, không được làm gì khác
     */
    public function confirm(Request $request, $id)
    {
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $order = Order::with('orderDetails.product')->findOrFail($id);

            // Mob chỉ được xác nhận đơn đang chờ
            if ($order->status !== 'pending') {
                return back()->with('error', 'Chỉ có thể xác nhận đơn hàng đang chờ xử lý!');
            }

            $oldStatus = $order->status;

            // Trừ kho khi xác nhận
            foreach ($order->orderDetails as $detail) {
                $inventory = $detail->product->inventory;

                if (!$inventory || $inventory->quantity_in_stock < $detail->quantity) {
                    throw new \Exception(
                        "Sản phẩm '{$detail->product->name}' không đủ tồn kho!"
                    );
                }

                $inventory->reduceStock($detail->quantity);

                InventoryTransaction::create([
                    'product_id' => $detail->product_id,
                    'user_id'    => Auth::id(),
                    'type'       => 'out',
                    'quantity'   => $detail->quantity,
                    'unit_price' => $detail->final_price,
                    'note'       => "Xuất kho - Đơn {$order->order_code} (xác nhận bởi " . Auth::user()->name . ")",
                    'order_id'   => $order->id,
                ]);
            }

            // Cập nhật trạng thái đơn hàng
            $order->update(['status' => 'confirmed']);

            // Ghi nhật ký xác nhận
            OrderConfirmationLog::create([
                'order_id'     => $order->id,
                'confirmed_by' => Auth::id(),
                'old_status'   => $oldStatus,
                'new_status'   => 'confirmed',
                'note'         => $request->note,
                'ip_address'   => $request->ip(),
            ]);

            DB::commit();

            return redirect()->route('mob.orders.show', $id)
                ->with('success', 'Đã xác nhận đơn hàng thành công! Nhật ký đã được ghi lại.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xác nhận thất bại: ' . $e->getMessage());
        }
    }
}