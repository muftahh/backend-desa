<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //membuat data user baru dengan role admin
        // menggunakan eloquent
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password')
        ]);

        
        
        $role = Role::find(1);                  //mencari role id 1
        $permissions = Permission::all();       //memanggil semua data permission yang ada
        $role->syncPermissions($permissions);   //memberikan semua akses tersebut ke daalam role id 1

        $user = User::find(1);
        $user->assignRole($role->name);         //memberikan aakses role ke user
    }
}
