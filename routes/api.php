<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservasiController;
use App\Http\Controllers\SsoTestController;
Route::prefix('v1')->middleware('reservasi.key')->group(function () {

    Route::get('/reservations',
        [ReservasiController::class,'index']);

    Route::get('/reservations/{id}',
        [ReservasiController::class,'show']);

    Route::post('/reservations/{id}/checkin',
        [ReservasiController::class,'checkin']);

    Route::put('/reservations/{id}/status',
        [ReservasiController::class,'updateStatus']);

    Route::get('/sso/login', 
        [SsoTestController::class,'login']);

    Route::get('/sso/m2m',
        [SsoTestController::class, 'm2m']);
});

