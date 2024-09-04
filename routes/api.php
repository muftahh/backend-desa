<?php

use Illuminate\Support\Facades\Route;
use Nette\Utils\Html;

//login
Route::post('/login', [App\Http\Controllers\Api\Auth\LoginController::class, 'index']);

//group route with middleware "auth" 
//artinya hanyabisa di akses ketika sudah login
Route::group(['middleware' => 'auth:api'], function() {
    //logout
    Route::post('/logout', [App\Http\Controllers\Api\Auth\LoginController::class, 'logout']); 
});

//route grup admin
Route::prefix('admin')->group(function() {
    //group route with middleware "auth" 
    //artinya hanyabisa di akses ketika sudah login
    Route::group(['middleware' => 'auth:api'], function() {
        //urlnya akan menajdi "/admin/dashboard"
        Route::get('/dashboard', App\Http\Controllers\Api\Admin\DashboardController::class); 

        //url = "/permissions"
        Route::get('/permissions', [\App\Http\Controllers\Api\Admin\PermissionController::class, 'index'])
        ->middleware('permission:permissions.index');

        //url = "/permissions/all"
        Route::get('/permissions/all', [\App\Http\Controllers\Api\Admin\PermissionController::class, 'all'])
        ->middleware('permission:permissions.index');

        //url = "/roles/all"
        Route::get('/roles/all', [\App\Http\Controllers\Api\Admin\RoleController::class], 'all')
        ->middleware('permission:roles.index');

        //url = "/roles"
        Route::apiResource('/roles', App\Http\Controllers\Api\Admin\RoleController::class)
        ->middleware('permission:roles.index|roles.store|roles.update|roles.delete');

        //url = "/user"
        Route::apiResource('/users', App\Http\Controllers\Api\Admin\UserController::class)
        ->middleware('permission:users.index|users.store|users.update|users.delete');

        //url = "/categories/all"
        Route::get('/categories/all', [\App\Http\Controllers\Api\Admin\CategoryController::class, 'all'])
        ->middleware('permission:categories.index');
 
        //url = "/categories"
        Route::apiResource('/categories', App\Http\Controllers\Api\Admin\CategoryController::class)
        ->middleware('permission:categories.index|categories.store|categories.update|categories.delete');
 
        //url = "/posts"
        Route::apiResource('/posts', App\Http\Controllers\Api\Admin\PostController::class)
        ->middleware('permission:posts.index|posts.store|posts.update|posts.delete');

        //url = "/products"
        Route::apiResource('/products', App\Http\Controllers\Api\Admin\ProductController::class)
        ->middleware('permission:products.index|products.store|products.update|products.delete');

        //url = "/pages"
        Route::apiResource('/pages', App\Http\Controllers\Api\Admin\PageController::class)
        ->middleware('permission:pages.index|pages.store|pages.update|pages.delete');

        //url = "/photos"
        Route::apiResource('/photos', App\Http\Controllers\Api\Admin\PhotoController::class, ['except' => ['create', 'show', 'update']])
        ->middleware('permission:photos.index|photos.store|photos.delete');
 
        //url = '/sliders
        Route::apiResource('/sliders', App\Http\Controllers\Api\Admin\SliderController::class, ['except' => ['create', 'show', 'update']])
        ->middleware('permission:sliders.index|sliders.store|sliders.delete');

        //url = "/aparaturs"
        Route::apiResource('/aparaturs', App\Http\Controllers\Api\Admin\AparaturController::class)
        ->middleware('permission:aparaturs.index|aparaturs.store|aparaturs.update|aparaturs.delete');
    });
});

//route grip public
Route::prefix('public')->group(function () {
    // = /public/posts
    Route::get('/posts', [App\Http\Controllers\Api\Public\PostController::class, 'index']);
    Route::get('/posts/{slug}', [App\Http\Controllers\Api\Public\PostController::class, 'show']);
    Route::get('/posts_home', [App\Http\Controllers\Api\Public\PostController::class, 'homePage']);

    // = /public/products
    Route::get('/products', [App\Http\Controllers\Api\Public\ProductController::class, 'index']);
    Route::get('/products/{slug}', [App\Http\Controllers\Api\Public\ProductController::class, 'show']);
    Route::get('/products_home', [App\Http\Controllers\Api\Public\ProductController::class, 'homePage']);

    // = /public/pages
    Route::get('/pages', [App\Http\Controllers\Api\Public\PageController::class, 'index']);
    Route::get('/pages/{slug}', [App\Http\Controllers\Api\Public\PageController::class, 'show']);
    
    // = /public/aparaturs
    Route::get('/aparaturs', [App\Http\Controllers\Api\Public\AparaturController::class, 'index']);

    // = /public/photos
    Route::get('/photos', [App\Http\Controllers\Api\Public\PhotoController::class, 'index']);

    // = /public/sliders
    Route::get('/sliders', [App\Http\Controllers\Api\Public\SliderController::class, 'index']);
});