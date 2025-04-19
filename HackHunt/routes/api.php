<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthenticateAdmin;
use App\Http\Middleware\AuthenticateCustomer;
use App\Http\Middleware\AuthenticateResearcher;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportCommentController;

Route::fallback(function () {
    return response()->json(['message' => 'Not Found!'], 404);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::get('logout', [AuthController::class, 'logout'])->middleware('authenticated');
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me'])->middleware('authenticated');
    Route::post('forget-password', [AuthController::class, 'ForgetPassword']);
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


    Route::post('SubmitReport/{report}', [ReportController::class, 'Store']);
    Route::put('SubmitReport/{report}/triage', [ReportController::class, 'Update']);
    Route::post('SubmitReport/{report}/publish', [ReportController::class, 'publish']);

