<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    return response()->json(['message' => 'Not Found!'], 404);
});


Route::group(['prefix' => 'test'], function () {
    Route::get('/welcome', function () {
        return view('welcome');
    });
});
