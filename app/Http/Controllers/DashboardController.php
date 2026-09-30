<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Recipient;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard with real-time indicators.
     */
    public function index(): View
    {
        $activeEmployees = Recipient::where('status', 'active')->count();
        $totalItems = Item::count();
        $totalStock = Item::sum('current_stock');
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

        return view('dashboard', compact(
            'activeEmployees',
            'totalItems',
            'totalStock',
            'lowStockCount',
            'outOfStockCount',
            'workUnitsCount',
            'recentItems',
            'lowStockItems'
        ));
    }
}
