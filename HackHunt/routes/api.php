<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthenticateAdmin;
use App\Http\Middleware\AuthenticateCustomer;
use App\Http\Middleware\AuthenticateResearcher;

Route::fallback(function () {
    return response()->json(['message' => 'Not Found!'], 404);
});

Route::group(['prefix' => 'admins', 'middleware' => AuthenticateAdmin::class], function () {
    Route::get('/welcome', function () {
        return view('welcome');
    });
});

Route::group(['prefix' => 'customers', 'middleware' => AuthenticateCustomer::class], function () {
    Route::get('/welcome', function () {
        return view('welcome');
    });
});


Route::group(['prefix' => 'researchers', 'middleware' => AuthenticateResearcher::class], function () {
    Route::get('/welcome', function () {
        return view('welcome');
    });
});
