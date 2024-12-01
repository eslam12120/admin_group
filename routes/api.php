<?php

use App\Http\Controllers\Api\Users\AuthController;
use App\Http\Controllers\Api\Users\EditProfileController;
use App\Http\Controllers\Api\Users\ForgotPasswordController;
use App\Http\Controllers\Api\Users\ResetPasswordController;
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
Route::group(['namespace' => 'Api', 'middleware' => 'checkLang'], function () {

    Route::group(['namespace' => 'Auths'], function () {

        Route::post('user/register', [AuthController::class, 'register']);
        Route::post('user/login', [AuthController::class, 'login']);
        Route::post('user/check/code', [AuthController::class, 'check_otp']);
        Route::get('user/getUserById/{id}', [AuthController::class, 'getUserById']);
        Route::post('password/email',  [ForgotPasswordController::class, 'forget']);
        Route::post('password/reset', [ResetPasswordController::class, 'code']);
      
    });

    Route::group(['middleware' => 'checkUser:user-api'], function () {

        Route::post('user/logout', [AuthController::class, 'logout']);
        Route::get('user/getUserData', [AuthController::class, 'getUserData']);
        Route::post('user/edit', [EditProfileController::class, 'Editprofile']);
        Route::post('user/change_password', [EditProfileController::class, 'change_password'])->middleware('checkUser:user-api');
 
    });

  
});

