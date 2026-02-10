<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InstrumentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReservationController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    Route::middleware('admin')->group(function(){
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::post('/instruments', [InstrumentController::class, 'store']);
        Route::put('/instruments/{id}', [InstrumentController::class, 'update']);
        Route::delete('/instruments/{id}', [InstrumentController::class, 'destroy']);
        Route::get('/reservations', [ReservationController::class, 'index']);
    });

    Route::get('/instruments', [InstrumentController::class, 'index']);
    Route::get('/instruments/{id}', [InstrumentController::class, 'show']);
    Route::post('/instruments/{id}/reserve', [ReservationController::class, 'reserve']);
    
    Route::get('/reservations/my', [ReservationController::class, 'myReservations']);
    Route::post('/reservations/{id}/return', [ReservationController::class, 'returnReservation']);
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);
});