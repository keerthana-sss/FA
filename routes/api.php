<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TripController;

Route::prefix('/auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});


Route::middleware(['auth:api'])->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
});

Route::prefix('/trips')->middleware(['auth:api'])->group(function () {

    // // Trips
    Route::get('/', [TripController::class, 'index']);
    Route::post('/', [TripController::class, 'store']);
    Route::get('/{trip}', [TripController::class, 'show']);
    Route::put('/{trip}', [TripController::class, 'update']);
    Route::delete('/{trip}', [TripController::class, 'destroy']);

    // Trip members
    Route::post('/{trip}/add-member', [TripController::class, 'addMember']);
    Route::delete('/{trip}/members/{user}', [TripController::class, 'removeMember']);
});