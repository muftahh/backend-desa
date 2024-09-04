<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Slider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    public function index() {
        //getSliders
        // menggunakan method when karena apabila ada sebuaah request
        // yang bernama serach maka akan dilakukan sebuah peroses
        // pencarian data berdasarkan name yang sesuai dengan isi yang di cari
        // request search
        //$paginate digunakan untuk mendapatkan 5 data terakhir 
        $sliders = Slider::when(request()->search, function($sliders) {
            $sliders = $sliders->where('name', 'like', '%'. request()->search . '%');
        })->latest()->paginate(5);

        //agar url tetap berfungsi selama url pencarian
        $sliders->appends(['search' => request()->search]);

        //return with Api Resource
        return new SliderResource(true, 'List Data Sliders', $sliders);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2000',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/sliders', $image->hashName());

        $slider = Slider::create([
            'image' => $image->hashName(),
        ]);
        if ($slider) {
            return new SliderResource(true, 'Slider Berhasil Ditambahkan', $slider);
        }
        return new SliderResource(false, 'Slider Gagal Ditambahkan', null);
    }

    public function destroy(Slider $slider) {
        Storage::disk('local')->delete('public/sliders/' . basename($slider->image));

        if ($slider->delete()) {
            return new SliderResource(true, 'Slider Berhasil Dihapus', null);
        }
        return new SliderResource(false, 'Slider Gagal Dihapus', null);
    }
}
