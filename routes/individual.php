<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Individual\DashboardController;
use App\Http\Controllers\Individual\AccountUpdateController;
use App\Http\Controllers\Individual\HubController;

Route::group(['middleware' => ['auth:sanctum', 'email.verified', 'individual'], 'prefix' => 'individual'], function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index');
    });
    //Account and subscription section
    Route::controller(AccountUpdateController::class)->group(function () {
        Route::get('/profile', 'profile');
        Route::post('/update-profile', 'updateProfile');
        Route::get('/plans', 'getPlans');
        Route::get('/payment-history', 'paymentHistory');
        Route::post('/sla', 'subscribeToSla');
        Route::post('/create-subscription', 'createSubscription');
        Route::post('/submit-otp', 'submitOtp');
    });
    //Hub Section
    Route::controller(HubController::class)->group(function () {
        Route::get('/all-hubs', 'allHubs');
        Route::post('/join-hub', 'joinHub');
        Route::post('/leave-hub', 'leaveHub');
    });
});
