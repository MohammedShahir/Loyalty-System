<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HairdresserController;
use App\Http\Controllers\CalculatingPointsController;
use App\Http\Controllers\ControlController;
use App\Http\Controllers\AuthController;

// Route::middleware('guest')->group(function () {
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
// });

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/add-hairdresser', [HairdresserController::class, 'create'])->name('add-hairdresser');
    Route::post('/add-hairdresser', [HairdresserController::class, 'store'])->name('hairdresser.store');
    Route::get('/hairdressers/{id}/edit', [HairdresserController::class, 'edit'])->name('hairdresser.edit');
    Route::put('/hairdressers/{id}', [HairdresserController::class, 'update'])->name('hairdresser.update');

    Route::get('/calculating-points', [CalculatingPointsController::class, 'create'])->name('calculating-points');
    Route::post('/calculating-points', [CalculatingPointsController::class, 'store'])->name('calculating-points.store');

    Route::get('/control', [ControlController::class, 'index'])->name('control.index');
    Route::delete('/control/{id}', [ControlController::class, 'destroy'])->name('control.destroy');
    Route::post('/confirm-password', [ControlController::class, 'confirmPassword'])->name('confirm.password');
    Route::get('/reports', [ControlController::class, 'reports'])->name('reports.index');
    // Server-side printable PDF (supports Arabic when dompdf + Arabic font installed)
    Route::get('/reports/pdf', [ControlController::class, 'reportsPdf'])->name('reports.pdf');
    // Statistics dashboard
    Route::get('/stats', [ControlController::class, 'stats'])->name('reports.stats');

    // Issued cards management (create personalized cards)
    Route::get('/issued-cards/create', [\App\Http\Controllers\IssuedCardController::class, 'create'])->name('issued_cards.create');
    Route::post('/issued-cards', [\App\Http\Controllers\IssuedCardController::class, 'store'])->name('issued_cards.store');
    Route::get('/issued-cards', [\App\Http\Controllers\IssuedCardController::class, 'index'])->name('issued_cards.index');
    Route::get('/issued-cards/verify', [\App\Http\Controllers\IssuedCardController::class, 'verifyForm'])->name('issued_cards.verify.form');
    Route::post('/issued-cards/verify', [\App\Http\Controllers\IssuedCardController::class, 'verify'])->name('issued_cards.verify');
    Route::post('/issued-cards/{public_code}/revoke', [\App\Http\Controllers\IssuedCardController::class, 'revoke'])->name('issued_cards.revoke');
    Route::get('/issued-cards/{public_code}/audit', [\App\Http\Controllers\IssuedCardController::class, 'audit'])->name('issued_cards.audit');
    Route::post('/issued-cards/{public_code}/undo-audit', [\App\Http\Controllers\IssuedCardController::class, 'undoAudit'])->name('issued_cards.undo_audit');
    // Sales invoice management
    Route::get('/sales/{id}/edit', [\App\Http\Controllers\SalesController::class, 'edit'])->name('sales.edit');
    Route::put('/sales/{id}', [\App\Http\Controllers\SalesController::class, 'update'])->name('sales.update');
    Route::delete('/sales/{id}', [\App\Http\Controllers\SalesController::class, 'destroy'])->name('sales.destroy');
});
