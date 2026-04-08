<?php
// app/Http/Controllers/OrderController.php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Trang thanh toán
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Giỏ hàng trống!');
        }

        // Kiểm tra tồn kho cho tất cả sản phẩm
        $outOfStock = [];
        foreach ($cart as $item) {
            $product = Product::with('inventory')->find($item['id']);
            
            if (!$product || $product->status !== 'active') {
                $outOfStock[] = $item['name'] . ' (sản phẩm không còn bán)';
                continue;
            }

            $stock = $product->inventory ? $product->inventory->quantity_in_stock : 0;
            
            if ($stock < $item['quantity']) {
                $outOfStock[] = $item['name'] . " (còn {$stock} sản phẩm)";
            }
        }

        if (!empty($outOfStock)) {
            return redirect()->route('cart.index')
                ->with('error', 'Một số sản phẩm không đủ hàng: ' . implode(', ', $outOfStock));
        }

        $cartData = $this->calculateCart($cart);
        
        return view('cart.checkout', $cartData);
    }

    /**
     * Xử lý đặt hàng
     */
    public function placeOrder(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Giỏ hàng trống!');
        }

        // Validate dữ liệu
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'note' => 'nullable|string|max:500'
        ], [
            'fullname.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'address.required' => 'Vui lòng nhập địa chỉ giao hàng',
        ]);

        DB::beginTransaction();
        
        try {
            // Lấy thông tin sản phẩm mới nhất từ database
            $products = Product::with('inventory')
                ->whereIn('id', array_keys($cart))
                ->get()
                ->keyBy('id');

            // Kiểm tra lại tồn kho và tính toán chi tiết
            $orderItems = [];
            $totalAmount = 0;
            $totalDiscount = 0;

            foreach ($cart as $productId => $item) {
                $product = $products->get($productId);

                // Kiểm tra sản phẩm tồn tại và còn bán
                if (!$product || $product->status !== 'active') {
                    throw new \Exception("Sản phẩm '{$item['name']}' không còn bán!");
                }

                // Kiểm tra tồn kho
                $stock = $product->inventory ? $product->inventory->quantity_in_stock : 0;
                if ($stock < $item['quantity']) {
                    throw new \Exception("Sản phẩm '{$product->name}' chỉ còn {$stock} sản phẩm!");
                }

                // Tính toán giá (lấy từ database để đảm bảo chính xác)
                $sellingPrice = $product->selling_price;
                $discountPercent = $product->discount_percent;
                $finalPrice = $sellingPrice * (1 - $discountPercent / 100);
                $discountAmount = ($sellingPrice - $finalPrice) * $item['quantity'];

                $orderItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'purchase_price' => $product->purchase_price,
                    'selling_price' => $sellingPrice,
                    'discount_percent' => $discountPercent,
                    'final_price' => $finalPrice,
                    'subtotal' => $finalPrice * $item['quantity'],
                ];

                $totalAmount += $sellingPrice * $item['quantity'];
                $totalDiscount += $discountAmount;
            }

            $finalAmount = $totalAmount - $totalDiscount;

            // Lấy mã khách hàng nếu là user đã đăng nhập
            $customerCode = null;
            if (Auth::check()) {
                $user = Auth::user();
                $customerCode = $user->customer_code;
            }

            // ========== CÁCH SỬA MỚI - TRÁNH DUPLICATE ==========
            
            // Tạo đơn hàng với order_code tạm thời
            $order = Order::create([
                'user_id' => Auth::id(),
                'customer_code' => $customerCode,
                'fullname' => $validated['fullname'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'note' => $validated['note'] ?? null,
                'total_amount' => $totalAmount,
                'discount_amount' => $totalDiscount,
                'final_amount' => $finalAmount,
                'status' => 'pending',
                'order_code' => 'TEMP-' // Tạm thời để tránh null
            ]);

            // Tạo mã đơn hàng dựa trên ID (đảm bảo unique)
            $orderCode = 'ORD-' . now()->format('Ymd') . '-' . str_pad($order->id, 4, '0', STR_PAD_LEFT);
            
            // Cập nhật lại order_code
            $order->update(['order_code' => $orderCode]);

            // ========== KẾT THÚC PHẦN SỬA ==========

            // Tạo chi tiết đơn hàng
            foreach ($orderItems as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                    'selling_price' => $item['selling_price'],
                    'discount_percent' => $item['discount_percent'],
                    'final_price' => $item['final_price'],
                ]);
            }

            DB::commit();

            // Xóa giỏ hàng
            session()->forget('cart');
            session()->put('order_id', $order->id);

            return redirect()->route('order.success');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Đặt hàng thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Trang đặt hàng thành công
     */
    public function success()
    {
        $orderId = session()->get('order_id');
        
        if (!$orderId) {
            return redirect()->route('home');
        }

        $order = Order::with('orderDetails.product')->find($orderId);

        if (!$order) {
            return redirect()->route('home');
        }

        session()->forget('order_id');
        
        return view('cart.success', compact('order'));
    }

    /**
     * Xem chi tiết đơn hàng (cho user đã đăng nhập)
     */
    public function show($id)
    {
        $order = Order::with(['orderDetails.product.category'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('orders.show', compact('order'));
    }

    /**
     * Danh sách đơn hàng của user
     */
    public function index(Request $request)
    {
        $query = Order::with('orderDetails.product')
            ->where('user_id', Auth::id());

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Hủy đơn hàng (chỉ khi đang pending)
     */
    public function cancel($id)
    {
        DB::beginTransaction();
        try {
            $order = Order::where('id', $id)
                ->where('user_id', Auth::id())
                ->where('status', 'pending')
                ->firstOrFail();

            $order->update([
                'status' => 'cancelled',
                'note' => ($order->note ?? '') . "\n" . now()->format('d/m/Y H:i') . ": Khách hàng hủy đơn"
            ]);

            DB::commit();

            return redirect()->route('orders.show', $id)
                ->with('success', 'Đã hủy đơn hàng thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Không thể hủy đơn hàng: ' . $e->getMessage());
        }
    }

    /**
     * Tính toán giỏ hàng với giá mới nhất
     */
    private function calculateCart($cart)
    {
        $products = Product::with('inventory')
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $items = [];
        $totalAmount = 0;
        $totalDiscount = 0;

        foreach ($cart as $productId => $item) {
            $product = $products->get($productId);

            if (!$product) {
                continue;
            }

            $sellingPrice = $product->selling_price;
            $discountPercent = $product->discount_percent;
            $finalPrice = $sellingPrice * (1 - $discountPercent / 100);
            $subtotal = $finalPrice * $item['quantity'];
            $discountAmount = ($sellingPrice - $finalPrice) * $item['quantity'];

            $items[] = [
                'id' => $product->id,
                'name' => $product->name,
                'image' => $product->image,
                'quantity' => $item['quantity'],
                'selling_price' => $sellingPrice,
                'discount_percent' => $discountPercent,
                'final_price' => $finalPrice,
                'discount_amount' => $discountAmount,
                'subtotal' => $subtotal,
                'stock' => $product->inventory ? $product->inventory->quantity_in_stock : 0,
            ];

            $totalAmount += $sellingPrice * $item['quantity'];
            $totalDiscount += $discountAmount;
        }

        return [
            'cart' => $items,
            'total' => $totalAmount,
            'discount' => $totalDiscount,
            'final_total' => $totalAmount - $totalDiscount,
        ];
    }
}