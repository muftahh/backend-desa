<?php

namespace App\Http\Controllers\Api\Public;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    public function index() {
        $posts = Post::with('user', 'category')->latest()->paginate(10);
        return new PostResource(true, 'List Data Posts', $posts);
    }

    public function show($slug) {
        $post = Post::with('user', 'category')->where('slug', $slug)->first();
        if ($post) {
            return new PostResource(true, 'Berhasil Detail Post', $post);
        }
        return new PostResource(false, 'Gagal Detail Post', $post);
    }

    public function homePage() {
        $posts = Post::with('user', 'category')->latest()->take(6)->get();
        return new PostResource(true, 'List Data Post Di HomePage', $posts);
    }
}
