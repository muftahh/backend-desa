<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index() {
        //getRoles
        // menggunakan method when karena apabila ada sebuaah request
        // yang bernama serach maka akan dilakukan sebuah peroses
        // pencarian data berdasarkan name yang sesuai dengan isi yang di cari
        // request search
        //$paginate digunakan untuk mendapatkan 5 data terakhir 
        $roles = Role::when(request()->search, function($roles) {
            $roles = $roles->where('name', 'like', '%'. request()->search . '%');
        })->latest()->paginate(5);

        //agar url tetap berfungsi selama url pencarian
        $roles->appends(['search' => request()->search]);

        //return with Api Resource
        return new RoleResource(true, 'List Data Roles', $roles);
    }

    
    /**
     * create
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function Store(Request $request) {
        //validator request
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'permissions' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $role = Role::create(['name' => $request->name]);
        $role->givePermissionTo($request->permissions);

        if ($role) {
            return new RoleResource(true, 'Data Role Berhasil Di Simpan', $role);
        }
        return new RoleResource(false, 'Data Role Gagal Di Simpan', null);
    }

    public function show($id) {
        //getRole
        $role = Role::with('permissions')->findOrFail($id);

        if ($role) {
            return new RoleResource(true, 'Detail Data Role', $role);
        }
        return new RoleResource(false, 'Detail Data Role Tidak Ditemukan', null);
    }

    public function update(Request $request, Role $role) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'permissions' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update role
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        if ($role) {
            return new RoleResource(true, 'Data Role Berhasil Diupdate', $role);
        }
        return new RoleResource(false, 'Data Role Gagal Diupdate', null);
    }

    public function destroy($id) {
        //mencari role by id
        $role = Role::findOrFail($id);
        if ($role->delete()) {
            return new RoleResource(true, 'Data Role Berhasil Dihapus', null);
        }
        return new RoleResource(false, 'Data Role Gagal Dihapus', null);
    }

    public function all() {
        $roles = Role::latest()->get();
        return new RoleResource(true, 'List Data Role', $roles);
    }
}
