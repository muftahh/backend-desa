<?php

namespace App\Http\Controllers\Api\Public;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index() {
        $products = Product::latest()->paginate(9);
        return new ProductResource(true, 'List Data Product', $products);
    }

    public function show($slug) {
        $product = Product::where('slug', $slug)->first();
        if ($product) {
            return new ProductResource(true, 'Detail Data Product', $product);
        }
        return new ProductResource(false, 'Gagal Detail Data Product', null);
    }

    public function homePage() {
        $product = Product::latest()->take(6)->get();
        return new ProductResource(true, 'Product HomePage', $product);
    }
}
