<?php

namespace App\Http\Controllers\Api\Public;

use App\Models\Photo;
use App\Http\Controllers\Controller;
use App\Http\Resources\PhotoResource;

class PhotoController extends Controller
{
    public function index() {
        $photos = Photo::latest()->paginate(9);
        return new PhotoResource(true, 'List Data Photo', $photos);
    }
}
