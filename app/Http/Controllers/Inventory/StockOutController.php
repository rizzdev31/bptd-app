<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Item;
use App\Models\Recipient;
use App\Models\StockLedger;
use App\Models\StockOut;
use App\Models\StockOutDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockOutController extends Controller
{
    /**
     * Tampilan Utama Kasir POS Distribusi & Riwayat Pengeluaran ATK
     */
    public function index(Request $request)
    {
        // 1. Data untuk Kasir POS (Produk & Penerima)
        $categories = Category::orderBy('name')->get();
        $recipients = Recipient::active()
            ->with('workUnit')
            ->orderBy('name')
            ->get();

        // Query Items aktif untuk POS Grid & Instant Search
        $itemsQuery = Item::active()
            ->with(['category', 'unitDetail'])
            ->orderBy('name');

        if ($request->filled('pos_category_id')) {
            $itemsQuery->where('category_id', $request->pos_category_id);
        }

        if ($request->filled('pos_keyword')) {
            $kw = $request->pos_keyword;
            $itemsQuery->where(function ($q) use ($kw) {
                $q->where('name', 'like', "%{$kw}%")
                  ->orWhere('code', 'like', "%{$kw}%")
                  ->orWhere('barcode', 'like', "%{$kw}%");
            });
        }

        $posItems = $itemsQuery->get();

        // 2. Query Riwayat Transaksi (History & SBPB)
        $historyQuery = StockOut::with(['details.item', 'recipient.workUnit', 'user'])
            ->orderByDesc('id');

        if ($request->filled('history_keyword')) {
            $hKw = $request->history_keyword;
            $historyQuery->where(function ($q) use ($hKw) {
                $q->where('transaction_number', 'like', "%{$hKw}%")
                  ->orWhere('recipient_name', 'like', "%{$hKw}%")
                  ->orWhere('recipient_nip', 'like', "%{$hKw}%")
                  ->orWhere('recipient_unit', 'like', "%{$hKw}%");
            });
        }

        if ($request->filled('date_from')) {
            $historyQuery->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $historyQuery->whereDate('transaction_date', '<=', $request->date_to);
        }

        if ($request->filled('filter_recipient_id')) {
            $historyQuery->where('recipient_id', $request->filter_recipient_id);
        }

        $stockOutHistory = $historyQuery->paginate(10)->withQueryString();

        // 3. Metrik Ringkasan Hari Ini
        $todayTransactionsCount = StockOut::whereDate('transaction_date', today())->count();
        $todayQuantityTotal = (int) StockOut::whereDate('transaction_date', today())->sum('total_quantity');
        $readyItemsCount = Item::active()->where('current_stock', '>', 0)->count();
        $nextTransactionNumber = StockOut::generateTransactionNumber();

        return view('inventory.stock_out.index', compact(
            'categories',
            'recipients',
            'posItems',
            'stockOutHistory',
            'todayTransactionsCount',
            'todayQuantityTotal',
            'readyItemsCount',
            'nextTransactionNumber'
        ));
    }

    /**
     * Eksekusi Transaksi Stock Out Atomik Sesuai PRD Seksi 10
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'recipient_id' => 'nullable|exists:recipients,id',
            'recipient_name' => 'required_without:recipient_id|nullable|string|max:255',
            'recipient_nip' => 'nullable|string|max:100',
            'recipient_unit' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.notes' => 'nullable|string|max:255',
        ], [
            'items.required' => 'Keranjang pengeluaran ATK belum berisi barang.',
            'items.min' => 'Pilih minimal 1 item barang yang dikeluarkan.',
            'recipient_name.required_without' => 'Pilih pegawai terdaftar atau isi nama penerima ATK.',
        ]);

        try {
            $stockOut = DB::transaction(function () use ($validated, $request) {
                // 1. Ambil data snapshot penerima
                $recipientName = $validated['recipient_name'] ?? '-';
                $recipientNip = $validated['recipient_nip'] ?? null;
                $recipientUnit = $validated['recipient_unit'] ?? null;
                $recipientPosition = null;

                if (!empty($validated['recipient_id'])) {
                    $rec = Recipient::with('workUnit')->find($validated['recipient_id']);
                    if ($rec) {
                        $recipientName = $rec->name;
                        $recipientNip = $rec->nip;
                        $recipientUnit = $rec->workUnit?->name ?? '-';
                        $recipientPosition = $rec->position;
                    }
                }

                // 2. Kunci baris item untuk mencegah race condition (lockForUpdate)
                $itemIds = collect($validated['items'])->pluck('item_id')->unique()->toArray();
                $items = Item::whereIn('id', $itemIds)->lockForUpdate()->get()->keyBy('id');

                $totalDistinctItems = 0;
                $totalUnitsCount = 0;
                $detailsToInsert = [];

                foreach ($validated['items'] as $entry) {
                    $itemId = $entry['item_id'];
                    $inputQty = (int) $entry['quantity'];
                    $requestedUnit = !empty($entry['unit']) ? trim($entry['unit']) : null;

                    /** @var Item|null $item */
                    $item = $items->get($itemId);

                    if (!$item) {
                        throw ValidationException::withMessages([
                            'items' => "Barang dengan ID {$itemId} tidak ditemukan.",
                        ]);
                    }

                    if ($item->status !== 'active') {
                        throw ValidationException::withMessages([
                            'items' => "Barang '{$item->name}' berstatus nonaktif dan tidak dapat didistribusikan.",
                        ]);
                    }

                    // Logika Konversi Satuan Bertingkat (Lusin/Box/Pack vs Pcs/Eceran)
                    $rate = max(1, (int) $item->conversion_rate);
                    $primaryUnit = $item->unit ?: 'Pcs';
                    $smallUnit = $item->effective_small_unit;

                    if ($requestedUnit && strcasecmp($requestedUnit, $primaryUnit) === 0 && $rate > 1) {
                        // Satuan kemasan dipilih (misal 1 Lusin = 12 Pcs)
                        $conversionFactor = $rate;
                        $unitLabel = $primaryUnit;
                    } else {
                        // Satuan eceran dipilih (misal 1 atau 2 Pcs)
                        $conversionFactor = 1;
                        $unitLabel = $requestedUnit ?: $smallUnit;
                    }

                    $baseQtyDeducted = $inputQty * $conversionFactor;

                    if ($item->current_stock < $baseQtyDeducted) {
                        throw ValidationException::withMessages([
                            'items' => "Stok barang '{$item->name}' tidak mencukupi. Tersedia: {$item->formatted_stock}, diminta: {$inputQty} {$unitLabel} (setara {$baseQtyDeducted} {$smallUnit}).",
                        ]);
                    }

                    $stockBefore = $item->current_stock;
                    $stockAfter = $stockBefore - $baseQtyDeducted;

                    // Update current_stock fisik barang di database (disimpan dalam satuan terkecil/base unit)
                    $item->current_stock = $stockAfter;
                    $item->save();

                    $detailsToInsert[] = [
                        'item_id' => $item->id,
                        'item_code' => $item->code,
                        'item_name' => $item->name,
                        'quantity' => $inputQty,
                        'unit' => $unitLabel,
                        'conversion_factor' => $conversionFactor,
                        'base_quantity' => $baseQtyDeducted,
                        'current_stock_before' => $stockBefore,
                        'current_stock_after' => $stockAfter,
                        'notes' => $entry['notes'] ?? null,
                    ];

                    $totalDistinctItems++;
                    $totalUnitsCount += $baseQtyDeducted;
                }

                // 3. Buat Header Stock Out
                $txNumber = StockOut::generateTransactionNumber();
                $stockOut = StockOut::create([
                    'transaction_number' => $txNumber,
                    'transaction_date' => $validated['transaction_date'],
                    'recipient_id' => $validated['recipient_id'] ?? null,
                    'recipient_name' => $recipientName,
                    'recipient_nip' => $recipientNip,
                    'recipient_unit' => $recipientUnit,
                    'recipient_position' => $recipientPosition,
                    'user_id' => auth()->id(),
                    'total_items' => $totalDistinctItems,
                    'total_quantity' => $totalUnitsCount,
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'completed',
                ]);

                // 4. Buat Details & Stock Ledger Entries
                foreach ($detailsToInsert as $detail) {
                    $stockOut->details()->create($detail);

                    $unitDesc = $detail['conversion_factor'] > 1 
                        ? "{$detail['quantity']} {$detail['unit']} (setara {$detail['base_quantity']} " . ($item->effective_small_unit ?? 'Pcs') . ")"
                        : "{$detail['quantity']} {$detail['unit']}";

                    StockLedger::create([
                        'item_id' => $detail['item_id'],
                        'transaction_type' => 'OUT',
                        'quantity' => $detail['base_quantity'],
                        'balance_before' => $detail['current_stock_before'],
                        'balance_after' => $detail['current_stock_after'],
                        'reference_type' => StockOut::class,
                        'reference_id' => $stockOut->id,
                        'reference_number' => $stockOut->transaction_number,
                        'user_id' => auth()->id(),
                        'description' => "Distribusi ATK: {$unitDesc} kepada {$stockOut->recipient_name}" . ($stockOut->recipient_unit ? " ({$stockOut->recipient_unit})" : ""),
                    ]);
                }

                // 5. Catat Log Audit Sesuai PRD Seksi 7, 10 & 28
                AuditLog::record(
                    action: 'STOCK_OUT',
                    module: 'Permintaan ATK',
                    description: "Pengeluaran ATK no. {$stockOut->transaction_number} kepada {$stockOut->recipient_name} ({$stockOut->recipient_unit}) total {$stockOut->total_quantity} item fisik",
                    recordType: StockOut::class,
                    recordId: $stockOut->id,
                    newValues: [
                        'transaction_number' => $stockOut->transaction_number,
                        'recipient' => $stockOut->recipient_name,
                        'unit' => $stockOut->recipient_unit,
                        'total_quantity' => $stockOut->total_quantity,
                        'items' => collect($detailsToInsert)->map(fn($d) => "{$d['item_name']} ({$d['quantity']} {$d['unit']})")->toArray(),
                    ]
                );

                return $stockOut;
            });

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi pengeluaran ATK berhasil disimpan dan stok telah diperbarui.',
                    'transaction_number' => $stockOut->transaction_number,
                    'stock_out_id' => $stockOut->id,
                    'redirect_print' => route('inventory.stock-out.print', $stockOut),
                ]);
            }

            return redirect()->route('inventory.stock-out.index', ['tab' => 'history'])
                ->with('success', "Transaksi pengeluaran {$stockOut->transaction_number} berhasil diproses.");

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $th) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem saat memproses transaksi: ' . $th->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', 'Gagal memproses transaksi: ' . $th->getMessage());
        }
    }

    /**
     * Detail Transaksi Stock Out (JSON untuk Modal Pratinjau)
     */
    public function show(StockOut $stockOut): JsonResponse
    {
        $stockOut->load([
            'details.item.unitDetail',
            'recipient.workUnit',
            'user'
        ]);

        return response()->json([
            'success' => true,
            'data' => $stockOut,
            'print_url' => route('inventory.stock-out.print', $stockOut),
        ]);
    }

    /**
     * Cetak Surat Bukti Pengeluaran Barang (SBPB) / Berita Acara Distribusi ATK
     */
    public function print(StockOut $stockOut)
    {
        $stockOut->load([
            'details.item',
            'recipient.workUnit',
            'user'
        ]);

        return view('inventory.stock_out.print', compact('stockOut'));
    }
}
