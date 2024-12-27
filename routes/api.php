<?php

use App\Models\Specialist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\Users\AuthController;
use App\Http\Controllers\Api\Users\EditProfileController;
use App\Http\Controllers\Api\Users\ResetPasswordController;
use App\Http\Controllers\Api\Users\ForgotPasswordController;

use App\Http\Controllers\Api\Specialists\SpecialistController;
use App\Http\Controllers\Api\Specialists\HomeSpecialistController;
use App\Http\Controllers\Api\Specialists\OrdersSpecialistController;

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
    Route::get('get/data/specialist/{id}', [HomeController::class,'getSpecialistData']);
    Route::get('services/specials', [HomeController::class,'services_specials']);



    Route::group(['namespace' => 'Auths'], function () {

        Route::post('user/register', [AuthController::class, 'register']);
        Route::post('user/login', [AuthController::class, 'login']);
        Route::post('user/check/code', [AuthController::class, 'check_otp']);
        Route::get('user/getUserById/{id}', [AuthController::class, 'getUserById']);
        Route::post('password/email',  [ForgotPasswordController::class, 'forget']);
        Route::post('password/reset', [ResetPasswordController::class, 'code']);
    });

    Route::group(['middleware' => 'checkUser:user-api'], function () {


        Route::get('get/orders', [OrderController::class, 'orders']);
        Route::get('get/normal-orders', [OrderController::class, 'normal_orders']);
        Route::get('get/order-services', [OrderController::class, 'orderservices']);

        Route::post('user/logout', [AuthController::class, 'logout']);
        Route::get('user/getUserData', [AuthController::class, 'getUserData']);
        Route::post('user/edit', [EditProfileController::class, 'Editprofile']);
        Route::post('user/change_password', [EditProfileController::class, 'change_password'])->middleware('checkUser:user-api');
        Route::post('user/rate', [HomeController::class, 'add_rate']);
        Route::get('user_notifications', [HomeController::class, 'userNotifications'])->name('userNotifications');
        Route::get('read_notifications', [HomeController::class, 'read_notifications'])->name('read_notifications');
        Route::post('add_order', [HomeController::class, 'add_order'])->name('add_order');
        Route::post('add_coupoun', [HomeController::class, 'add_coupoun'])->name('add_coupoun');

        Route::post('/orders/normal', [HomeController::class, 'add_order_normal'])->name('orders.normal.add');

        // Route for adding a service order
        Route::post('/orders/service', [HomeController::class, 'add_order_service'])->name('orders.service.add');

        Route::get('/specialist-offers', [HomeController::class, 'specialist_offers']);

        // Approve the offer
        Route::post('/approve-offer', [HomeController::class, 'approve_offers']);

        // Reject the offer
        Route::post('/reject-offer', [HomeController::class, 'reject_offers']);

    });
    Route::group(['namespace' => 'auth-specialist'], function () {

        Route::post('specialist/register', [SpecialistController::class, 'register']);
        Route::post('specialist/login', [SpecialistController::class, 'login']);

        Route::post('specialist/check/code', [SpecialistController::class, 'check_otp']);
        Route::get('specialist/getUserById/{id}', [AuthController::class, 'getUserById']);
        Route::post('specialist/data',  [SpecialistController::class, 'get_all_data']);
        Route::post('specialist/password/reset', [ResetPasswordController::class, 'code']);
    });
    Route::group(['middleware' => 'checkUser:specialist-api'], function () {
        // Route::post('user/logout', [AuthController::class, 'logout']);
        Route::get('specialist/getSpecialistData', [SpecialistController::class, 'getSpecialistData']);
        Route::get('specialist/services/orders', [HomeSpecialistController::class, 'getData']);
        Route::post('specialist/services/orders/by/id', [HomeSpecialistController::class, 'get_data_by_id']);
          Route::post('specialist/services/add/negotation', [HomeSpecialistController::class, 'add_negotation']);
        // Route::post('user/change_password', [EditProfileController::class, 'change_password'])->middleware('checkUser:user-api');
        // Route::post('user/rate', [HomeController::class, 'add_rate']);
        // Route::get('user_notifications', [HomeController::class, 'userNotifications'])->name('userNotifications');
        // Route::get('read_notifications', [HomeController::class, 'read_notifications'])->name('read_notifications');
        // Route::post('add_order', [HomeController::class, 'add_order'])->name('add_order');
        // Route::post('add_coupoun', [HomeController::class, 'add_coupoun'])->name('add_coupoun');
        Route::get('specialist/orders', [SpecialistController::class, 'get_all_orders_for_user']);
        Route::get('specialist_notifications', [HomeController::class, 'specialistNotifications'])->name('userNotifications');
        Route::get('specialist_read_notifications', [HomeController::class, 'specialistread_notifications'])->name('read_notifications');
        Route::post('specialist/orders/schadule', [OrdersSpecialistController::class, 'order_schadule']);
        Route::post('specialist/orders/finished', [OrdersSpecialistController::class, 'order_finished']);
        Route::post('specialist/orders/cancelled', [OrdersSpecialistController::class, 'order_cancelled']);
        Route::post('specialist/normal/orders/finished', [OrdersSpecialistController::class, 'normal_order_finished']);
        Route::post('specialist/normal/orders/cancelled', [OrdersSpecialistController::class, 'normal_order_cancelled']);
        Route::post('specialist/service/orders/finished', [OrdersSpecialistController::class, 'service_order_finished']);
        Route::post('specialist/service/orders/cancelled', [OrdersSpecialistController::class, 'service_order_cancelled']);
        Route::post('specialist/activate/account', [SpecialistController::class, 'activate_account']);
        Route::post('specialist/unactivate/account', [SpecialistController::class,'unactivate_account']);
        Route::get('specialist/get/all/finished/orders', [HomeSpecialistController::class, 'get_all_finished_orders']);
        Route::get('specialist/get/all/schadule/orders', [HomeSpecialistController::class, 'get_all_schadule_orders']);
        Route::get('specialist/get/all/cancelled/orders', [HomeSpecialistController::class, 'get_all_cancelled_orders']);
        Route::get('specialist/get/all/finished/service/orders', [HomeSpecialistController::class, 'get_all_finished_service_orders']);
        Route::get('specialist/get/all/cancelled/service/orders', [HomeSpecialistController::class, 'get_all_cancelled_service_orders']);
        Route::get('specialist/get/all/finished/normal/orders', [HomeSpecialistController::class, 'get_all_finished_normal_orders']);
        Route::get('specialist/get/all/cancelled/normal/orders', [HomeSpecialistController::class, 'get_all_cancelled_normal_orders']);
    });

});
