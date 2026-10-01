<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Inventory\StockOutController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RolePermissionController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Pegawai & Penerima ATK
    Route::get('/pegawai', [EmployeeController::class, 'index'])->name('pegawai.index');
    Route::post('/pegawai', [EmployeeController::class, 'store'])->name('pegawai.store');
    Route::put('/pegawai/{recipient}', [EmployeeController::class, 'update'])->name('pegawai.update');
    Route::delete('/pegawai/{recipient}', [EmployeeController::class, 'destroy'])->name('pegawai.destroy');

    // Master Inventory ATK
    Route::prefix('inventory')->name('inventory.')->group(function () {
        // Submenu 1: Data Master ATK
        Route::get('/items', [ItemController::class, 'index'])->name('items.index');
        Route::post('/items', [ItemController::class, 'store'])->name('items.store');
        Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

        // Submenu 2: Permintaan / Pengeluaran ATK (Kasir POS Distribusi)
        Route::get('/stock-out', [StockOutController::class, 'index'])->name('stock-out.index');
        Route::post('/stock-out', [StockOutController::class, 'store'])->name('stock-out.store');
        Route::get('/stock-out/{stockOut}', [StockOutController::class, 'show'])->name('stock-out.show');
        Route::get('/stock-out/{stockOut}/print', [StockOutController::class, 'print'])->name('stock-out.print');
    });

    // Pengaturan Sistem / Role & Hak Akses
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RolePermissionController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [RolePermissionController::class, 'update'])->name('roles.update');
        Route::put('/roles/{role}/permissions', [RolePermissionController::class, 'updatePermissions'])->name('roles.permissions.update');
        Route::delete('/roles/{role}', [RolePermissionController::class, 'destroy'])->name('roles.destroy');
    });
});
