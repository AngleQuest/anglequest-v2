<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Individual\DashboardController;

Route::group(['middleware' => ['auth:sanctum','email.verified','individual'], 'prefix' => 'individual'], function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index');
    });

});
