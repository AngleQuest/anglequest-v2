<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GeneralSettingController;
use App\Http\Controllers\Admin\PlanManagerController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Admin\SlaManagerController;
use App\Http\Controllers\Admin\SpecializationCategoryManagerController;

Route::group(['prefix' => 'administrator'], function () {
    /* Authenticate */
    Route::controller(AdminLoginController::class)->group(function () {
        Route::post('/login', 'login');
    });

    // Route::group(['middleware' => ['auth:admin']], function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index');
    });
    //General Setting
    Route::controller(GeneralSettingController::class)->group(function () {
        Route::prefix('admin-bank')->group(function () {
            Route::get('/', 'adminAccountDetails');
            Route::post('/update', 'updateAccountDetails');
        });
    });
    //SLA Manager
    Route::resource('sla', SlaManagerController::class);
    Route::resource('subscription-plans', PlanManagerController::class);

    //Category Manager(Specilization Category)
    Route::resource('specialization-category', SpecializationCategoryManagerController::class);

    Route::controller(SpecializationCategoryManagerController::class)->prefix('specialization')->group(function () {
        Route::get('/', 'allSpecializations');
        Route::post('/add', 'storeSpecialization');
        Route::get('/details/{id}', 'showSpecialization');
        Route::post('/update/{id}', 'updateSpecialization');
        Route::delete('/delete/{id}', 'destroySpecialization');

        Route::prefix('category')->group(function () {
            Route::get('/', 'allCategories');
            Route::get('/add', 'storeCategory');
            Route::get('/details/{id}', 'showCategory');
            Route::post('/update/{id}', 'updateCategory');
            Route::delete('/delete/{id}', 'destroyCategory');
        });
    });
    Route::post('/logout', [AdminAuthController::class, 'adminLogout']);
    // });
});
