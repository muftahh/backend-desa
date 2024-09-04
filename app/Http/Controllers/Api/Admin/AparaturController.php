<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Aparatur;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AparatursResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AparaturController extends Controller
{
    public function index() {
        //getAparaturs
        // menggunakan method when karena apabila ada sebuaah request
        // yang bernama serach maka akan dilakukan sebuah peroses
        // pencarian data berdasarkan name yang sesuai dengan isi yang di cari
        // request search
        //$paginate digunakan untuk mendapatkan 5 data terakhir 
        $aparaturs = Aparatur::when(request()->search, function($aparaturs) {
            $aparaturs = $aparaturs->where('name', 'like', '%'. request()->search . '%');
        })->latest()->paginate(5);

        //agar url tetap berfungsi selama url pencarian
        $aparaturs->appends(['search' => request()->search]);

        //return with Api Resource
        return new AparatursResource(true, 'List Data Aparaturs', $aparaturs);
    }

    public function store (Request $request) {
        $validator = Validator::make($request->all(), [
            'image' => 'required|mimes:jpeg,jpg,png|max:2000',
            'name' => 'required',
            'role' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/aparaturs', $image->hashName());

        $aparatur = Aparatur::create([
            'image' => $image->hashName(),
            'name' => $request->name,
            'role' => $request->role
        ]);
        
        if ($aparatur) {
            return new AparatursResource(true, 'Aparatur Berhasil Ditambahkan', $aparatur);
        }
        return new AparatursResource(false, 'Aparatur Gagal Ditambahkan', null);
    }

    public function show($id) {
        $aparatur = Aparatur::whereId($id)->first();
        if ($aparatur) {
            return new AparatursResource(true, 'Aparatur Berhasil Ditampilkan', $aparatur);
        }
        return new AparatursResource(false, 'Aparatur Gagal Ditampilkan', null);
    }

    public function update(Request $request, Aparatur $aparatur) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'role' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->file('image')) {
            Storage::disk('local')->delete('public/aparaturs/'. basename($aparatur->image));

            $image = $request->file('image');
            $image->storeAs('public/aparaturs', $image->hashName());
            
            $aparatur->update([
                'image' => $image->hashName(),
                'name' => $request->name,
                'role' => $request->role
            ]);
        }

        $aparatur->update([
            'name' => $request->name,
            'role' => $request->role
        ]);

        if ($aparatur) {
            return new AparatursResource(true, 'Aparatur Berhasil Diubah', $aparatur);
        }
        return new AparatursResource(false, 'Aparatur Gagal Diubah', null);
    }

    public function destroy(Aparatur $aparatur) {
        Storage::disk('local')->delete('public/aparaturs/'. basename($aparatur->image));
        if ($aparatur->delete()) {
            return new AparatursResource(true, 'Aparatur Berhasil Dihapus', null);
        }
        return new AparatursResource(false, 'Aparatur Gagal Dihapus', null);
    }
}
