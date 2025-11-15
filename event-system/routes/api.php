<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RsvpController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\EventController;

// 🔐 Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 🧍 Protected user routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // RSVP routes
    Route::get('/rsvps', [RsvpController::class, 'index'])->name('rsvps.index');
    Route::get('/rsvps/{rsvp}', [RsvpController::class, 'show'])->name('rsvps.show');
    Route::delete('/rsvps/{rsvp}', [RsvpController::class, 'cancel'])->name('rsvps.cancel');
    Route::post('/events/{event}/rsvp', [RsvpController::class, 'rsvp']);
});

// 👥 Guest RSVP (no login)
Route::post('/events/{event}/guest-rsvp', [RsvpController::class, 'guestRsvp']);

// 🛠️ Admin-only routes
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);
    
    Route::put('/seats/{seat}/block', [SeatController::class, 'block']);
    Route::put('/seats/{seat}/unblock', [SeatController::class, 'unblock']);

    Route::get('/admin/rsvps', [RsvpController::class, 'manage']);
    Route::get('/events/{event}/rsvps/export/csv', [RsvpController::class, 'exportCsv']);
    Route::get('/events/{event}/rsvps/export/excel', [RsvpController::class, 'exportExcel']);
});

// 🌍 Public endpoints
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);
Route::get('/events/{event}/seats', [SeatController::class, 'index']);
