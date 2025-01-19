<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

use App\Http\Controllers\Auth\AccountController;
use App\Http\Controllers\Auth\CodeVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;


Route::prefix('auth')->group(function () {
    Route::controller(AccountController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
    });

    Route::controller(ForgotPasswordController::class)->prefix('forgot-password')->group(function () {
        Route::post('/verify-user', 'verifyUser');
        Route::post('/verify-code', 'verifyCode');
        Route::post('/reset-password', 'changePassword');
        Route::post('/resend-code', 'resendCode');
    });

    // Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::controller(CodeVerificationController::class)->prefix('email-verification')->group(function () {
        Route::post('/verify-code', 'verifyCode');
        Route::post('/resend-code', 'resendCode');
    });
    // });

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::controller(AccountController::class)->group(function () {
            Route::get('/logout', 'logout');
        });
    });
});
