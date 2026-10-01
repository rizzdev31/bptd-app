<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Recipient;
use App\Models\StockOut;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama dengan indikator real-time terintegrasi data Permintaan & Pengeluaran ATK.
     */
    public function index(): View
    {
        $activeEmployees = Recipient::where('status', 'active')->count();
        $totalItems = Item::count();
        $totalStock = (int) Item::sum('current_stock');
        $lowStockCount = Item::lowStock()->count();
        $outOfStockCount = Item::outOfStock()->count();
        $workUnitsCount = WorkUnit::where('status', 'active')->count();

        $recentItems = Item::with(['category', 'unitDetail'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $lowStockItems = Item::with('category')
            ->lowStock()
            ->orderBy('current_stock', 'asc')
            ->take(5)
            ->get();

        // 1. Data Real-time Permintaan & Pengeluaran ATK (Stock Out)
        $todayStockOutCount = StockOut::whereDate('transaction_date', today())->count();
        $todayQuantityOut = (int) StockOut::whereDate('transaction_date', today())->sum('total_quantity');

        $monthStockOutCount = StockOut::whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->count();
        $monthQuantityOut = (int) StockOut::whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('total_quantity');

        $totalStockOutCount = StockOut::count();
        $totalQuantityOut = (int) StockOut::sum('total_quantity');

        // 2. Daftar Transaksi Permintaan / Pengeluaran Terkini (Real-time Feed)
        $recentStockOuts = StockOut::with(['details.item', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // 3. Data Bulanan Distribusi ATK Tahun Berjalan (Jan - Des) untuk ApexCharts
        $currentYear = (int) date('Y');
        $yearlyStockOuts = StockOut::whereYear('transaction_date', $currentYear)
            ->get(['transaction_date', 'total_quantity']);

        $monthlyStockOutData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyStockOutData[] = (int) $yearlyStockOuts
                ->filter(fn($s) => (int) $s->transaction_date->format('n') === $m)
                ->sum('total_quantity');
        }

        // 4. Data Sparkline Tren 7 Hari Terakhir
        $recent7Days = StockOut::where('transaction_date', '>=', now()->subDays(6)->toDateString())
            ->get(['transaction_date', 'total_quantity']);

        $sparklineTx = [];
        $sparklineQty = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateStr = now()->subDays($i)->format('Y-m-d');
            $dayItems = $recent7Days->filter(fn($s) => $s->transaction_date->format('Y-m-d') === $dateStr);
            $sparklineTx[] = $dayItems->count();
            $sparklineQty[] = (int) $dayItems->sum('total_quantity');
        }

        // Fallback data visual sparkline jika sistem baru/belum banyak transaksi
        if (array_sum($sparklineTx) === 0 && $monthStockOutCount > 0) {
            $sparklineTx = [0, 0, 0, 0, 1, 1, $todayStockOutCount];
        }
        if (array_sum($sparklineQty) === 0 && $monthQuantityOut > 0) {
            $sparklineQty = [0, 0, 0, 0, 1, 2, $todayQuantityOut];
        }

        return view('dashboard', compact(
            'activeEmployees',
            'totalItems',
            'totalStock',
            'lowStockCount',
            'outOfStockCount',
            'workUnitsCount',
            'recentItems',
            'lowStockItems',
            'todayStockOutCount',
            'todayQuantityOut',
            'monthStockOutCount',
            'monthQuantityOut',
            'totalStockOutCount',
            'totalQuantityOut',
            'recentStockOuts',
            'monthlyStockOutData',
            'sparklineTx',
            'sparklineQty'
        ));
    }
}
