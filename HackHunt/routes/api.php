<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthenticateAdmin;
use App\Http\Middleware\AuthenticateCustomer;
use App\Http\Middleware\AuthenticateResearcher;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportCommentController;
use App\Http\Controllers\PasswordResetController;


Route::fallback(function () {
    return response()->json(['message' => 'Not Found!'], 404);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::get('logout', [AuthController::class, 'logout'])->middleware('authenticated');
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me'])->middleware('authenticated');
    
});

Route::group(['prefix' => 'admins', 'middleware' => AuthenticateAdmin::class], function () {
    Route::get('/', [AuthController::class, 'me'])->middleware(['role:admin|player|test|Test']);
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


Route::middleware('authenticated')->prefix('SubmitReport')->group(function () {
    Route::post('{report}', [ReportController::class, 'Store']);
    Route::put('{report}/triage', [ReportController::class, 'Update']);
    Route::post('{report}/publish', [ReportController::class, 'Publish']);
    Route::post('{report}/comments', [ReportCommentController::class, 'Store']);
    Route::get('{report}/comments', [ReportCommentController::class, 'Publish']);
});


Route::prefix('password')->group(function () {
    Route::post('forgot', [PasswordResetController::class, 'sendResetLinkEmail']);
    Route::post('reset', [PasswordResetController::class, 'reset']);
});