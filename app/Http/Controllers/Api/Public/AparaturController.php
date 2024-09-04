<?php

namespace App\Http\Controllers\Api\Public;

use App\Models\Aparatur;
use App\Http\Controllers\Controller;
use App\Http\Resources\AparatursResource;

class AparaturController extends Controller
{
    public function index() {
        $aparaturs = Aparatur::oldest()->get();
        return new AparatursResource(true, 'List Data Aparaturs', $aparaturs);
    }    
}
