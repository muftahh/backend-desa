<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * @var string
     */
    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * getPermissionArray
     * mendapatkan list permision berdasarkan user
     * yang sedang login
     * 
     * @return array
     */
    public function getPermissionArray(){
        return $this->getAllPermissions()->mapWithKeys(function($pr){
            return [$pr['name'] => true];
        });
    }

    /**
     * getJWTIdentifier
     * ketika token JWT dibuat, nilai dari token tersebut
     * akan disimpan untuk mengidentifikasi pengguna
     * 
     * @return void
     */
    public function getJWTIdentifier(){
        return $this->getKey();
    }

    /**
     * getJWTCustomClaims
     * digunakan untuk menambahkan informasi kedalam token
     * seperti role dan permission
     * 
     * @return void
     */
    public function getJWTCustomClaims(){
        return [];
    }

    /**
     * satu data tabel user memiliki banyak data post
     * @return void
     */
    public function posts(){
        return $this->hasMany(Post::class);
    }

    /**
     * satu data user memiliki banyak data product
     * @return void
     */
    public function product(){
        return $this->hasMany(Product::class);
    }

    /**
     * satu data user memiliki banya data page
     * @return void
     */
    public function pages(){
        return $this->hasMany(Page::class);
    }
    
}
