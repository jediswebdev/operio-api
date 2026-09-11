<?php

use App\Http\Controllers\Api\Auth\CurrentUserController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutUserController;
use App\Http\Controllers\Api\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    Route::prefix('auth')->group(function () {

        // Registration & Login
        Route::post('/register', RegisterController::class)
            ->name('register');

        Route::post('/login', LoginController::class)
            ->name('login');

        /*
        |--------------------------------------------------------------------------
        | Email Verification
        |--------------------------------------------------------------------------
        */

        Route::controller(EmailVerificationController::class)->group(function () {

            Route::get('/email/verify/{id}/{hash}', 'verify')
                ->middleware('signed')
                ->name('verification.verify');

            Route::post('/email/verification-notification', 'resend')
                ->middleware('throttle:6,1')
                ->name('verification.resend');

            Route::get('/email/notice', 'notice')
                ->name('verification.notice');
        });

        /*
        |--------------------------------------------------------------------------
        | Password Reset
        |--------------------------------------------------------------------------
        */

        // User is NOT required to be authenticated here.
        Route::post('/forgot-password', [ForgotPasswordController::class, 'send'])
            ->middleware('throttle:6,1')
            ->name('password.email');

        Route::post('/reset-password/{token}', [ForgotPasswordController::class, 'reset'])
            ->middleware('throttle:6,1')
            ->name('password.reset');

        /*
        |--------------------------------------------------------------------------
        | Protected Authentication Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware('auth:sanctum')->group(function () {

            Route::get('/user', CurrentUserController::class);

            Route::post('/logout', LogoutUserController::class);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Protected API
    |--------------------------------------------------------------------------
    */

    Route::middleware(['auth:sanctum', 'verified'])->group(function () {

        // Protected Operio API routes will go here.

    });
});
