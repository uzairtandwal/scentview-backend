<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        return view('admin.dashboard', compact('totalProducts', 'totalCategories'));
    }

    public function banners()
    {
        return view('admin.banners');
    }

    public function categories()
    {
        return view('admin.categories');
    }

    public function products()
    {
        return view('admin.products');
    }
}
