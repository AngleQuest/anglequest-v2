<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CompanyManagerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExpertManagerController;
use App\Http\Controllers\Admin\GeneralSettingController;
use App\Http\Controllers\Admin\InterviewManagerController;
use App\Http\Controllers\Admin\PlanManagerController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Admin\SlaManagerController;
use App\Http\Controllers\Admin\SpecializationCategoryManagerController;
use App\Http\Controllers\Admin\UserManagerController;

Route::group(['prefix' => 'administrator'], function () {
    /* Authenticate */
    Route::controller(AdminLoginController::class)->group(function () {
        Route::post('/login', 'login');
    });

    Route::group(['middleware' => ['auth:sanctum', 'super.admin', 'token.expiration']], function () {
        Route::controller(DashboardController::class)->group(function () {
            Route::get('/dashboard', 'index');
            //Withdrawal Request Mangager
            Route::prefix('withdrawal-requests')->group(function () {
                Route::get('/', 'withdrawalRequests');
                Route::post('/approve/{id}', 'approveRequest');
                Route::post('/decline/{id}', 'declineRequest');
            });

            Route::prefix('individuals')->group(function () {
                Route::get('/', 'individuals');
                Route::get('/details/{id}', 'getSingleIndividual');
            });
        });

        //User Mangager
        Route::controller(UserManagerController::class)->prefix('users')->group(function () {
            Route::get('/', 'users');
            Route::post('/add', 'create');
            Route::post('/de-activate/{id}', 'deActivateUser');
            Route::post('/activate/{id}', 'activateUser');
            Route::delete('/delete/{id}', 'deleteUser');
            Route::get('/details/{id}', 'details');
            Route::post('/update-password/{id}', 'updatePassword');
        });

        //Expert Mangager
        Route::controller(ExpertManagerController::class)->prefix('experts')->group(function () {
            Route::get('/', 'getExperts');
            Route::post('/add', 'create');
            Route::get('/details/{id}', 'details');
            Route::post('/update-profile/{id}', 'updateProfile');
            Route::delete('/delete/{id}', 'deleteAccount');
        });

        //General Setting
        Route::controller(GeneralSettingController::class)->prefix('settings')->group(function () {
            Route::prefix('admin-bank')->group(function () {
                Route::get('/', 'adminAccountDetails');
                Route::post('/update', 'updateAccountDetails');
            });

            Route::get('/config-details', 'getConfigDetails');
            Route::post('/update-config', 'updateConfigDetails');

            Route::controller(AdminAuthController::class)->group(function () {
                Route::get('/view-profile', 'profile');
                Route::post('/update-profile', 'changeEmail');
                Route::post('/update-password', 'changePassword');
            });
        });

        //SLA Manager
        Route::controller(SlaManagerController::class)->prefix('sla')->group(function () {
            Route::get('/', 'index');
            Route::post('/add', 'store');
            Route::get('/details/{id}', 'show');
            Route::post('/update/{id}', 'update');
            Route::delete('/delete/{id}', 'destroy');
        });
        //Subscription Mangager
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
                Route::post('/update/{id}', 'updateIndividualPlan');
                Route::delete('/delete/{id}', 'deleteIndividualPlan');
            });
        });
        //Company Mangager
        Route::controller(CompanyManagerController::class)->prefix('company')->group(function () {
            Route::get('/', 'index');
            Route::post('/add', 'create');
            Route::get('/details/{id}', 'edit');
            Route::post('/update/{id}', 'updateCompany');
            Route::delete('/delete/{id}', 'deleteCompany');
        });
        //Interviews Mangager
        Route::controller(InterviewManagerController::class)->prefix('interviews')->group(function () {
            Route::get('/pending', 'pending');
            Route::get('/active', 'active');
            Route::get('/completed', 'completed');
            Route::get('/declined', 'declined');
        });
        //specialization Mangager
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
    });
});
