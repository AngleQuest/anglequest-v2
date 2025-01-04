<?php

use App\Http\Middleware\Expert;
use App\Http\Middleware\Business;
use App\Http\Middleware\Individual;
use App\Http\Middleware\IsVerifyEmail;
use Illuminate\Foundation\Application;
use App\Http\Middleware\EnsureEmailIsVerified;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            // 'super.admin' => \App\Http\Middleware\SuperAdmin::class,
            'email.verified' => EnsureEmailIsVerified::class,
            'individual' => Individual::class,
            'expert' => Expert::class,
            'business' => Business::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
