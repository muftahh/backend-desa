<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index() {
        //getPosts
        // menggunakan method when karena apabila ada sebuaah request
        // yang bernama serach maka akan dilakukan sebuah peroses
        // pencarian data berdasarkan name yang sesuai dengan isi yang di cari
        // request search
        //$paginate digunakan untuk mendapatkan 5 data terakhir 
        $posts = Post::when(request()->search, function($posts) {
            $posts = $posts->where('name', 'like', '%'. request()->search . '%');
        })->latest()->paginate(5);

        //agar url tetap berfungsi selama url pencarian
        $posts->appends(['search' => request()->search]);

        //return with Api Resource
        return new PostResource(true, 'List Data Posts', $posts);
    }

    public function store (Request $request) {
        $validator = Validator::make($request->all(), [
            'image'         => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'title'         => 'required|unique:posts',
            'category_id'   => 'required',
            'content'       => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        $post = Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'slug' => Str::slug($request->title, '-'), //akan menggunakan judul tetapi menggunakan fitur slug untuk menghasilkan url dengan tanda - disetiap kata
            'category_id' => $request->category_id,
            'user_id' => auth()->guard('api')->user()->id, //akan mengambil dari id user yg sedang login
            'content' => $request->content
        ]);
        if ($post) {
            return new PostResource(true, 'Post Berhasil Disimpan', $post);
        }
        return new PostResource(false, 'Post Gagal Disimpan', null);
    }

    public function show ($id) {
        $post = Post::with('category')->whereId($id)->first();
        if ($post) {
            return new PostResource(true, 'Detail Post', $post);
        }
        return new PostResource(false, 'Tidak Ada Detail Post', null);
    }

    public function update (Request $request, Post $post) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:posts,title,'.$post->id,
            'category_id' => 'required',
            'content' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->file('image')) {
            //remove old image
            Storage::disk('local')->delete('public/post/'. basename($post->image));

            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());
            
            $post->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'slug' => Str::slug($request->title, '-'), //akan menggunakan judul tetapi menggunakan fitur slug untuk menghasilkan url dengan tanda - disetiap kata
                'category_id' => $request->category_id,
                'user_id' => auth()->guard('api')->user()->id, //akan mengambil dari id user yg sedang login
                'content' => $request->content
            ]);
        }
        $post->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title, '-'), //akan menggunakan judul tetapi menggunakan fitur slug untuk menghasilkan url dengan tanda - disetiap kata
            'category_id' => $request->category_id,
            'user_id' => auth()->guard('api')->user()->id, //akan mengambil dari id user yg sedang login
            'content' => $request->content
        ]);
        
        if ($post) {
            return new PostResource(true, 'Berhasil Update Post', $post);
        }
        return new PostResource(false, 'Gagal Update Post', null);
    }

    public function destroy(Post $post) {
        //remove old image
        Storage::disk('local')->delete('public/post/'. basename($post->image));

        if ($post->delete()) {
            return new PostResource(true, 'Berhasil Delete Post', null);
        }
        return new PostResource(false, 'Gagal Delete Post', null);
    }

}
