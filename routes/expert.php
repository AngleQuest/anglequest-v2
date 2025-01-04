<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;

Route::group(['middleware' => ['auth:expert','email.verified'], 'prefix' => 'expert'], function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index');
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});
