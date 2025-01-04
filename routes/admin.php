<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Admin\SlaManagerController;
use App\Http\Controllers\Admin\SpecializationCategoryManagerController;

Route::group(['prefix' => 'administrator'], function () {
    /* Authenticate */
    Route::controller(AdminLoginController::class)->group(function () {
        Route::post('/login', 'login');
    });

    Route::group(['middleware' => ['auth:admin']], function () {
        Route::controller(DashboardController::class)->group(function () {
            Route::get('/dashboard', 'index');
        });
        //SLA Manager
        Route::resource('sla',SlaManagerController::class);

        //Category Manager(Specilization Category)
        Route::resource('specialization-category',SpecializationCategoryManagerController::class);

        Route::post('/logout', [AdminAuthController::class, 'adminLogout']);
    });
});
