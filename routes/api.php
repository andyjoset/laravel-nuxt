<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\AvatarController;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [UserController::class, 'current'])->name('user.current');
    Route::put('user/avatar', [AvatarController::class, 'update'])->name('user.avatar.update');
    Route::delete('user/avatar', [AvatarController::class, 'destroy'])->name('user.avatar.destroy');
});

Route::middleware('guest:sanctum')->group(function () {
});

// Use fortify routes in api context for stateless apps,
// comment or remove them if your app don't need stateless auth routes
require __DIR__.'/auth.api.php';
