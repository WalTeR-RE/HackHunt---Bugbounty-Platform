<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$_ENV['DB_CONNECTION'] = 'mysql';
putenv('DB_CONNECTION=mysql');

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->use([
            "role" => \App\Http\Middleware\RoleMiddleware::class,
            "rate-limiter" => \App\Http\Middleware\RateLimiterMiddleware::class,
            "authenticate_admin" => \App\Http\Middleware\AuthenticateAdmin::class,
            "authenticate_customer" => \App\Http\Middleware\AuthenticateCustomer::class,
            "authenticate_researcher" => \App\Http\Middleware\AuthenticateResearcher::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
