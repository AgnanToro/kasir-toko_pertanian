<?php

use Illuminate\Support\Facades\Route;

// Aplikasi Vue
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
