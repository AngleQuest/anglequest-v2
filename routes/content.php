<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Guest\HomeController;

Route::group(['prefix' => 'content'], function () {
    Route::controller(HomeController::class)->group(function () {
        Route::get('/home', 'index');
    });
});
