<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\Users\AuthController;
use App\Http\Controllers\Api\Users\EditProfileController;
use App\Http\Controllers\Api\Users\ResetPasswordController;
use App\Http\Controllers\Api\Users\ForgotPasswordController;

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
    Route::get('home', [HomeController::class, 'Home']);
    Route::get('search/home', [HomeController::class, 'search_service_specialist']);
    Route::get('specials', [HomeController::class, 'get_specials']);
    Route::get('search/specialist', [HomeController::class, 'search_specialist']);
    Route::get('sort/specialist', [HomeController::class, 'sort_by']);
    Route::get('filter/specialist', [HomeController::class, 'filter_by']);

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
        Route::post('user/rate', [HomeController::class, 'add_rate']);
        Route::get('user_notifications', [HomeController::class, 'userNotifications'])->name('userNotifications');
        Route::get('read_notifications', [HomeController::class, 'read_notifications'])->name('read_notifications');
        Route::post('add_order', [HomeController::class, 'add_order'])->name('add_order');
        Route::post('add_coupoun', [HomeController::class, 'add_coupoun'])->name('add_coupoun');
    });
});
