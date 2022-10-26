<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
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
Route::middleware('api')->group(function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
        Route::get('/profile', [AuthController::class, 'getProfile'])->name('auth.getProfile');
        Route::patch('/change-pass', [AuthController::class, 'changePassword'])->name('auth.changePassword');
    });
    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/{order}', [OrderController::class, 'getOrder'])->name('orders.getOrder');
    });
});
