<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)->latest()->get();
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return response()->view('sitemap', compact('products', 'categories'))
            ->header('Content-Type', 'text/xml');
    }
}
