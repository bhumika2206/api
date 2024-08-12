<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\RegisterController;


Route::group(['prefix' => 'v1'], function () {
    Route::post('payments', [PaymentController::class, 'index']);
    Route::post('balance', [PaymentController::class, 'getBalance']);
    Route::post('transfer', [PaymentController::class, 'transfer']);
    Route::post('history', [PaymentController::class, 'history']);
});

Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});