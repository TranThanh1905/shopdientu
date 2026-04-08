<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        
        $query = Product::where('status', 'active')->with('category');
        
        // Lọc theo danh mục
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }
        
        // Tìm kiếm
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $products = $query->latest()->paginate(12);
        
        return view('products.index', compact('products', 'categories'));
    }
    
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        
        // Sản phẩm liên quan
        $relatedProducts = Product::where('category_id', $product->category_id)
                                  ->where('id', '!=', $product->id)
                                  ->where('status', 'active')
                                  ->take(4)
                                  ->get();
        
        return view('products.show', compact('product', 'relatedProducts'));
    }
}
