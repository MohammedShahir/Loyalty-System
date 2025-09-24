<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HairdresserController;

Route::get('/', function () {
    return view('layouts.app');
});

Route::get('/add-hairdresser', [HairdresserController::class, 'create'])->name('add-hairdresser');
Route::post('/add-hairdresser', [HairdresserController::class, 'store'])->name('hairdresser.store');
