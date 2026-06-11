<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies (Railway runs behind a load balancer that terminates SSL)
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'cityzen.auth' => \App\Http\Middleware\CityZenAuthenticate::class,
            'cityzen.admin' => \App\Http\Middleware\CityZenAdmin::class,
            'cityzen.verified' => \App\Http\Middleware\CityZenEnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
