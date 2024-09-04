<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index() {
        //getCategories
        // menggunakan method when karena apabila ada sebuaah request
        // yang bernama serach maka akan dilakukan sebuah peroses
        // pencarian data berdasarkan name yang sesuai dengan isi yang di cari
        // request search
        //$paginate digunakan untuk mendapatkan 5 data terakhir 
        $categories = Category::when(request()->search, function ($categories) {
            $categories = $categories->where('name', 'like', '%' . request()->search . '%');
        })->latest()->paginate(5);

        //agar url tetap berfungsi selama url pencarian
        $categories->appends(['search' => request()->search]);

        //return with Api Resource
        return new CategoryResource(true, 'List Data Kategories', $categories);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if ($category) {
            return new CategoryResource(true, 'Kategori Berhasil Dibuat', $category);
        }
        return new CategoryResource(false, 'Kategori Gagal Dibuat', null);
    }

    public function show($id) {
        $category = Category::whereId($id)->first();

        if ($category) {
            return new CategoryResource(true, 'Detail Data Kategori', $category);
        }
        return new CategoryResource(false, 'Tidak Ada Data Kategori', null);
    }

    public function update(Request $request, Category $category) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name,'.$category->id,
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);
        if ($category) {
            return new CategoryResource(true, 'Category Berhasil Diupdate!', $category);
        }

        return new CategoryResource(false, 'Ctegory Gagal Diupdate!', null);
    }

    public function destroy(Category $category){
        if ($category->delete()) {
            return new CategoryResource(true, 'Category Berhasil Dihapus!', null);
        }
        return new CategoryResource(false, 'Category Gagal Dihapus!', null);
    }

    public function all(){
        $categories = Category::latest()->get();
        return new CategoryResource(true, 'List Data Categories', $categories);
    }
}
