<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Photo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PhotoResource;
use Illuminate\Support\Facades\Validator;

class PhotoController extends Controller
{
    public function index() {
        //getPhotos
        // menggunakan method when karena apabila ada sebuaah request
        // yang bernama serach maka akan dilakukan sebuah peroses
        // pencarian data berdasarkan name yang sesuai dengan isi yang di cari
        // request search
        //$paginate digunakan untuk mendapatkan 5 data terakhir 
        $photos = Photo::when(request()->search, function($photos) {
            $photos = $photos->where('name', 'like', '%'. request()->search . '%');
        })->latest()->paginate(5);

        //agar url tetap berfungsi selama url pencarian
        $photos->appends(['search' => request()->search]);

        //return with Api Resource
        return new PhotoResource(true, 'List Data Photos', $photos);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'image'    => 'required|mimes:jpeg,jpg,png|max:2000',
            'caption'  => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/photos', $image->hashName());

        $Photo = Photo::create([
            'image' => $image->hashName(),
            'caption' => $request->caption,
        ]);
        if ($Photo) {
            return new PhotoResource(true, 'Photo Berhasil Ditambahkan', $Photo);
        }
        return new PhotoResource(false, 'Photo Gagal Ditambahkan', null);
    }

    public function destroy(Photo $Photo) {
        Storage::disk('local')->delete('public/photos/' . basename($Photo->image));

        if ($Photo->delete()) {
            return new PhotoResource(true, 'Photo Berhasil Dihapus', null);
        }
        return new PhotoResource(false, 'Photo Gagal Dihapus!', null);
    }
}
