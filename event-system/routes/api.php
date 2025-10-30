<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RsvpController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\EventController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->post('/change-password', [AuthController::class, 'changePassword']);

Route::middleware('auth:sanctum')->post('/events/{event}/rsvp', [RsvpController::class, 'rsvp']);
Route::post('/events/{event}/rsvp/guest', [RsvpController::class, 'rsvp']);

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);
    
    Route::put('/seats/{seat}/block', [SeatController::class, 'block']);
    Route::put('/seats/{seat}/unblock', [SeatController::class, 'unblock']);
});

Route::post('/events/{event}/guest-rsvp', [RsvpController::class, 'guestRsvp']);

// Public endpoints
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);

Route::get('/events/{event}/seats', [SeatController::class, 'index']);

