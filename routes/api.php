<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PaymentController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('payments', [PaymentController::class, 'index']);

Route::post('balance', [PaymentController::class, 'getBalance']);
Route::post('transfer', [PaymentController::class, 'transfer']);
Route::post('history', [PaymentController::class, 'history']);

// Route::middleware('auth:sanctum')->group( function () {
//     // Route::resource('payments', PaymentController::class);
//     Route::get('payments', [PaymentController::class, 'index']);

// });