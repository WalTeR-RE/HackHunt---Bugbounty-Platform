<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AuthenticateAdmin;
use App\Http\Middleware\AuthenticateResearcher;
use App\Http\Middleware\AuthenticateCustomer;

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
        $middleware->appendToGroup('admins',[
            "authenticate_admin" => AuthenticateAdmin::class,
        ]);

        $middleware->appendToGroup('customers',[
            "authenticate_customer" => AuthenticateCustomer::class,
        ]);

        $middleware->appendToGroup('researchers',[
            "authenticate_researcher" => AuthenticateResearcher::class,
        ]);

        $middleware->alias([
            'authenticated' => AuthenticateResearcher::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        $middleware->use([
            \App\Http\Middleware\EnsureContentType::class,
            \App\Http\Middleware\CustomCors::class
            ]
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create()->loadEnvironmentFrom('.env');
