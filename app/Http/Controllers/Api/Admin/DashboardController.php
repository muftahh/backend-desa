<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Product;
use App\Models\Aparatur;
use App\Models\Category;

class DashboardController extends Controller{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request){
        //count tabel
        $categories = Category::count();
        $aparaturs = Aparatur::count();
        $products = Product::count();
        $posts  = Post::count();

        return response()->json([
            'success' => true,
            'message' => 'list data on dashboard',
            'data' => [
                'categories' => $categories,
                'aparaturs' => $aparaturs,
                'products' => $products,
                'posts' => $posts,
            ]
        ]);
    }
}
