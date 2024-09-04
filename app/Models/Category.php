<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug',
    ];


    /**
     * satu data tabel category bisa memiliki
     * banyak data post
     * @return void
     */
    public function posts(){
        return $this->hasMany(Post::class);
    }
}
