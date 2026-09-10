<?php

use App\Http\Controllers\Api\Auth\CurrentUserController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutUserController;
use App\Http\Controllers\Api\Auth\RegisterController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        // Authentication Routes
        Route::post('/register', RegisterController::class)->name('register');
        Route::post('/login', LoginController::class)->name('login');


        //Email verification routes
        Route::controller(EmailVerificationController::class)->group(function () {
            Route::get('/email/verify/{id}/{hash}', 'verify')->middleware('signed')->name('verification.verify');
            Route::post('/email/verification-notification', 'verify')->middleware('throttle:6,1')->name('verification.resend');
            Route::get('/email/notice', 'notice')->name('verification.notice');
        });


        // Password reset routes
        Route::controller(ForgotPasswordController::class)->group(function() {
            Route::post('/forgot-password', 'send')->middleware('throttle:5,1');
            Route::post('/reset-password/{token}', 'reset')->middleware('throttle:5,1')->name('password.reset');
        });


        // Protected
        Route::get('/user', CurrentUserController::class)->middleware('auth:sanctum');
        Route::post('/logout', LogoutUserController::class)->middleware('auth:sanctum');
    });



    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        // Protected api routes
    });
});
