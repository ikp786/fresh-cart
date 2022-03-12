<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryBoyController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'admin'], function () {

    Route::get('/', function () {
        return view('admin.login');
    })->name('admin');
    Route::post('login', [AdminController::class, 'login'])->name('admin.login');
    Route::group(['middleware'  => 'admin'], function () {

        /*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/
        Route::controller(DashboardController::class)->group(function () {
            Route::get('dashboard', 'index')->name('admin.dashboard');
            Route::get('logout', 'logout')->name('admin.logout');
            Route::group(['prefix' => 'users'], function () {
                Route::get('index', 'userList')->name('admin.users.index');
            });
        });
        /*
|--------------------------------------------------------------------------
| CATEGORIES CREATE STORE DELETE UPDATE EDIT 
|--------------------------------------------------------------------------
*/
        Route::controller(CategoryController::class)->group(function () {
            Route::group(['prefix' => 'categories'], function () {
                Route::get('index', 'index')->name('admin.categories.index');
                Route::get('create', 'create')->name('admin.categories.create');
                Route::post('store', 'store')->name('admin.categories.store');
                Route::get('edit/{id}', 'edit')->name('admin.categories.edit');
                Route::PATCH('update/{id}', 'update')->name('admin.categories.update');
                Route::delete('destroy{id}', 'destroy')->name('admin.categories.destroy');
            });
        });

        /*
|--------------------------------------------------------------------------
| PRODUCTSL CREATE STORE DELETE UPDATE EDIT 
|--------------------------------------------------------------------------
*/
        Route::controller(ProductController::class)->group(function () {
            Route::group(['prefix' => 'products'], function () {
                Route::get('index', 'index')->name('admin.products.index');
                Route::get('create', 'create')->name('admin.products.create');
                Route::post('store', 'store')->name('admin.products.store');
                Route::get('edit/{id}', 'edit')->name('admin.products.edit');
                Route::PATCH('update/{id}', 'update')->name('admin.products.update');
                Route::delete('destroy{id}', 'destroy')->name('admin.products.destroy');
            });
        });


        /*
|--------------------------------------------------------------------------
| DELIVERY BOY CREATE STORE DELETE UPDATE EDIT 
|--------------------------------------------------------------------------
*/
        Route::controller(DeliveryBoyController::class)->group(function () {
            Route::group(['prefix' => 'delivery-boys'], function () {
                Route::get('index', 'index')->name('admin.delivery-boys.index');
                Route::get('create', 'create')->name('admin.delivery-boys.create');
                Route::post('store', 'store')->name('admin.delivery-boys.store');
                Route::get('edit/{id}', 'edit')->name('admin.delivery-boys.edit');
                Route::PATCH('update/{id}', 'update')->name('admin.delivery-boys.update');
                Route::delete('destroy{id}', 'destroy')->name('admin.delivery-boys.destroy');
            });
        });
    });
});
