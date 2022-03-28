<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Resources\UserProfileCollection;
// use App\Http\Resources\ProfileCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::fallback(function () {
    return response()->json([
        'ResponseCode'  => 404,
        'status'        => False,
        'message'       => 'URL not found as you looking'
    ]);
});

/*
        |--------------------------------------------------------------------------
        | AUTHORISATION FAILED ROUTE
        |--------------------------------------------------------------------------
        */

Route::get('login', [AuthController::class, 'unauthorized_access'])->name('login');

/*
        |--------------------------------------------------------------------------
        | PRODUCT ROUTE
        |--------------------------------------------------------------------------
        */
Route::controller(ProductController::class)->group(function () {
    Route::get('get_category_list', 'getCategoryList');
    Route::get('get_product_list', 'getProductList');
    Route::post('get_search_product', 'getSearchProduct');
    Route::get('get_product_detail/{id}', 'getProductDetails');
    
});



/*
        |--------------------------------------------------------------------------
        | AUTH REGISTER LOGIN SENT LOGIN OTP ROUTE
        |--------------------------------------------------------------------------
        */

Route::controller(AuthController::class)->group(function () {
    Route::post('user_register', 'userRegister');
    Route::post('sent_register_otp', 'sentRegisterOtp');
});

/*
        |--------------------------------------------------------------------------
        | AUTHORISATION ROUTE
        |--------------------------------------------------------------------------
        */
Route::middleware('auth:api')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('get_user_profile', 'getUserProfile');
        Route::post('update_user_profile', 'updateUserProfile');
    });
    Route::controller(ProductController::class)->group(function () {
    Route::post('add_to_cart', 'addToCart');
    Route::get('delete_in_cart/{id}', 'deleteProdcutInCart');
    Route::get('get_Cart_detail', 'getCartDetail');
});
});
