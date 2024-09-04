<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Page;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function index() {
        //getPages
        // menggunakan method when karena apabila ada sebuaah request
        // yang bernama serach maka akan dilakukan sebuah peroses
        // pencarian data berdasarkan name yang sesuai dengan isi yang di cari
        // request search
        //$paginate digunakan untuk mendapatkan 5 data terakhir 
        $pages = Page::when(request()->search, function ($pages) {
            $pages = $pages->where('title', 'like', '%' . request()->search . '%');
        })->latest()->paginate(5);

        //agar url tetap berfungsi selama url pencarian
        $pages->appends(['search' => request()->search]);

        //return with Api Resource
        return new PageResource(true, 'List Data Page', $pages);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $page = Page::create([
            'title'     => $request->title,
            'slug'      => Str::slug($request->title),
            'content'   => $request->content,
            'user_id'   => auth()->guard('api')->user()->id
        ]);
        if ($page) {
            return new PageResource(true, 'Berhasil Menambahkan Page', $page);
        }
        return new PageResource(false, 'Gagal Menambahkan Page', null);
    }

    public function show($id) {
        $page = Page::whereId($id)->first();
        if ($page) {
            return new PageResource(true, 'Berhasil Menampilkan Page', $page);
        }
        return new PageResource(false, 'Gagal Menampilkan Page', null);
    }

    public function update(Request $request, Page $page) {
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $page->update([
            'title'     => $request->title,
            'slug'      => Str::slug($request->title),
            'content'   => $request->content,
            'user_id'   => auth()->guard('api')->user()->id
        ]);
        if ($page) {
            return new PageResource(true, 'Berhasil Menupdate Page', $page);
        }
        return new PageResource(false, 'Gagal Menupdate Page', null);
    }

    public function destroy(Page $page) {
        if ($page->delete()) {
            return new PageResource(true, 'Berhasil Menhapus Page', null);
        }
        return new PageResource(false, 'Gagal Menhapus Page', null);
    }
}
