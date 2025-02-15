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

        Route::prefix('withdrawal-requests')->group(function () {
            Route::get('/', 'withdrawalRequests');
            Route::post('/approve/{id}', 'approveRequest');
            Route::post('/decline/{id}', 'declineRequest');
        });
        Route::prefix('individuals')->group(function () {
            Route::get('/', 'individuals');
            Route::get('/details/{id}', 'getSingleIndividual');
        });
        Route::prefix('experts')->group(function () {
            Route::get('/', 'experts');
            Route::get('/details/{id}', 'getSingleExpert');
        });
        Route::prefix('companies')->group(function () {
            Route::get('/', 'companies');
            Route::get('/details/{id}', 'getSingleCompany');
        });
        Route::prefix('users')->group(function () {
            Route::get('/', 'users');
            Route::post('/de-activate/{id}', 'deActivateUser');
        });
    });

    //General Setting
    Route::controller(GeneralSettingController::class)->prefix('settings')->group(function () {
        Route::prefix('admin-bank')->group(function () {
            Route::get('/', 'adminAccountDetails');
            Route::post('/update', 'updateAccountDetails');
        });
        Route::get('/config-details', 'getConfigDetails');
        Route::post('/update-config', 'updateConfigDetails');
    });

    //SLA Manager
    Route::controller(SlaManagerController::class)->prefix('sla')->group(function () {
        Route::get('/', 'index');
        Route::post('/add', 'store');
        Route::get('/details/{id}', 'show');
        Route::post('/update/{id}', 'update');
        Route::delete('/delete/{id}', 'destroy');
    });

    Route::controller(PlanManagerController::class)->prefix('subscription-plans')->group(function () {
        Route::get('/', 'index');
        Route::post('/add', 'store');
        Route::get('/details/{id}', 'show');
        Route::post('/update/{id}', 'update');
        Route::delete('/delete/{id}', 'destroy');

        Route::prefix('individual')->group(function () {
            Route::get('/', 'allIndividualPlans');
            Route::post('/add', 'storeIndividualPlan');
            Route::get('/details/{id}', 'getIndividualPlan');
        });
    });

    Route::controller(SpecializationCategoryManagerController::class)->prefix('specialization')->group(function () {
        Route::get('/', 'allSpecializations');
        Route::post('/add', 'storeSpecialization');
        Route::get('/details/{id}', 'showSpecialization');
        Route::post('/update/{id}', 'updateSpecialization');
        Route::delete('/delete/{id}', 'destroySpecialization');

        Route::prefix('category')->group(function () {
            Route::get('/', 'allCategories');
            Route::post('/add', 'storeCategory');
            Route::get('/details/{id}', 'showCategory');
            Route::post('/update/{id}', 'updateCategory');
            Route::delete('/delete/{id}', 'destroyCategory');
        });
    });
    Route::post('/logout', [AdminAuthController::class, 'adminLogout']);
    // });
});
