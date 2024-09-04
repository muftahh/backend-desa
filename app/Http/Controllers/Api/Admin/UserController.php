<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index() {
        //getUsers
        // menggunakan method when karena apabila ada sebuaah request
        // yang bernama serach maka akan dilakukan sebuah peroses
        // pencarian data berdasarkan name yang sesuai dengan isi yang di cari
        // request search
        //$paginate digunakan untuk mendapatkan 5 data terakhir 
        $users = User::when(request()->search, function($users) {
            $users = $users->where('name', 'like', '%'. request()->search . '%');
        })->with('roles')->latest()->paginate(5);

        //agar url tetap berfungsi selama url pencarian
        $users->appends(['search' => request()->search]);

        //return with Api Resource
        return new UserResource(true, 'List Data Users', $users);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|unique:users',
            'password' => 'required|confirmed' 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password)
        ]);

        $user->assignRole($request->roles);
        if ($user) {
            return new UserResource(true, 'User Berhasil Disimpan', $user);
        }
        return new UserResource(false, 'User Gagal Disimpan', null);
    }

    public function show($id) {
        $user = User::with('roles')->whereId($id)->first();
        
        if ($user) {
            return new UserResource(true, 'Detail Data User', $user);
        }
        return new UserResource(false, 'Tidak Ada Data User', null);
    }

    public function update(Request $request, User $user) {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$user->id, //khusus id_user akan dikecualikan
            'password' => 'confirmed'
        ]);

        if ($request->password == "") {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }
        else {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->paassword)
            ]);
        }

        $user->syncRoles($request->roles);

        if ($user) {
            return new UserResource(true, 'User Berhasil Diupdate', $user);
        }
        return new UserResource(false, 'User Gagal Diupdate', null);
    }

    public function destroy(User $user) {
        if ($user->delete()) {
            return new UserResource(true, 'User Berhasil Dihapus', null);
        }
        return new UserResource(false, 'User Gagal Dihapus', null);
    }
}
