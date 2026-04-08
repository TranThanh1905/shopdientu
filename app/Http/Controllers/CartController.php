<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Hiển thị giỏ hàng
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return view('cart.index', [
                'cart' => [],
                'total' => 0,
                'discount' => 0,
                'final_total' => 0,
            ]);
        }

        $cartData = $this->calculateCart($cart);
        
        return view('cart.index', $cartData);
    }
    
    /**
     * Thêm sản phẩm vào giỏ
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $product = Product::with('inventory')->findOrFail($request->product_id);
        
        // Kiểm tra trạng thái sản phẩm
        if ($product->status !== 'active') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm này hiện không còn bán!'
                ], 400);
            }
            return redirect()->back()->with('error', 'Sản phẩm này hiện không còn bán!');
        }

        // Kiểm tra tồn kho
        $stock = $product->inventory ? $product->inventory->quantity_in_stock : 0;
        if ($stock <= 0) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm đã hết hàng!'
                ], 400);
            }
            return redirect()->back()->with('error', 'Sản phẩm đã hết hàng!');
        }

        $cart = session()->get('cart', []);
        $quantity = $request->quantity ?? 1;
        
        // Kiểm tra số lượng trong giỏ + số lượng thêm
        $currentQty = isset($cart[$product->id]) ? $cart[$product->id]['quantity'] : 0;
        $newTotalQty = $currentQty + $quantity;

        if ($newTotalQty > $stock) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Chỉ còn {$stock} sản phẩm trong kho!"
                ], 400);
            }
            return redirect()->back()->with('error', "Chỉ còn {$stock} sản phẩm trong kho!");
        }
        
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->selling_price,
                'discount_percent' => $product->discount_percent,
                'final_price' => $product->final_price,
                'image' => $product->image,
                'quantity' => $quantity
            ];
        }
        
        session()->put('cart', $cart);
        
        // Nếu là AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm "' . $product->name . '" vào giỏ hàng!',
                'cart_count' => count($cart),
                'product_name' => $product->name
            ]);
        }
        
        return redirect()->back()->with('success', 'Đã thêm vào giỏ hàng!');
    }

    /**
     * Cập nhật số lượng sản phẩm
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0'
        ]);

        $cart = session()->get('cart', []);
        $productId = $request->product_id;
        $quantity = $request->quantity;

        if (!isset($cart[$productId])) {
            return redirect()->route('cart.index')->with('error', 'Sản phẩm không có trong giỏ hàng!');
        }

        // Kiểm tra tồn kho
        $product = Product::with('inventory')->find($productId);
        $stock = $product->inventory ? $product->inventory->quantity_in_stock : 0;

        if ($quantity > $stock) {
            return redirect()->route('cart.index')
                ->with('error', "Sản phẩm '{$product->name}' chỉ còn {$stock} sản phẩm trong kho!");
        }

        if ($quantity > 0) {
            $cart[$productId]['quantity'] = $quantity;
        } else {
            unset($cart[$productId]);
        }
        
        session()->put('cart', $cart);
        
        return redirect()->route('cart.index');
    }
    
    /**
     * Xóa sản phẩm khỏi giỏ
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$id])) {
            $productName = $cart[$id]['name'];
            unset($cart[$id]);
            session()->put('cart', $cart);
            
            return redirect()->route('cart.index')
                ->with('success', "Đã xóa '{$productName}' khỏi giỏ hàng!");
        }
        
        return redirect()->route('cart.index');
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Đã xóa toàn bộ giỏ hàng!');
    }
    
    /**
     * Tính toán giỏ hàng với giá mới nhất từ database
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
        $warnings = [];

        foreach ($cart as $productId => $item) {
            $product = $products->get($productId);

            if (!$product) {
                $warnings[] = "Sản phẩm '{$item['name']}' không tồn tại!";
                continue;
            }

            if ($product->status !== 'active') {
                $warnings[] = "Sản phẩm '{$product->name}' không còn bán!";
                continue;
            }

            $stock = $product->inventory ? $product->inventory->quantity_in_stock : 0;
            
            if ($stock < $item['quantity']) {
                $warnings[] = "Sản phẩm '{$product->name}' chỉ còn {$stock} sản phẩm!";
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
                'stock' => $stock,
                'out_of_stock' => $stock < $item['quantity'],
            ];

            $totalAmount += $sellingPrice * $item['quantity'];
            $totalDiscount += $discountAmount;
        }

        return [
            'cart' => $items,
            'total' => $totalAmount,
            'discount' => $totalDiscount,
            'final_total' => $totalAmount - $totalDiscount,
            'warnings' => $warnings,
        ];
    }
}