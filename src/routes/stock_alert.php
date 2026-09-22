<?php

use App\Http\Controllers\Admin\StockAlertController;
use App\Http\Controllers\InternalStockAlertController;
use App\Http\Middleware\EnsureStockAlertInternalToken;
use App\Http\Controllers\StockLineWebhookController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::post('/stock-alert/line/webhook', StockLineWebhookController::class)
    ->withoutMiddleware([ValidateCsrfToken::class])
    ->name('stock-alert.line.webhook');


Route::middleware(EnsureStockAlertInternalToken::class)
    ->prefix('stock-alert/internal')
    ->withoutMiddleware([ValidateCsrfToken::class])
    ->group(function (): void {
        Route::post('/scan', [InternalStockAlertController::class, 'scan']);
        Route::post('/morning-summary', [InternalStockAlertController::class, 'morningSummary']);
    });

Route::middleware('auth:admin')
    ->prefix('admin/stocks')
    ->name('admin.stocks.')
    ->group(function (): void {
        Route::get('/', [StockAlertController::class, 'index'])->name('index');
        Route::post('/', [StockAlertController::class, 'store'])->name('store');
        Route::put('/settings', [StockAlertController::class, 'updateSettings'])->name('settings.update');
        Route::put('/{watchlist}', [StockAlertController::class, 'update'])->name('update');
        Route::delete('/{watchlist}', [StockAlertController::class, 'destroy'])->name('destroy');
    });
