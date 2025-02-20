<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Guest\HomeController;

Route::group(['prefix' => 'content'], function () {
    Route::controller(HomeController::class)->group(function () {
        Route::get('/home', 'index');
        Route::get('/categories', 'allCategories');
        Route::get('/category-specializations/{id}', 'categorySpecializations');
        Route::get('/specializations', 'allSpecializations');
        Route::get('/config', 'configDetails');
       // Route::post('/cvAnalysis', 'cvAnalysis');
    });
});
