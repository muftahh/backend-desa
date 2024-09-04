<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'category_id', 'user_id', 'content', 'image'
    ];

    /**
     * bisa memanggil data induk melalui data child
     * @return void
     */
    public function category(){
        return $this->belongsTo(Category::class);
    }

    /**
     * bisa memanggil data induk melalui data child
     * @return void
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * ketika memanggil nilai return outputnya akan 
     * sesuai dengan path dari gambar tersebut
     * @return Attribute
     */
    protected function image() : Attribute {
        return Attribute::make (
            get: fn ($image) => url('/storage/posts'. $image),
        );
    }
}
