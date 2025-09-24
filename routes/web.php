<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HairdresserController;
use App\Http\Controllers\CalculatingPointsController;

Route::get('/', function () {
    return view('layouts.app');
});

Route::get('/add-hairdresser', [HairdresserController::class, 'create'])->name('add-hairdresser');
Route::post('/add-hairdresser', [HairdresserController::class, 'store'])->name('hairdresser.store');

Route::get('/calculating-points', [CalculatingPointsController::class, 'create'])->name('calculating-points');
Route::post('/calculating-points', [CalculatingPointsController::class, 'store'])->name('calculating-points.store');
