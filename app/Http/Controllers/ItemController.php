<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ItemController extends Controller
{
    /**
     * Display a listing of ATK Master Items.
     */
    public function index(Request $request): View
    {
        $query = Item::with(['category', 'unitDetail', 'supplier']);

        // Search by Keyword (Name, Code, Barcode)
        if ($keyword = $request->filled('keyword') ? trim($request->keyword) : null) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('code', 'like', "%{$keyword}%")
                  ->orWhere('barcode', 'like', "%{$keyword}%");
            });
        }

        // Filter by Category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by Status (active/inactive)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Stock Status
        if ($request->filled('stock_status')) {
            match ($request->stock_status) {
                'available' => $query->available(),
                'low_stock' => $query->lowStock(),
                'out_of_stock' => $query->outOfStock(),
                default => null,
            };
        }

        // Summary Metric Cards
        $totalItems = Item::count();
        $totalPhysicalStock = Item::sum('current_stock');
        $lowStockCount = Item::lowStock()->count();
        $outOfStockCount = Item::outOfStock()->count();

        // Paginated results
        $items = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        // Dropdowns for modal & filters
        $categories = Category::active()->orderBy('name')->get();
        $units = Unit::active()->orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('inventory.items.index', compact(
            'items',
            'categories',
            'units',
            'suppliers',
            'totalItems',
            'totalPhysicalStock',
            'lowStockCount',
            'outOfStockCount'
        ));
    }

    /**
     * Store a newly created ATK item.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:items,code'],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:items,barcode'],
            'name' => ['required', 'string', 'max:200'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'small_unit' => ['nullable', 'string', 'max:50'],
            'conversion_rate' => ['nullable', 'integer', 'min:1'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'target_stock' => ['required', 'integer', 'min:0'],
            'current_stock' => ['nullable', 'integer', 'min:0'],
            'storage_location' => ['nullable', 'string', 'max:150'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'status' => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
        ], [
            'code.unique' => 'Kode ATK sudah terdaftar.',
            'barcode.unique' => 'Barcode sudah digunakan oleh item lain.',
            'category_id.required' => 'Kategori ATK wajib dipilih.',
            'unit_id.required' => 'Satuan ATK wajib dipilih.',
        ]);

        $unit = Unit::find($validated['unit_id']);
        $validated['unit'] = $unit?->name ?? 'Pcs';
        $validated['small_unit'] = !empty($validated['small_unit']) ? $validated['small_unit'] : ($unit?->name ?? 'Pcs');
        $validated['conversion_rate'] = max(1, (int) ($validated['conversion_rate'] ?? 1));
        $validated['current_stock'] = $validated['current_stock'] ?? 0;

        Item::create($validated);

        return redirect()->route('inventory.items.index')->with('status', 'Barang ATK berhasil ditambahkan ke inventaris.');
    }

    /**
     * Update the specified ATK item.
     */
    public function update(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('items', 'code')->ignore($item->id)],
            'barcode' => ['nullable', 'string', 'max:100', Rule::unique('items', 'barcode')->ignore($item->id)],
            'name' => ['required', 'string', 'max:200'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'small_unit' => ['nullable', 'string', 'max:50'],
            'conversion_rate' => ['nullable', 'integer', 'min:1'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'target_stock' => ['required', 'integer', 'min:0'],
            'storage_location' => ['nullable', 'string', 'max:150'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'status' => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
        ], [
            'code.unique' => 'Kode ATK sudah terdaftar.',
            'barcode.unique' => 'Barcode sudah digunakan oleh item lain.',
        ]);

        $unit = Unit::find($validated['unit_id']);
        $validated['unit'] = $unit?->name ?? $item->unit;
        $validated['small_unit'] = !empty($validated['small_unit']) ? $validated['small_unit'] : ($item->small_unit ?: ($unit?->name ?? 'Pcs'));
        $validated['conversion_rate'] = max(1, (int) ($validated['conversion_rate'] ?? 1));

        $item->update($validated);

        return redirect()->route('inventory.items.index')->with('status', 'Data barang ATK berhasil diperbarui.');
    }

    /**
     * Remove or toggle status of the specified ATK item.
     */
    public function destroy(Item $item): RedirectResponse
    {
        $newStatus = $item->status === 'active' ? 'inactive' : 'active';
        $item->update(['status' => $newStatus]);

        $message = $newStatus === 'inactive' ? 'Barang ATK telah dinonaktifkan.' : 'Barang ATK telah diaktifkan kembali.';
        return redirect()->route('inventory.items.index')->with('status', $message);
    }
}
