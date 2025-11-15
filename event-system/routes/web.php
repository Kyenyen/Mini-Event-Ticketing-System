<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    // Serve the SPA entry (adjust view name if different)
    return view('welcome');
})->where('any', '.*');
