<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Item;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetail;
use App\Models\StockLedger;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockControlController extends Controller
{
    /**
     * Halaman Utama Kendali Stok & Audit Persediaan ATK (PRD Seksi 12, 13, 14, 16)
     */
    public function index(Request $request)
    {
        // 1. Metrik Global Pemantauan Stok
        $totalActiveItems = Item::active()->count();
        $availableItems = Item::active()->available()->count();
        $lowStockItems = Item::active()->lowStock()->count();
        $outOfStockItems = Item::active()->outOfStock()->count();
        $totalPhysicalUnits = (int) Item::active()->sum('current_stock');

        // 2. Data Master Referensi (Kategori & Seluruh ATK Aktif untuk Selector)
        $categories = Category::orderBy('name')->get();
        $allItems = Item::active()
            ->with(['category', 'unitDetail'])
            ->orderBy('name')
            ->get();

        // 3. Tab 1: Query Pemantauan & Status Stok (Monitoring)
        $monitoringQuery = Item::active()
            ->with(['category', 'unitDetail'])
            ->orderBy('name');

        if ($request->filled('monitor_category_id')) {
            $monitoringQuery->where('category_id', $request->monitor_category_id);
        }

        if ($request->filled('monitor_keyword')) {
            $mKw = $request->monitor_keyword;
            $monitoringQuery->where(function ($q) use ($mKw) {
                $q->where('name', 'like', "%{$mKw}%")
                  ->orWhere('code', 'like', "%{$mKw}%")
                  ->orWhere('barcode', 'like', "%{$mKw}%")
                  ->orWhere('storage_location', 'like', "%{$mKw}%");
            });
        }

        if ($request->filled('monitor_status')) {
            $status = $request->monitor_status;
            if ($status === 'need_restock') {
                $monitoringQuery->whereColumn('current_stock', '<=', 'minimum_stock');
            } elseif ($status === 'low_stock') {
                $monitoringQuery->lowStock();
            } elseif ($status === 'out_of_stock') {
                $monitoringQuery->outOfStock();
            } elseif ($status === 'available') {
                $monitoringQuery->available();
            }
        }

        $monitoredItems = $monitoringQuery->paginate(15, ['*'], 'mon_page')->withQueryString();

        // 4. Tab 2: Riwayat Penyesuaian Stok (Stock Adjustment)
        $adjQuery = StockAdjustment::with(['user', 'details.item.category'])
            ->orderByDesc('adjustment_date')
            ->orderByDesc('id');

        if ($request->filled('adj_keyword')) {
            $aKw = $request->adj_keyword;
            $adjQuery->where(function ($q) use ($aKw) {
                $q->where('adjustment_number', 'like', "%{$aKw}%")
                  ->orWhere('reason', 'like', "%{$aKw}%")
                  ->orWhere('notes', 'like', "%{$aKw}%");
            });
        }

        if ($request->filled('adj_date_from')) {
            $adjQuery->whereDate('adjustment_date', '>=', $request->adj_date_from);
        }
        if ($request->filled('adj_date_to')) {
            $adjQuery->whereDate('adjustment_date', '<=', $request->adj_date_to);
        }

        $adjustments = $adjQuery->paginate(10, ['*'], 'adj_page')->withQueryString();
        $nextAdjustmentNumber = StockAdjustment::generateAdjustmentNumber();

        // 5. Tab 3: Riwayat Pemeriksaan Fisik (Stock Opname)
        $opnQuery = StockOpname::with(['user', 'details.item'])
            ->orderByDesc('opname_date')
            ->orderByDesc('id');

        if ($request->filled('opn_keyword')) {
            $oKw = $request->opn_keyword;
            $opnQuery->where(function ($q) use ($oKw) {
                $q->where('opname_number', 'like', "%{$oKw}%")
                  ->orWhere('conducted_by', 'like', "%{$oKw}%")
                  ->orWhere('notes', 'like', "%{$oKw}%");
            });
        }

        $opnames = $opnQuery->paginate(10, ['*'], 'opn_page')->withQueryString();
        $nextOpnameNumber = StockOpname::generateOpnameNumber();

        // 6. Tab 4: Kartu Stok & Buku Besar Mutasi (Stock Ledger)
        $ledgerQuery = StockLedger::with(['item.category', 'user'])
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($request->filled('ledger_item_id')) {
            $ledgerQuery->where('item_id', $request->ledger_item_id);
        }

        if ($request->filled('ledger_type')) {
            $ledgerQuery->where('transaction_type', $request->ledger_type);
        }

        if ($request->filled('ledger_date_from')) {
            $ledgerQuery->whereDate('created_at', '>=', $request->ledger_date_from);
        }
        if ($request->filled('ledger_date_to')) {
            $ledgerQuery->whereDate('created_at', '<=', $request->ledger_date_to);
        }

        if ($request->filled('ledger_keyword')) {
            $lKw = $request->ledger_keyword;
            $ledgerQuery->where(function ($q) use ($lKw) {
                $q->where('reference_number', 'like', "%{$lKw}%")
                  ->orWhere('description', 'like', "%{$lKw}%");
            });
        }

        $ledgers = $ledgerQuery->paginate(15, ['*'], 'led_page')->withQueryString();

        return view('inventory.stock_control.index', compact(
            'totalActiveItems',
            'availableItems',
            'lowStockItems',
            'outOfStockItems',
            'totalPhysicalUnits',
            'categories',
            'allItems',
            'monitoredItems',
            'adjustments',
            'nextAdjustmentNumber',
            'opnames',
            'nextOpnameNumber',
            'ledgers'
        ));
    }

    /**
     * Simpan Transaksi Penyesuaian Stok (PRD Seksi 13)
     */
    public function storeAdjustment(Request $request)
    {
        $validated = $request->validate([
            'adjustment_date' => 'required|date',
            'reason' => 'required|string|max:150',
            'type' => 'nullable|string|in:INCREASE,DECREASE,CORRECTION',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.unit' => 'nullable|string|max:30',
            'items.*.mode' => 'nullable|string|in:actual,delta', // actual = input stok akhir, delta = selisih +/-
            'items.*.actual_stock' => 'nullable|numeric',
            'items.*.delta_qty' => 'nullable|numeric',
            'items.*.notes' => 'nullable|string|max:255',
        ], [
            'reason.required' => 'Alasan penyesuaian stok wajib diisi secara jelas.',
            'items.required' => 'Pilih minimal 1 barang persediaan untuk disesuaikan.',
            'items.min' => 'Pilih minimal 1 barang persediaan untuk disesuaikan.',
        ]);

        try {
            $createdAdjustment = DB::transaction(function () use ($validated) {
                $detailsToInsert = [];
                $totalItemsCount = 0;
                $adjType = $validated['type'] ?? 'CORRECTION';

                foreach ($validated['items'] as $entry) {
                    $item = Item::lockForUpdate()->find($entry['item_id']);
                    if (!$item) {
                        throw ValidationException::withMessages([
                            'items' => ["Barang ATK dengan ID {$entry['item_id']} tidak ditemukan."],
                        ]);
                    }

                    $stockBefore = (int) $item->current_stock;
                    $mode = $entry['mode'] ?? 'actual';
                    $rate = max(1, (int) $item->conversion_rate);
                    $unitSelected = $entry['unit'] ?? ($item->effective_small_unit ?: 'Pcs');
                    $isPackaging = $item->has_multi_unit && strcasecmp($unitSelected, $item->effective_small_unit) !== 0;

                    if ($mode === 'delta' && isset($entry['delta_qty'])) {
                        // Delta input (+ atau -)
                        $deltaInput = (int) $entry['delta_qty'];
                        if ($deltaInput === 0) {
                            throw ValidationException::withMessages([
                                'items' => ["Nilai koreksi selisih untuk {$item->name} tidak boleh bernilai 0."],
                            ]);
                        }
                        $physicalDelta = $isPackaging ? ($deltaInput * $rate) : $deltaInput;
                        $stockAfter = $stockBefore + $physicalDelta;
                        $difference = $physicalDelta;
                    } else {
                        // Actual stock input (stok fisik akhir yang dihitung)
                        if (!isset($entry['actual_stock']) || $entry['actual_stock'] === '') {
                            throw ValidationException::withMessages([
                                'items' => ["Stok fisik nyata untuk {$item->name} wajib diisi."],
                            ]);
                        }
                        $actualInput = (int) $entry['actual_stock'];
                        $stockAfter = $isPackaging ? ($actualInput * $rate) : $actualInput;
                        $difference = $stockAfter - $stockBefore;
                    }

                    // PRD Rule: Stok tidak boleh negatif
                    if ($stockAfter < 0) {
                        throw ValidationException::withMessages([
                            'items' => ["Penyesuaian ditolak: Stok akhir untuk {$item->name} tidak boleh kurang dari 0 (hasil: {$stockAfter})."],
                        ]);
                    }

                    // Update stok fisik item
                    $item->update(['current_stock' => $stockAfter]);

                    $detailsToInsert[] = [
                        'item' => $item,
                        'item_id' => $item->id,
                        'system_stock' => $stockBefore,
                        'actual_stock' => $stockAfter,
                        'difference' => $difference,
                        'unit' => $unitSelected,
                        'notes' => $entry['notes'] ?? null,
                    ];

                    $totalItemsCount++;
                }

                // Buat Header Stock Adjustment
                $adjNumber = StockAdjustment::generateAdjustmentNumber();
                $adjustment = StockAdjustment::create([
                    'adjustment_number' => $adjNumber,
                    'adjustment_date' => $validated['adjustment_date'],
                    'type' => $adjType,
                    'total_items' => $totalItemsCount,
                    'reason' => $validated['reason'],
                    'notes' => $validated['notes'] ?? null,
                    'user_id' => auth()->id(),
                ]);

                // Buat Details & Catat Stock Ledger resmi
                foreach ($detailsToInsert as $detail) {
                    $itemObj = $detail['item'];
                    unset($detail['item']);

                    $adjustment->details()->create($detail);

                    $sign = $detail['difference'] > 0 ? "+{$detail['difference']}" : "{$detail['difference']}";
                    $desc = "Penyesuaian Stok: {$sign} {$itemObj->effective_small_unit}. Alasan: {$adjustment->reason}";
                    if (!empty($detail['notes'])) {
                        $desc .= " ({$detail['notes']})";
                    }

                    StockLedger::create([
                        'item_id' => $detail['item_id'],
                        'transaction_type' => 'ADJUSTMENT',
                        'quantity' => abs($detail['difference']),
                        'balance_before' => $detail['system_stock'],
                        'balance_after' => $detail['actual_stock'],
                        'reference_type' => StockAdjustment::class,
                        'reference_id' => $adjustment->id,
                        'reference_number' => $adjustment->adjustment_number,
                        'user_id' => auth()->id(),
                        'description' => $desc,
                    ]);
                }

                // Catat Log Audit Sesuai PRD Seksi 13 & 28
                AuditLog::record(
                    action: 'ADJUSTMENT',
                    module: 'Kendali Stok',
                    description: "Penyesuaian stok no. {$adjustment->adjustment_number} ({$adjustment->total_items} item). Alasan: {$adjustment->reason}",
                    recordType: StockAdjustment::class,
                    recordId: $adjustment->id,
                    newValues: [
                        'adjustment_number' => $adjustment->adjustment_number,
                        'reason' => $adjustment->reason,
                        'total_items' => $adjustment->total_items,
                        'items' => collect($detailsToInsert)->map(fn($d) => "Item #{$d['item_id']}: {$d['system_stock']} -> {$d['actual_stock']} (Selisih: {$d['difference']})")->toArray(),
                    ]
                );

                return $adjustment;
            });

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Penyesuaian stok {$createdAdjustment->adjustment_number} berhasil dibukukan.",
                    'adjustment_number' => $createdAdjustment->adjustment_number,
                ]);
            }

            return redirect()->route('inventory.stock-control.index', ['tab' => 'adjustment'])
                ->with('success', "Penyesuaian stok {$createdAdjustment->adjustment_number} berhasil dibukukan.");

        } catch (ValidationException $ve) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $ve->getMessage(),
                    'errors' => $ve->errors(),
                ], 422);
            }
            throw $ve;
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem saat menyimpan penyesuaian: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Simpan Sesi & Rekonsiliasi Hasil Stock Opname (PRD Seksi 14)
     */
    public function storeOpname(Request $request)
    {
        $validated = $request->validate([
            'opname_date' => 'required|date',
            'conducted_by' => 'required|string|max:100',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.physical_stock' => 'required|integer|min:0',
            'items.*.notes' => 'nullable|string|max:255',
        ], [
            'conducted_by.required' => 'Nama tim atau pemeriksa fisik wajib diisi.',
            'items.required' => 'Daftar barang hasil pemeriksaan fisik wajib diisi.',
            'items.min' => 'Daftar barang hasil pemeriksaan fisik wajib diisi minimal 1 barang.',
        ]);

        try {
            $createdOpname = DB::transaction(function () use ($validated) {
                $totalItems = 0;
                $matchedCount = 0;
                $mismatchedCount = 0;
                $detailsToInsert = [];

                foreach ($validated['items'] as $entry) {
                    $item = Item::lockForUpdate()->find($entry['item_id']);
                    if (!$item) continue;

                    $systemStock = (int) $item->current_stock;
                    $physicalStock = (int) $entry['physical_stock'];
                    $diff = $physicalStock - $systemStock;

                    $status = 'MATCH';
                    if ($diff > 0) {
                        $status = 'PLUS';
                        $mismatchedCount++;
                    } elseif ($diff < 0) {
                        $status = 'MINUS';
                        $mismatchedCount++;
                    } else {
                        $matchedCount++;
                    }

                    $totalItems++;

                    // Update stok sistem jika terjadi selisih fisik riil
                    if ($diff !== 0) {
                        $item->update(['current_stock' => $physicalStock]);
                    }

                    $detailsToInsert[] = [
                        'item' => $item,
                        'item_id' => $item->id,
                        'system_stock' => $systemStock,
                        'physical_stock' => $physicalStock,
                        'difference' => $diff,
                        'status' => $status,
                        'notes' => $entry['notes'] ?? null,
                    ];
                }

                $opnNumber = StockOpname::generateOpnameNumber();
                $opname = StockOpname::create([
                    'opname_number' => $opnNumber,
                    'opname_date' => $validated['opname_date'],
                    'status' => 'COMPLETED',
                    'total_items' => $totalItems,
                    'total_matched' => $matchedCount,
                    'total_mismatched' => $mismatchedCount,
                    'notes' => $validated['notes'] ?? null,
                    'user_id' => auth()->id(),
                    'conducted_by' => $validated['conducted_by'],
                ]);

                foreach ($detailsToInsert as $detail) {
                    $itemObj = $detail['item'];
                    unset($detail['item']);

                    $opname->details()->create($detail);

                    // Hanya buat Ledger jika terdapat selisih fisik yang direkonsiliasi
                    if ($detail['difference'] !== 0) {
                        $sign = $detail['difference'] > 0 ? "+{$detail['difference']}" : "{$detail['difference']}";
                        StockLedger::create([
                            'item_id' => $detail['item_id'],
                            'transaction_type' => 'OPNAME',
                            'quantity' => abs($detail['difference']),
                            'balance_before' => $detail['system_stock'],
                            'balance_after' => $detail['physical_stock'],
                            'reference_type' => StockOpname::class,
                            'reference_id' => $opname->id,
                            'reference_number' => $opname->opname_number,
                            'user_id' => auth()->id(),
                            'description' => "Rekonsiliasi Sesi Opname: Selisih {$sign} {$itemObj->effective_small_unit} ({$detail['status']}). Tim: {$opname->conducted_by}",
                        ]);
                    }
                }

                // Catat Log Audit Sesuai PRD Seksi 14 & 28
                AuditLog::record(
                    action: 'OPNAME',
                    module: 'Kendali Stok',
                    description: "Rekonsiliasi stock opname no. {$opname->opname_number} ({$opname->total_items} item fisik diperiksa, {$opname->total_mismatched} selisih disesuaikan). Tim: {$opname->conducted_by}",
                    recordType: StockOpname::class,
                    recordId: $opname->id,
                    newValues: [
                        'opname_number' => $opname->opname_number,
                        'conducted_by' => $opname->conducted_by,
                        'total_items' => $opname->total_items,
                        'matched' => $opname->total_matched,
                        'mismatched' => $opname->total_mismatched,
                    ]
                );

                return $opname;
            });

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Sesi Opname {$createdOpname->opname_number} berhasil direkonsiliasi.",
                    'opname_number' => $createdOpname->opname_number,
                    'matched' => $createdOpname->total_matched,
                    'mismatched' => $createdOpname->total_mismatched,
                ]);
            }

            return redirect()->route('inventory.stock-control.index', ['tab' => 'opname'])
                ->with('success', "Sesi Stock Opname {$createdOpname->opname_number} berhasil direkonsiliasi.");

        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem saat memproses opname: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Ambil Data Detail Penyesuaian Stok (JSON untuk Modal Review)
     */
    public function showAdjustment(StockAdjustment $stockAdjustment): JsonResponse
    {
        $stockAdjustment->load(['user', 'details.item.category', 'details.item.unitDetail']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $stockAdjustment->id,
                'adjustment_number' => $stockAdjustment->adjustment_number,
                'adjustment_date' => $stockAdjustment->adjustment_date->format('d/m/Y'),
                'type' => $stockAdjustment->type,
                'reason' => $stockAdjustment->reason,
                'notes' => $stockAdjustment->notes,
                'officer_name' => $stockAdjustment->user->name ?? 'Petugas ATK',
                'total_items' => $stockAdjustment->total_items,
                'details' => $stockAdjustment->details->map(function ($d) {
                    return [
                        'item_code' => $d->item->code ?? '-',
                        'item_name' => $d->item->name ?? '-',
                        'category' => $d->item->category->name ?? '-',
                        'unit' => $d->unit,
                        'system_stock' => $d->system_stock,
                        'actual_stock' => $d->actual_stock,
                        'difference' => $d->difference,
                        'notes' => $d->notes ?? '-',
                    ];
                }),
            ],
        ]);
    }

    /**
     * Ambil Data Detail Sesi Stock Opname (JSON untuk Modal Review / Berita Acara)
     */
    public function showOpname(StockOpname $stockOpname): JsonResponse
    {
        $stockOpname->load(['user', 'details.item.category', 'details.item.unitDetail']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $stockOpname->id,
                'opname_number' => $stockOpname->opname_number,
                'opname_date' => $stockOpname->opname_date->format('d/m/Y'),
                'status' => $stockOpname->status,
                'conducted_by' => $stockOpname->conducted_by ?? '-',
                'officer_name' => $stockOpname->user->name ?? 'Petugas ATK',
                'total_items' => $stockOpname->total_items,
                'total_matched' => $stockOpname->total_matched,
                'total_mismatched' => $stockOpname->total_mismatched,
                'notes' => $stockOpname->notes,
                'details' => $stockOpname->details->map(function ($d) {
                    return [
                        'item_code' => $d->item->code ?? '-',
                        'item_name' => $d->item->name ?? '-',
                        'category' => $d->item->category->name ?? '-',
                        'small_unit' => $d->item->effective_small_unit ?? 'Pcs',
                        'system_stock' => $d->system_stock,
                        'physical_stock' => $d->physical_stock,
                        'difference' => $d->difference,
                        'status' => $d->status,
                        'notes' => $d->notes ?? '-',
                    ];
                }),
            ],
        ]);
    }
}
