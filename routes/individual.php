<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Individual\HubController;
use App\Http\Controllers\Individual\DashboardController;
use App\Http\Controllers\Individual\AppointmentController;
use App\Http\Controllers\Individual\AccountUpdateController;
use App\Http\Controllers\Individual\SupportRequestController;

Route::group(['middleware' => ['auth:sanctum', 'email.verified', 'individual'], 'prefix' => 'individual'], function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index');
    });
    //Account and subscription section
    Route::controller(AccountUpdateController::class)->group(function () {
        Route::get('/profile', 'profile');
        Route::post('/update-profile', 'updateProfile');
        Route::post('/update-mode', 'updateMode');
        Route::post('/update-email', 'changeEmail');
        Route::post('/update-password', 'changePassword');
        Route::post('/delete-account', 'deleteMyAccount');
        Route::post('/block-account', 'forgetMyAccount');
        Route::get('/plans', 'getPlans');
        Route::get('/payment-history', 'paymentHistory');
        Route::post('/create-subscription', 'createSubscription');
        Route::post('/submit-otp', 'submitOtp');
    });

    //Hub Section
    Route::controller(HubController::class)->group(function () {
        Route::get('/all-hubs', 'allHubs');
        Route::post('/join-hub/{id}', 'joinHub');
        Route::post('/leave-hub/{id}', 'leaveHub');
    });

    //Appointment Section
    Route::controller(AppointmentController::class)->prefix('appointments')->group(function () {
        Route::get('/declined', 'declinedAppointments');
        Route::get('/completed', 'completedAppointments');
        Route::get('/pending', 'pendingAppointments');
        Route::get('/accepted', 'acceptedAppointments');
        Route::post('/book-appointment', 'bookAppointment');
        Route::post('/rate-appointment', 'rateAppointment');
        Route::post('/merge-appointment', 'mergeAppointment');
        Route::get('/feedback/{id}', 'feedback');
    });

    //Support Request Section
    Route::controller(SupportRequestController::class)->prefix('support-request')->group(function () {
        Route::get('/declined', 'declinedAppointments');
        Route::get('/completed', 'completedAppointments');
        Route::post('/book-appointment', 'bookAppointment');
        Route::post('/merge-appointment', 'mergeAppointment');
        Route::get('/feedback/{id}', 'feedback');
    });
});
