<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Business\AccountManagerController;
use App\Http\Controllers\Business\DashboardController;
use App\Http\Controllers\Business\EmployeeManagerController;
use App\Http\Controllers\Business\SubscriptionController;

Route::group(['middleware' => ['auth:sanctum', 'business', 'email.verified'], 'prefix' => 'business'], function () {
    //Dashboard
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index');
    });

    //Employee Management
    Route::controller(EmployeeManagerController::class)->prefix('employees')->group(function () {
        Route::get('/', 'index');
        Route::post('/add', 'addEmployee');
        Route::post('/email-invitation', 'inviteEmployeeViaEmail');
        Route::delete('/delete/{id}', 'deleteEmployee');
        Route::post('/de-activate/{id}', 'deactivateEmployee');
        Route::post('/bulk-delete', 'deleteEmployees');
        Route::post('/upload-csv', 'uploadCSV');
    });

    //Subscription Section
    Route::controller(SubscriptionController::class)->prefix('subscription')->group(function () {
        Route::get('/plans', 'plans');
        Route::get('/history', 'paymentHistory');
        Route::post('/subscribe', 'storePlan');
    });

    //Account Manager section
    Route::controller(AccountManagerController::class)->prefix('setting')->group(function () {
       Route::get('/profile', 'profile');
        Route::post('/update-profile', 'updateProfile');
        Route::post('/update-email', 'changeEmail');
        Route::post('/update-password', 'changePassword');
        Route::delete('/delete-account', 'deleteMyAccount');
    });
});
