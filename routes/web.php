<?php

use App\Http\Controllers\EcollPaymentController;
use App\Http\Controllers\EcollWebhookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/payment', [PaymentController::class, 'payment'])->name('payment');
Route::get('notify', [PaymentController::class, 'notify'])->name('notify');
Route::post('return', [PaymentController::class, 'return'])->name('return');
Route::get('/recon', [PaymentController::class, 'toRecon'])->name('recon');

Route::prefix('ecoll')->group(function () {
    Route::get('/payment', [EcollPaymentController::class, 'create'])->name('ecoll.payment.create');
    Route::post('/payment/redirect', [EcollPaymentController::class, 'redirectToGateway'])->name('ecoll.payment.redirect');
    Route::get('/payment/success', [EcollPaymentController::class, 'success'])->name('ecoll.payment.success');
    Route::get('/payment/failed', [EcollPaymentController::class, 'failed'])->name('ecoll.payment.failed');
    Route::get('/payment/cancelled', [EcollPaymentController::class, 'cancelled'])->name('ecoll.payment.cancelled');
});

Route::post('/ecoll/webhook', [EcollWebhookController::class, 'handle'])->name('ecoll.webhook');
