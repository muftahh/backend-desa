<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index() {
        //getProducts
        // menggunakan method when karena apabila ada sebuaah request
        // yang bernama serach maka akan dilakukan sebuah peroses
        // pencarian data berdasarkan name yang sesuai dengan isi yang di cari
        // request search
        //$paginate digunakan untuk mendapatkan 5 data terakhir 
        $products = Product::when(request()->search, function($products) {
            $products = $products->where('name', 'like', '%'. request()->search . '%');
        })->latest()->paginate(5);

        //agar url tetap berfungsi selama url pencarian
        $products->appends(['search' => request()->search]);

        //return with Api Resource
        return new ProductResource(true, 'List Data Product', $products);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'image'    => 'required|mimes:jpeg,jpg,png|max:2000',
            'title'    => 'required',
            'content'  => 'required',
            'owner'    => 'required',
            'price'    => 'required',
            'address'  => 'required',
            'phone'    => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        $product = Product::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'slug'  => Str::slug($request->title, '-'),
            'content' => $request->content,
            'owner' => $request->owner,
            'price' => $request->price,
            'address' => $request->address,
            'phone' => $request->phone,
            'user_id'     => auth()->guard('api')->user()->id,
        ]);

        if ($product) {
            return new ProductResource(true, 'Produk Berhasil Ditambahkan', $product);
        }
        return new ProductResource(false, 'Produk Gagal Ditambahkan', null);
    }

    public function show($id) {
        $product = Product::whereId($id)->first();
        if ($product) {
            return new ProductResource(true, 'Detail Produk', $product);
        }
        return new ProductResource(false, 'Detail Produk', null);
    }

    public function update(Request $request, Product $product) {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'owner' => 'required',
            'price' => 'required',
            'address' => 'required',
            'phone' => 'required'
        ]);

        if ($validator->fails()) {
            return request()->json($validator->errors(), 422);
        }

        if ($request->file('image')) {
            //delete image
            Storage::disk('local')->delete('public/products/' .basename($product->image));

            //upload imaage
            $image = $request->file('image');
            $image->storeAs('public/products', $product->hashName());

            $product->update([
                'image'    => $image->hashName(),
                'title'    => $request->title,
                'slug'     => Str::slug($request->title, '-'),
                'content'  => $request->content,
                'price'    => $request->price,
                'address'  => $request->address,
                'phone'    => $request->phone,
                'user_id'  => auth()->guard('api')->user()->id,
            ]);
        }

        $product->update([
            'title'    => $request->title,
            'slug'     => Str::slug($request->title, '-'),
            'content'  => $request->content,
            'price'    => $request->price,
            'address'  => $request->address,
            'phone'    => $request->phone,
            'user_id'  => auth()->guard('api')->user()->id,
        ]);

        if ($product) {
            return new ProductResource(true, 'Berhasil Update Produk', $product);
        }
        return new ProductResource(false, 'Gagal Update Produk', null);
    }

    public function destroy(Product $product){
        Storage::disk('local')->delete('public/products/' .basename($product->image));
        if ($product->delete()) {
            return new ProductResource(true, 'Berhasil Menghapus Produk', null);
        }
        return new ProductResource(false, 'Gagal Menghapus Produk', null);
    }
}
