<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('payment/{id}/{code}/{userid}/{payment_id}/{device_type}', [\App\Http\Controllers\PaymentControllerWeb::class, 'select_payment']);
Route::get('send/payment/{id}/{code}/{userid}/{payment_id}/{type}', [\App\Http\Controllers\PaymentControllerWeb::class, 'initiateSession'])->name('payment.card-view');
//Route::get('applepay/payment/{id}/{code}/{userid}/{payment_id}', [ApplePayController::class, 'applepaysession'])->name('apple_pay');
Route::get('/update-session', [\App\Http\Controllers\PaymentControllerWeb::class, 'updateSession'])->name('payment.updateSession');
Route::get('/execute-payment', [\App\Http\Controllers\PaymentControllerWeb::class, 'executePayment'])->name('payment.execute');
//Route::get('apple/execute-payment', [ApplePayController::class, 'executePaymentApple'])->name('payment.executeApple');
Route::get('success/payment/{encryptedOrderId}/{kind}', [\App\Http\Controllers\PaymentControllerWeb::class, 'callback_url'])->name('callback_url');
Route::get('failed/payment/{encryptedOrderId}/{kind}', [\App\Http\Controllers\PaymentControllerWeb::class, 'failed_url'])->name('failed_url');
Route::get('successpayment', [\App\Http\Controllers\PaymentControllerWeb::class, 'success_url']);
Route::get('/privacy-policy', function () {
    return view('privacy');
})->name('privacy.policy');
Route::get('/account-deletion', function () {
    return view('deletion');
})->name('account-deletion');
