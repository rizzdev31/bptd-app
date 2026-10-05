<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Inventory\AuditController;
use App\Http\Controllers\Inventory\ProcurementController;
use App\Http\Controllers\Inventory\ReportController;
use App\Http\Controllers\Inventory\StockControlController;
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

        // Submenu 3: Kendali Stock & Audit (Monitoring, Adjustment, Opname, Ledger)
        Route::get('/stock-control', [StockControlController::class, 'index'])->name('stock-control.index');
        Route::post('/stock-control/adjustment', [StockControlController::class, 'storeAdjustment'])->name('stock-control.adjustment.store');
        Route::get('/stock-control/adjustment/{stockAdjustment}', [StockControlController::class, 'showAdjustment'])->name('stock-control.adjustment.show');
        Route::post('/stock-control/opname', [StockControlController::class, 'storeOpname'])->name('stock-control.opname.store');
        Route::get('/stock-control/opname/{stockOpname}', [StockControlController::class, 'showOpname'])->name('stock-control.opname.show');

        // Submenu 4: Pengadaan & Penerimaan Stok (Procurement & Stock In)
        Route::get('/procurement', [ProcurementController::class, 'index'])->name('procurement.index');
        Route::post('/procurement', [ProcurementController::class, 'store'])->name('procurement.store');
        Route::get('/procurement/{procurement}', [ProcurementController::class, 'show'])->name('procurement.show');
        Route::post('/procurement/{procurement}/receive', [ProcurementController::class, 'receive'])->name('procurement.receive');
        Route::post('/procurement/{procurement}/cancel', [ProcurementController::class, 'cancel'])->name('procurement.cancel');
        Route::get('/procurement/{procurement}/print', [ProcurementController::class, 'print'])->name('procurement.print');
        Route::post('/procurement/supplier', [ProcurementController::class, 'storeSupplier'])->name('procurement.supplier.store');
        Route::post('/procurement/direct-stock-in', [ProcurementController::class, 'storeDirectStockIn'])->name('procurement.direct-stock-in.store');

        // Submenu 5: Laporan & Ekspor Persediaan (Reports & Exports)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
        Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');
        Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');

        // Submenu 6: Audit Trail & Jejak Aktivitas (PRD Seksi 28)
        Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
        Route::get('/audit/export-excel', [AuditController::class, 'exportExcel'])->name('audit.export-excel');
        Route::get('/audit/export-csv', [AuditController::class, 'exportCsv'])->name('audit.export-csv');
        Route::get('/audit/{auditLog}', [AuditController::class, 'show'])->name('audit.show');
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
