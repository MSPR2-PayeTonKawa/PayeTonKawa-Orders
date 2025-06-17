<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;

Route::prefix('api')->group(function () {
    Route::get('/test', [OrderController::class, 'test']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
});
