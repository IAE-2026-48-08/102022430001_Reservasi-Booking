<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservasiController;

Route::prefix('v1')->middleware('reservasi.key')->group(function () {

    Route::get('/reservasis',
        [ReservasiController::class,'index']);

    Route::get('/reservasis/{id}',
        [ReservasiController::class,'show']);

    Route::post('/reservasis/{id}/checkin',
        [ReservasiController::class,'checkin']);

    Route::put('/reservasis/{id}/status',
        [ReservasiController::class,'updateStatus']);
});

