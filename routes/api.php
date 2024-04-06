<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LikeCommentController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('auth/')->group(function(){
    Route::post('register', [AuthController::class,'register']);
    Route::post('login', [LoginController::class,'login']);
    Route::get('send-mail', [LoginController::class, 'testMail']);
    Route::post('forgot-password', [LoginController::class, 'forgetPasswordReset']);
    Route::post('reset-password', [LoginController::class, 'verifyAndChangePassword']);
});


Route::middleware('auth:sanctum')->group(function(){
    Route::post('logout',[LoginController::class, 'logout']);
    Route::get('get-profile', [LoginController::class, 'getProfile']);
    Route::post('change-password', [LoginController::class, 'changePassword']);
    Route::post('update-profile', [LoginController::class, 'updateProfile']);
});

Route::middleware('auth:sanctum')->prefix('user')->group(function(){
    Route::get('posts/publicos', [PostController::class, 'publicPosts']);
    Route::apiResource('posts', PostController::class);

    Route::controller(LikeCommentController::class)->group(function(){
        Route::post('comments', 'PostComment');
        Route::get('like/{postId}', 'LikeUnlike');
    });
});



