<?php

namespace App\Http\Controllers\Api\Public;

use App\Models\Page;
use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;

class PageController extends Controller
{
    public function index() {
        $pages = Page::oldest()->get();
        return new PageResource(true, 'List Data Pages', $pages);
    }

    public function show($slug) {
        $page = Page::where('slug', $slug)->first();
        if ($page) {
            return new PageResource(true, 'Detail Data Pages', $page);
        }
        return new PageResource(false, 'Gagal Detail Data Pages', null);
    }
}
