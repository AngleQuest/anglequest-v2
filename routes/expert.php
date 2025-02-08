<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Expert\DashboardController;
use App\Http\Controllers\Expert\HubManagerController;
use App\Http\Controllers\Expert\AccountManagerController;
use App\Http\Controllers\Expert\InterviewManagerController;

Route::group(['middleware' => ['auth:sanctum', 'expert', 'email.verified'], 'prefix' => 'expert'], function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index');
    });

    //Account Manager section
    Route::controller(AccountManagerController::class)->prefix('setting')->group(function () {
        Route::get('/profile', 'profile');
        Route::post('/update-profile', 'updateProfile');
        Route::post('/update-email', 'changeEmail');
        Route::post('/update-password', 'changePassword');
        Route::delete('/delete-account', 'deleteMyAccount');
    });

    //Hub Manager
    Route::resource('hub', HubManagerController::class);

      //Appointment/Interview Section
      Route::controller(InterviewManagerController::class)->prefix('interview')->group(function () {
        Route::get('/', 'index');
        Route::post('/accept-request/{id}', 'acceptAppointment');
        Route::post('/create-guide', 'storeAppointment');
        Route::get('/feedback/{id}', 'feedback');
    });
});
