<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $products = Product::where('status', 'active')
                          ->latest()
                          ->take(8)
                          ->get();
        
        return view('home', compact('categories', 'products'));
    }
}
