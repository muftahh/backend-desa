<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
    ];

    /**
     * ketika memanggil nilai return outputnya akan 
     * sesuai dengan path dari gambar tersebut
     * @return Attribute
     */
    protected function image() : Attribute {
        return Attribute::make (
            get: fn ($image) => url('/storage/sliders'. $image),
        );
    }
}
