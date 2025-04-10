<?php
use App\Http\Controllers\StaticController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BannerController;
Route::group(['prefix' => 'admin'], function () {

    Route::group(['middleware' => 'guest:admin'], function () {

        

            Route::get('password/update', [AdminLoginController::class, 'showUpdateForm'])->name('password.update.form');
            Route::post('password/update', [AdminLoginController::class, 'updatePassword'])->name('password.update');

            Route::get('/login', [StaticController::class, 'index'])->name('admin.login');
    });



    Route::group(['middleware' => 'auth:admin'], function () {

            Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

            Route::get('/logout', [AdminController::class, 'logout'])->name('admin.logout');
            Route::get('/product/index', [AdminController::class, 'productindex'])->name('admin.productindex');
    });
    
    Route::post('/authenticate', [AdminController::class, 'authenticate'])->name('authenticate');
    Route::get('/usercreate', [AdminController::class, 'usercreate'])->name('usercreate');
    Route::post('/addUser', [AdminController::class, 'addUser'])->name('addUser');

    Route::post('/login_admin', [AdminLoginController::class, 'login_admin'])->name('login_admin');
    Route::get('/User-list', [AdminController::class, 'userindex'])->name('userindex');
    Route::get('/User-Delete/{id}', [AdminController::class, 'userdelete'])->name('userdelete');
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('banners', BannerController::class);
});