<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'image', 'caption',
    ];

    /**
     * ketika memanggil nilai return outputnya akan 
     * sesuai dengan path dari gambar tersebut
     * @return Attribute
     */
    protected function image() : Attribute {
        return Attribute::make (
            get: fn ($image) => url('/storage/photos'. $image),
        );
    }
}
