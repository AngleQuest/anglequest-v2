<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Expert\DashboardController;
use App\Http\Controllers\Expert\HubManagerController;
use App\Http\Controllers\Expert\AccountManagerController;
use App\Http\Controllers\Expert\InterviewManagerController;
use App\Http\Controllers\Expert\WalletController;

Route::group(['middleware' => ['auth:sanctum', 'expert', 'email.verified'], 'prefix' => 'expert'], function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index');
    });

    //Account Manager section
    Route::controller(AccountManagerController::class)->prefix('setting')->group(function () {
        Route::get('/profile', 'profile');
        Route::post('/update-profile', 'updateProfile');
        Route::post('/create-payment-info', 'createPaymentInfo');
        Route::get('/payment-info', 'getPaymentInfo');
        Route::post('/update-email', 'changeEmail');
        Route::post('/update-password', 'changePassword');
        Route::delete('/delete-account', 'deleteMyAccount');
        Route::prefix('job-experience')->group(function () {
            Route::get('/', 'getExperiences');
            Route::post('/add', 'addExperience');
            Route::get('/view/{id}', 'getExperience');
            Route::post('/update/{id}', 'updateExperience');
            Route::delete('/delete/{id}', 'deleteExperience');
        });
    });

    //Hub Manager
    Route::resource('hub', HubManagerController::class);

    //Appointment/Interview Section
    Route::controller(InterviewManagerController::class)->prefix('interview')->group(function () {
        Route::get('/pending', 'pendingAppointments');
        Route::get('/accepted', 'acceptedAppointments');
        Route::get('/completed', 'completedAppointments');
        Route::get('/declined', 'declinedAppointments');
        Route::post('/accept-request/{id}', 'acceptAppointment');
        Route::post('/decline-request/{id}', 'rejectAppointment');
        Route::post('/create-guide', 'createGuide');
        Route::get('/view-guide', 'viewGuide');
        Route::get('/view-appointment/{id}', 'viewAppointment');
        Route::post('/feedback/{id}', 'createFeedback');
    });

    //Appointment/Interview Section
    Route::controller(WalletController::class)->prefix('wallet')->group(function () {
        Route::get('/', 'index');
        Route::post('/request-withdrawal', 'withdrawFund');
    });
});
