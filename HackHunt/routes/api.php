<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthenticateAdmin;
use App\Http\Middleware\AuthenticateCustomer;
use App\Http\Middleware\AuthenticateResearcher;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportCommentController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\FriendController;

Route::fallback(function () {
    return response()->json(['message' => 'Not Found!'], 404);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::get('logout', [AuthController::class, 'logout'])->middleware('authenticated');
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me'])->middleware('authenticated');

    Route::prefix('password')->group(function () {
        Route::post('forgot', [PasswordResetController::class, 'sendResetLinkEmail']);
        Route::post('reset', [PasswordResetController::class, 'reset']);
    });
});

Route::group(['prefix' => 'admins', 'middleware' => AuthenticateAdmin::class], function () {
    Route::post('/createSuperUser', [AdminController::class, 'createSuperUser']);
    Route::put('/editSuperUser/{uuid}', [AdminController::class, 'updateSuperUser']);
    Route::delete('/removeSuperUser/{uuid}', [AdminController::class, 'destroySuperUser']);
});

Route::group(['prefix' => 'customers', 'middleware' => AuthenticateCustomer::class], function () {
    Route::post('/createProgram', [ProgramController::class, 'store']);
    Route::put('/editPrograms/{uuid}', [ProgramController::class, 'update']);
    Route::delete('/removePrograms/{uuid}', [ProgramController::class, 'destroy']);
    Route::get('/programs/{program_id}/reports', [ReportController::class, 'getProgramReports']);
    Route::put('/programs/{program_id}/invite', [ProgramController::class, 'inviteResearcher']);
    Route::delete('/programs/{program_id}/remove', [ProgramController::class, 'removeResearcher']);
    
});


Route::group(['prefix' => 'researchers', 'middleware' => AuthenticateResearcher::class], function () {
    Route::middleware('authenticated')->prefix('reports')->group(function () {
        Route::post('{program}/Submit', [ReportController::class, 'store']);
        Route::put('{report}/triage', [ReportController::class, 'Update'])->middleware(AuthenticateCustomer::class) ;
        Route::post('{report}/publish', [ReportController::class, 'Publish'])->middleware(AuthenticateCustomer::class);
        Route::post('{report}/comments', [ReportCommentController::class, 'Store']);
        Route::get('{report}/comments', [ReportCommentController::class, 'restore']);
        Route::get('{report}', [ReportController::class, 'getReportData']);
    });

    Route::put('/programs/{uuid}/invite', [ProgramController::class, 'acceptrejectInvite']);
    Route::delete('/programs/{uuid}/leave', [ProgramController::class, 'leaveProgram']);
    Route::get('/invites', [ProgramController::class, 'getInvites']);
    Route::get('/crowdstream', [ReportController::class, 'getCrowdstream']);
    Route::get('/programs/{uuid}', [ProgramController::class, 'getProgramData']);
    Route::get('/programs/{uuid}/hallofFame', [ReportController::class, 'getProgramHallOfFame']);
    Route::get('/hallOfFame/{nickname}', [ReportController::class, 'getHallOfFame']);
    Route::get('/programs', [ProgramController::class, 'index']);


    Route::group(['prefix' => 'friends'], function () {
        Route::get('/', [FriendController::class, 'getFriends']);
        Route::put('/add', [FriendController::class, 'addFriend']);
        Route::delete('/remove', [FriendController::class, 'removeFriend']);
        Route::get('/requests', [FriendController::class, 'getFriendRequests']);
        Route::put('/accept', [FriendController::class, 'acceptFriendRequest']);
        Route::put('/reject', [FriendController::class, 'rejectFriendRequest']);
        Route::post('/block', [FriendController::class, 'blockUser']);
        Route::post('/unblock', [FriendController::class, 'unblockUser']);
        Route::get('/blocked', [FriendController::class, 'getBlockedUsers']);
    });

    Route::post('/profile', [AuthController::class, 'updateProfile']);
});

Route::get('/files/{filename}', function ($filename) {
    $path = storage_path('app/public/attachments/' . $filename);

    if (!file_exists($path)) {
        return response()->json(['message' => 'File not found.'], 404);
    }

    return response()->download($path);
});







Route::prefix('test-route')->group(function(){
    Route::get('test', [AuthController::class, 'test']);
});
