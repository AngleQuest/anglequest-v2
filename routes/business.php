<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Business\AccountManagerController;
use App\Http\Controllers\Business\DashboardController;
use App\Http\Controllers\Business\EmployeeManagerController;
use App\Http\Controllers\Business\JobPostController;
use App\Http\Controllers\Business\SubscriptionController;

Route::group(['middleware' => ['auth:sanctum', 'business', 'email.verified'], 'prefix' => 'business'], function () {
    //Dashboard
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index');
        Route::prefix('shortlisted-candidates')->group(function () {
            Route::get('/', 'hiringCandidates');
            Route::get('/single/{id}', 'candidateDetails');
        });
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

    //Employee Management
    Route::controller(JobPostController::class)->group(function () {
        Route::prefix('job-post')->group(function () {
            Route::get('/', 'index');
            Route::post('/add', 'addPost');
            Route::get('/details/{id}', 'viewPost');
            Route::post('/update/{id}', 'editPost');
            Route::delete('/delete/{id}', 'deletePost');

            Route::post('/add-candidate', 'addCandidate');
            Route::post('/schedule-interview', 'deletePost');
        });
        //questionaire Management
        Route::prefix('questionaire')->group(function () {
            Route::get('/', 'allQuestionaires');
            Route::post('/add', 'addQuestionaire');
            Route::get('/details/{id}', 'viewQuestionaire');
            Route::post('/update/{id}', 'editQuestionaire');
            Route::delete('/delete/{id}', 'deleteQuestionaire');
        });
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
