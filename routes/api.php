<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\TripFileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItineraryController;

//Register login logout
// Route::get('/test', function (){
//     echo(Carbon::today()."\n");
//     print_r (now()->subWeek()->startOfWeek(). "\n");
//     echo(now()->subWeek()->endOfWeek(). "\n");
// });
Route::prefix('/auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});


Route::middleware(['auth:api'])->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
});

//trips managment
Route::get('/trips', [TripController::class, 'index']); //only admin still do

Route::prefix('/trips')->middleware(['auth:api'])->group(function () {

    // Trips
    Route::post('/', [TripController::class, 'store']);
    Route::get('/{trip}', [TripController::class, 'show']);
    Route::put('/{trip}', [TripController::class, 'update']);
    Route::delete('/{trip}', [TripController::class, 'destroy']);
    // Trip members
    Route::post('/{trip}/add-member', [TripController::class, 'addMember']);
    Route::delete('/{trip}/members/{user}', [TripController::class, 'removeMember']);
});


//upload files in trips
Route::middleware('auth:api')->group(function () {

    Route::prefix('trips/{trip}/files')->group(function () {
        Route::post('/', [TripFileController::class, 'upload']);
        Route::get('/',  [TripFileController::class, 'list']);
        Route::delete('{file}', [TripFileController::class, 'delete']);
    });
});


//itinarary managment
Route::middleware('auth:api')->prefix('trips/{trip}/itineraries')->group(function () {
        Route::get('/', [ItineraryController::class, 'index']);
        Route::post('/', [ItineraryController::class, 'store']);
        Route::put('/{itinerary}', [ItineraryController::class, 'update']);
        Route::delete('/{itinerary}', [ItineraryController::class, 'destroy']);
});

//Expenses
Route::middleware(['auth:api','check.trip.members'])->prefix('trips/{trip}/expenses')->group(function () {
    Route::post('/', [ExpenseController::class, 'store']);
    Route::patch('/settle/{expense}', [ExpenseController::class, 'settle']);
    Route::get('/report', [ExpenseController::class, 'report']);
});


Route::get('/dashboard/trip/{trip}', [DashboardController::class, 'showTripDashboard'])->name('dashboard');