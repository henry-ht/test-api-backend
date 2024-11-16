<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SaleController;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware(['preflight'])->post("register", [AuthController::class, 'register'])->name('api.register');
Route::middleware(['preflight'])->post("start-change-password", [AuthController::class, 'startChangePw'])->name('api.startChangePw');

Route::get("public/product", [PublicController::class, 'products']);
Route::get("public/profile", [PublicController::class, 'profile']);

Route::get("category", [CategoryController::class, 'index']);
Route::get("category/group", [CategoryController::class, 'getCategory']);


Route::group(['as' => 'api.', 'middleware'  => [ 'auth:api', 'can:isAccessible']], function (){
    Route::delete("oauth/token", [AuthController::class, 'destroy']);

    Route::get("profile/notification", [ProfileController::class, 'notification']);
    Route::get("profile/notification/count-unread", [ProfileController::class, 'notificationCountUnRead']);
    Route::delete("profile/notification/{id}/readed", [ProfileController::class, 'notificationMarkAsRead']);
    Route::delete("profile/notifications/readed", [ProfileController::class, 'notificationsMarkAsRead']);

    Route::get("profile", [ProfileController::class, 'index']);
    Route::put("profile/{user}", [ProfileController::class, 'update'], [
        'parameters' => [
            'user' => 'user',
        ]
    ]);

    Route::delete("profile/{user}", [ProfileController::class, 'destroy'], [
        'parameters' => [
            'user' => 'user',
        ]
    ]);

    Route::apiResource("product", ProductController::class);
    
    Route::delete("product/{product}/image/{image}", [ProductController::class, 'destroyImage'], [
        'parameters' => [
            'product' => 'product',
            'image' => 'image'
        ]
    ]);

    Route::apiResource("sale", SaleController::class);
    Route::get("sale/{sale}/message", [SaleController::class, 'getMessage']);
    Route::post("sale/{sale}/message", [SaleController::class, 'storeMessage']);
    Route::put("sale/{sale}/end", [SaleController::class, 'endSale']);
    Route::post("sale/{sale}/reported-user", [SaleController::class, 'reportSale']);
});
