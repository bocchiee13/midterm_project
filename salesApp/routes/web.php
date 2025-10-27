<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\InventoryController;

Route::get('/', function () {
    return redirect()->route('sales.index');
});

// Sales Export/Import Routes (before resource routes to avoid conflicts)
Route::get('sales/export', [SaleController::class, 'export'])->name('sales.export');
Route::post('sales/import', [SaleController::class, 'import'])->name('sales.import');

// Sales History and Bulk Actions
Route::get('sales/history', [SaleController::class, 'history'])->name('sales.history');
Route::delete('sales/destroy-all', [SaleController::class, 'destroyAll'])->name('sales.destroy.all');

// AJAX route for getting inventory details
Route::get('sales/inventory/{id}', [SaleController::class, 'getInventoryDetails'])->name('sales.inventory.details');

// Sales Resource Routes
Route::resource('sales', SaleController::class);

// Inventory Export/Import Routes (before resource routes to avoid conflicts)
Route::get('inventory/export', [InventoryController::class, 'export'])->name('inventory.export');
Route::post('inventory/import', [InventoryController::class, 'import'])->name('inventory.import');

// Inventory History and Bulk Actions
Route::get('inventory/history', [InventoryController::class, 'history'])->name('inventory.history');
Route::delete('inventory/destroy-all', [InventoryController::class, 'destroyAll'])->name('inventory.destroy.all');

// Inventory Resource Routes
Route::resource('inventory', InventoryController::class);