<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductionBatchController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Redirect root ke Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('batches')->name('batches.')->group(function () {
    Route::get('/', [ProductionBatchController::class, 'index'])->name('index');
    Route::get('/create', [ProductionBatchController::class, 'create'])->name('create');
    Route::post('/', [ProductionBatchController::class, 'store'])->name('store');
    Route::get('/{batch}', [ProductionBatchController::class, 'show'])->name('show');
    Route::post('/{batch}/start', [ProductionBatchController::class, 'start'])->name('start');
    Route::post('/{batch}/complete', [ProductionBatchController::class, 'complete'])->name('complete');
    Route::get('/products/{product}/bom', [ProductController::class, 'getBom'])->name('products.bom');
    Route::patch('/batches/{batch}/status', [ProductionBatchController::class, 'updateStatus'])->name('batches.update-status');
});