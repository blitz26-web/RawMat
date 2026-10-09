<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductionBatchController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Redirect root ke Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Route khusus BOM Produk
Route::get('/products/{product}/bom', [ProductController::class, 'getBom'])->name('products.bom');

// Group Route Batches
Route::prefix('batches')->name('batches.')->group(function () {
    Route::get('/', [ProductionBatchController::class, 'index'])->name('index');
    Route::get('/create', [ProductionBatchController::class, 'create'])->name('create');
    Route::post('/', [ProductionBatchController::class, 'store'])->name('store');
    Route::get('/{batch}', [ProductionBatchController::class, 'show'])->name('show');
    Route::post('/{batch}/start', [ProductionBatchController::class, 'start'])->name('start');
    Route::post('/{batch}/complete', [ProductionBatchController::class, 'complete'])->name('complete');
    Route::patch('/{batch}/status', [ProductionBatchController::class, 'updateStatus'])->name('update-status');
    Route::delete('/{batch}', [ProductionBatchController::class, 'destroy'])->name('destroy');
});