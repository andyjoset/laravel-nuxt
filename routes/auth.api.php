<?php

use Illuminate\Http\Request;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiController;
use Laravel\Fortify\Http\Controllers\PasswordController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\ProfileInformationController;

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
    Route::post('logout/token', [ApiController::class, 'logout'])->name('api.logout');
});

Route::middleware('guest:sanctum')->group(function () {
    Route::post('login/token', [ApiController::class, 'login'])->name('api.login');
    Route::post('register', [RegisteredUserController::class, 'store'])->name('api.register');
});

// Profile Information...
if (Features::enabled(Features::updateProfileInformation())) {
    Route::put('user/profile-information', [ProfileInformationController::class, 'update'])
        ->middleware(['auth:sanctum'])
        ->name('api.user-profile-information.update');
}

// Passwords...
if (Features::enabled(Features::updatePasswords())) {
    Route::put('/user/password', [PasswordController::class, 'update'])
        ->middleware(['auth:sanctum'])
        ->name('api.user-password.update');
}

// Password Reset...
if (Features::enabled(Features::resetPasswords())) {
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest:sanctum')
        ->name('api.password.email');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->middleware('guest:sanctum')
        ->name('api.password.update');
}
