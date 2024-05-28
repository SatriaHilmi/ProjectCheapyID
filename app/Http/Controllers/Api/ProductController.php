<?php

namespace App\Http\Controllers\Api;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all(); // Mengambil semua data produk dari database

        return response()->json(['products' => $products], 200);
    }
}
