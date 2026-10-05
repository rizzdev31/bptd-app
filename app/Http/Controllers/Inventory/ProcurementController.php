<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Item;
use App\Models\Procurement;
use App\Models\ProcurementDetail;
use App\Models\StockIn;
use App\Models\StockInDetail;
use App\Models\StockLedger;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProcurementController extends Controller
{
    /**
     * Tampilan Utama Pengadaan ATK & Penerimaan Stok
     * Memiliki 3 Tab:
     * 1. Pengadaan & PO (Procurements)
     * 2. Riwayat Penerimaan Fisik (Stock In Log)
     * 3. Rekanan / Supplier Penyedia
     */
    public function index(Request $request): View
    {
        $activeTab = $request->query('tab', 'procurement');

        // 1. Data Tab Pengadaan
        $procurementQuery = Procurement::with(['details.item', 'supplier', 'user', 'receiver'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('q_procurement')) {
            $search = trim($request->query('q_procurement'));
            $procurementQuery->where(function ($q) use ($search) {
                $q->where('procurement_number', 'like', "%{$search}%")
                    ->orWhere('supplier_name', 'like', "%{$search}%")
                    ->orWhere('invoice_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_filter') && $request->query('status_filter') !== 'all') {
            $procurementQuery->where('status', $request->query('status_filter'));
        }

        if ($request->filled('supplier_filter') && $request->query('supplier_filter') !== 'all') {
            $procurementQuery->where('supplier_id', $request->query('supplier_filter'));
        }

        if ($request->filled('date_from')) {
            $procurementQuery->whereDate('date', '>=', $request->query('date_from'));
        }

        if ($request->filled('date_to')) {
            $procurementQuery->whereDate('date', '<=', $request->query('date_to'));
        }

        $procurements = $procurementQuery->paginate(12, ['*'], 'procurements_page')
            ->withQueryString();

        // 2. Data Tab Riwayat Stock In (Penerimaan Fisik)
        $stockInQuery = StockIn::with(['details.item', 'supplier', 'user', 'procurement'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('q_stock_in')) {
            $search = trim($request->query('q_stock_in'));
            $stockInQuery->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhere('supplier_name', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('source_filter') && $request->query('source_filter') !== 'all') {
            $stockInQuery->where('source', $request->query('source_filter'));
        }

        $stockIns = $stockInQuery->paginate(12, ['*'], 'stock_ins_page')
            ->withQueryString();

        // 3. Data Tab Rekanan / Supplier
        $supplierQuery = Supplier::withCount('items')
            ->orderBy('name', 'asc');

        if ($request->filled('q_supplier')) {
            $search = trim($request->query('q_supplier'));
            $supplierQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $suppliers = $supplierQuery->paginate(12, ['*'], 'suppliers_page')
            ->withQueryString();

        // Quick Metrics Cards (Ringkasan Pengadaan & Gudang)
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyBudgetSpent = (float) Procurement::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->whereIn('status', ['ordered', 'received', 'completed'])
            ->sum('total_amount');

        $pendingReceiptCount = Procurement::where('status', 'ordered')->count();

        $monthlyPhysicalReceivedPieces = (int) StockIn::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('total_quantity');

        $activeSupplierCount = Supplier::where('status', 'active')->count();

        // Master data untuk dropdown modal
        $activeItems = Item::with(['category', 'unitDetail'])
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        $activeSuppliers = Supplier::where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        return view('inventory.procurement.index', compact(
            'activeTab',
            'procurements',
            'stockIns',
            'suppliers',
            'monthlyBudgetSpent',
            'pendingReceiptCount',
            'monthlyPhysicalReceivedPieces',
            'activeSupplierCount',
            'activeItems',
            'activeSuppliers'
        ));
    }

    /**
     * Menyimpan Pengadaan Baru (Draft / Ordered / Langsung Diterima)
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'supplier_name' => ['nullable', 'string', 'max:150'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:draft,ordered,received'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit' => ['required', 'string', 'max:50'],
            'items.*.conversion_factor' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ], [
            'date.required' => 'Tanggal pengadaan wajib diisi.',
            'status.required' => 'Status pengadaan wajib dipilih.',
            'items.required' => 'Minimal pilih 1 item barang yang diadakan.',
            'items.min' => 'Minimal pilih 1 item barang yang diadakan.',
            'items.*.quantity.min' => 'Kuantitas item minimal 1.',
        ]);

        try {
            $procurement = DB::transaction(function () use ($validated, $request) {
                // Generate Nomor Pengadaan PO-YYYYMMDD-XXXX
                $dateStr = date('Ymd', strtotime($validated['date']));
                $prefix = "PO-{$dateStr}-";
                $lastNumber = Procurement::where('procurement_number', 'like', "{$prefix}%")
                    ->lockForUpdate()
                    ->orderBy('procurement_number', 'desc')
                    ->value('procurement_number');

                $seq = 1;
                if ($lastNumber && preg_match('/-(\d{4})$/', $lastNumber, $matches)) {
                    $seq = (int) $matches[1] + 1;
                }
                $procurementNumber = $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);

                // Tentukan nama supplier snapshot
                $supplierName = $validated['supplier_name'] ?? null;
                if (!empty($validated['supplier_id'])) {
                    $supplierObj = Supplier::find($validated['supplier_id']);
                    if ($supplierObj) {
                        $supplierName = $supplierObj->name;
                    }
                }
                if (empty($supplierName)) {
                    $supplierName = 'Pengadaan Mandiri / Tanpa Rekanan';
                }

                $totalAmount = 0;
                $detailsToInsert = [];

                foreach ($validated['items'] as $row) {
                    $item = Item::findOrFail($row['item_id']);
                    $qty = (int) $row['quantity'];
                    $factor = (int) ($row['conversion_factor'] ?: 1);
                    $baseQty = $qty * $factor;
                    $unitPrice = (float) $row['unit_price'];
                    $subtotal = $qty * $unitPrice;
                    $totalAmount += $subtotal;

                    $detailsToInsert[] = [
                        'item_id' => $item->id,
                        'item_name' => $item->name,
                        'item_code' => $item->code,
                        'quantity' => $qty,
                        'unit' => $row['unit'],
                        'conversion_factor' => $factor,
                        'base_quantity' => $baseQty,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                        'notes' => $row['notes'] ?? null,
                    ];
                }

                // Buat Header Pengadaan
                $procurement = Procurement::create([
                    'procurement_number' => $procurementNumber,
                    'date' => $validated['date'],
                    'supplier_id' => $validated['supplier_id'] ?? null,
                    'supplier_name' => $supplierName,
                    'status' => $validated['status'],
                    'invoice_number' => $validated['invoice_number'] ?? null,
                    'total_amount' => $totalAmount,
                    'payment_status' => 'unpaid',
                    'notes' => $validated['notes'] ?? null,
                    'user_id' => Auth::id(),
                    'received_at' => $validated['status'] === 'received' ? now() : null,
                    'received_by' => $validated['status'] === 'received' ? Auth::id() : null,
                ]);

                // Simpan Rincian Detail
                foreach ($detailsToInsert as $detailData) {
                    $procurement->details()->create($detailData);
                }

                // Jika status langsung "received" (Diterima di Gudang), langsung jalankan mutasi stok fisik & ledger
                if ($validated['status'] === 'received') {
                    $this->executePhysicalReceipt($procurement);
                }

                // Catat Log Audit Pembuatan Pengadaan
                AuditLog::record(
                    action: 'CREATE',
                    module: 'Pengadaan',
                    description: "Pembuatan Surat Pesanan (PO) no. {$procurement->procurement_number} ke {$procurement->supplier_name} senilai Rp " . number_format($procurement->total_amount, 0, ',', '.') . " (" . count($detailsToInsert) . " item)",
                    recordType: Procurement::class,
                    recordId: $procurement->id,
                    newValues: [
                        'procurement_number' => $procurement->procurement_number,
                        'supplier' => $procurement->supplier_name,
                        'total_amount' => $procurement->total_amount,
                        'status' => $procurement->status,
                        'items_count' => count($detailsToInsert),
                    ]
                );

                return $procurement;
            });

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Pengadaan {$procurement->procurement_number} berhasil dibuat.",
                    'data' => $procurement->load(['details.item', 'supplier']),
                ]);
            }

            return redirect()->route('inventory.procurement.index', ['tab' => 'procurement'])
                ->with('toast_success', "Pengadaan {$procurement->procurement_number} berhasil disimpan!");
        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses pengadaan: ' . $e->getMessage(),
                ], 422);
            }

            return back()->withInput()->with('toast_error', 'Gagal memproses pengadaan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan Rincian Pengadaan (JSON untuk Modal Detail)
     */
    public function show(Procurement $procurement): JsonResponse
    {
        $procurement->load(['details.item.unitDetail', 'supplier', 'user', 'receiver', 'stockIns.details']);

        return response()->json([
            'success' => true,
            'data' => $procurement,
        ]);
    }

    /**
     * Konfirmasi Penerimaan Barang (Confirm Receipt)
     * Mengubah status dari 'ordered'/'draft' menjadi 'received'.
     * Menambah stok item di gudang dan mencatat kartu stok (Stock Ledger) serta bukti Stock In.
     * Mencegah receipt ganda sesuai PRD Seksi 23.
     */
    public function receive(Procurement $procurement, Request $request): JsonResponse|RedirectResponse
    {
        // Mencegah penerimaan ganda (PRD Seksi 23: "Sistem harus mencegah receipt ganda")
        if ($procurement->status === 'received' || $procurement->status === 'completed') {
            $msg = "Pengadaan {$procurement->procurement_number} sudah pernah diterima sebelumnya. Tidak dapat menerima ulang.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('toast_error', $msg);
        }

        if ($procurement->status === 'cancelled') {
            $msg = "Pengadaan {$procurement->procurement_number} berstatus dibatalkan.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('toast_error', $msg);
        }

        try {
            DB::transaction(function () use ($procurement, $request) {
                // Update Invoice Number jika diisi saat penerimaan fisik
                if ($request->filled('invoice_number')) {
                    $procurement->invoice_number = $request->input('invoice_number');
                }

                $procurement->status = 'received';
                $procurement->received_at = now();
                $procurement->received_by = Auth::id();
                $procurement->save();

                // Eksekusi mutasi fisik ke gudang
                $this->executePhysicalReceipt($procurement);
            });

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Barang pengadaan {$procurement->procurement_number} berhasil diterima dan stok gudang telah ditambahkan!",
                ]);
            }

            return redirect()->route('inventory.procurement.index', ['tab' => 'procurement'])
                ->with('toast_success', "Barang pengadaan {$procurement->procurement_number} berhasil diterima dan stok gudang telah diperbarui!");
        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengonfirmasi penerimaan: ' . $e->getMessage(),
                ], 422);
            }

            return back()->with('toast_error', 'Gagal mengonfirmasi penerimaan: ' . $e->getMessage());
        }
    }

    /**
     * Membatalkan Pengadaan (Hanya jika belum diterima)
     */
    public function cancel(Procurement $procurement, Request $request): JsonResponse|RedirectResponse
    {
        if ($procurement->status === 'received' || $procurement->status === 'completed') {
            $msg = "Pengadaan {$procurement->procurement_number} sudah diterima di gudang dan tidak dapat dibatalkan.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('toast_error', $msg);
        }

        $reason = $request->input('reason', 'Dibatalkan oleh petugas logistik');

        $procurement->update([
            'status' => 'cancelled',
            'notes' => trim(($procurement->notes ? $procurement->notes . "\n" : '') . "[PEMBATALAN]: " . $reason),
        ]);

        AuditLog::record(
            action: 'CANCEL',
            module: 'Pengadaan',
            description: "Pembatalan Surat Pesanan (PO) no. {$procurement->procurement_number}. Alasan: {$reason}",
            recordType: Procurement::class,
            recordId: $procurement->id,
            newValues: [
                'status' => 'cancelled',
                'reason' => $reason,
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Pengadaan {$procurement->procurement_number} berhasil dibatalkan.",
            ]);
        }

        return redirect()->route('inventory.procurement.index', ['tab' => 'procurement'])
            ->with('toast_success', "Pengadaan {$procurement->procurement_number} telah dibatalkan.");
    }

    /**
     * Tambah Rekanan / Supplier Cepat (AJAX Friendly)
     */
    public function storeSupplier(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['nullable', 'string', 'max:50', 'unique:suppliers,code'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama rekanan/perusahaan penyedia wajib diisi.',
            'code.unique' => 'Kode rekanan sudah digunakan.',
        ]);

        // Auto generate code if empty
        if (empty($validated['code'])) {
            $count = Supplier::count() + 1;
            $validated['code'] = 'SUP-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
        }

        $supplier = Supplier::create([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'contact_person' => $validated['contact_person'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'tax_number' => $validated['tax_number'] ?? null,
            'status' => 'active',
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Rekanan {$supplier->name} berhasil ditambahkan.",
                'data' => $supplier,
            ]);
        }

        return redirect()->route('inventory.procurement.index', ['tab' => 'supplier'])
            ->with('toast_success', "Rekanan {$supplier->name} berhasil ditambahkan!");
    }

    /**
     * Input Penerimaan Langsung (Direct Stock In)
     * Digunakan ketika barang diterima langsung di gudang tanpa PO formal
     * (Misal hibah, sisa kegiatan, atau pembelian langsung kas kecil).
     */
    public function storeDirectStockIn(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'source' => ['required', 'string', 'max:50'], // Direct, Hibah, Sisa Kegiatan, Lainnya
            'supplier_name' => ['nullable', 'string', 'max:150'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit' => ['required', 'string', 'max:50'],
            'items.*.conversion_factor' => ['required', 'integer', 'min:1'],
        ], [
            'date.required' => 'Tanggal penerimaan wajib diisi.',
            'items.required' => 'Pilih minimal 1 item barang yang masuk.',
            'items.min' => 'Pilih minimal 1 item barang yang masuk.',
        ]);

        try {
            $stockIn = DB::transaction(function () use ($validated) {
                // Generate Nomor IN-YYYYMMDD-XXXX
                $dateStr = date('Ymd', strtotime($validated['date']));
                $prefix = "IN-{$dateStr}-";
                $lastNumber = StockIn::where('transaction_number', 'like', "{$prefix}%")
                    ->lockForUpdate()
                    ->orderBy('transaction_number', 'desc')
                    ->value('transaction_number');

                $seq = 1;
                if ($lastNumber && preg_match('/-(\d{4})$/', $lastNumber, $matches)) {
                    $seq = (int) $matches[1] + 1;
                }
                $transactionNumber = $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);

                $totalItems = count($validated['items']);
                $totalQuantity = 0;

                $stockIn = StockIn::create([
                    'transaction_number' => $transactionNumber,
                    'date' => $validated['date'],
                    'source' => $validated['source'],
                    'procurement_id' => null,
                    'supplier_id' => null,
                    'supplier_name' => $validated['supplier_name'] ?: 'Penerimaan Langsung Gudang',
                    'reference_number' => $validated['reference_number'] ?? null,
                    'user_id' => Auth::id(),
                    'notes' => $validated['notes'] ?? null,
                    'total_items' => $totalItems,
                    'total_quantity' => 0,
                ]);

                foreach ($validated['items'] as $row) {
                    $item = Item::where('id', $row['item_id'])->lockForUpdate()->firstOrFail();
                    $qty = (int) $row['quantity'];
                    $factor = (int) ($row['conversion_factor'] ?: 1);
                    $baseQty = $qty * $factor;
                    $totalQuantity += $baseQty;

                    $stockIn->details()->create([
                        'item_id' => $item->id,
                        'item_name' => $item->name,
                        'item_code' => $item->code,
                        'quantity' => $qty,
                        'unit' => $row['unit'],
                        'conversion_factor' => $factor,
                        'base_quantity' => $baseQty,
                        'notes' => $row['notes'] ?? null,
                    ]);

                    $oldStock = (int) $item->current_stock;
                    $newStock = $oldStock + $baseQty;
                    $item->update(['current_stock' => $newStock]);

                    $smallUnit = $item->effective_small_unit ?: 'Pcs';
                    $ratioDesc = $factor > 1 
                        ? " ({$qty} {$row['unit']} @ {$factor} {$smallUnit}/{$row['unit']})" 
                        : " ({$baseQty} {$smallUnit})";

                    StockLedger::create([
                        'item_id' => $item->id,
                        'transaction_type' => 'IN',
                        'quantity' => $baseQty,
                        'balance_before' => $oldStock,
                        'balance_after' => $newStock,
                        'reference_type' => StockIn::class,
                        'reference_id' => $stockIn->id,
                        'reference_number' => $stockIn->transaction_number,
                        'user_id' => Auth::id(),
                        'description' => "Penerimaan Langsung ({$validated['source']}) #{$stockIn->transaction_number}: {$baseQty} {$smallUnit}{$ratioDesc}" . ($stockIn->reference_number ? " Ref: {$stockIn->reference_number}" : ''),
                    ]);
                }

                $stockIn->update(['total_quantity' => $totalQuantity]);

                // Catat Log Audit Penerimaan Langsung
                AuditLog::record(
                    action: 'STOCK_IN',
                    module: 'Penerimaan Barang',
                    description: "Penerimaan stok langsung no. {$stockIn->transaction_number} dari {$stockIn->supplier_name} ({$stockIn->total_quantity} unit fisik masuk gudang). Sumber: {$stockIn->source}",
                    recordType: StockIn::class,
                    recordId: $stockIn->id,
                    newValues: [
                        'transaction_number' => $stockIn->transaction_number,
                        'source' => $stockIn->source,
                        'supplier' => $stockIn->supplier_name,
                        'total_quantity' => $stockIn->total_quantity,
                    ]
                );

                return $stockIn;
            });

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Penerimaan stok langsung {$stockIn->transaction_number} berhasil dicatat!",
                    'data' => $stockIn,
                ]);
            }

            return redirect()->route('inventory.procurement.index', ['tab' => 'stock_in'])
                ->with('toast_success', "Penerimaan stok {$stockIn->transaction_number} berhasil disimpan!");
        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mencatat penerimaan stok: ' . $e->getMessage(),
                ], 422);
            }

            return back()->withInput()->with('toast_error', 'Gagal mencatat penerimaan: ' . $e->getMessage());
        }
    }

    /**
     * Cetak Dokumen Pengadaan & Penerimaan (Surat Pesanan PO / Berita Acara BAPHP)
     */
    public function print(Procurement $procurement, Request $request): View
    {
        $procurement->load(['details.item.unitDetail', 'supplier', 'user', 'receiver']);
        $type = $request->query('type', 'po'); // 'po' atau 'baphp'

        return view('inventory.procurement.print', compact('procurement', 'type'));
    }

    /**
     * Helper internal: Mengeksekusi mutasi fisik ke gudang dari data pengadaan
     */
    protected function executePhysicalReceipt(Procurement $procurement): StockIn
    {
        // 1. Generate Nomor Transaksi Stock In: IN-YYYYMMDD-XXXX
        $dateStr = date('Ymd');
        $prefix = "IN-{$dateStr}-";
        $lastNumber = StockIn::where('transaction_number', 'like', "{$prefix}%")
            ->lockForUpdate()
            ->orderBy('transaction_number', 'desc')
            ->value('transaction_number');

        $seq = 1;
        if ($lastNumber && preg_match('/-(\d{4})$/', $lastNumber, $matches)) {
            $seq = (int) $matches[1] + 1;
        }
        $transactionNumber = $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);

        $totalItems = $procurement->details->count();
        $totalQuantity = 0;

        // 2. Buat Transaksi Stock In
        $stockIn = StockIn::create([
            'transaction_number' => $transactionNumber,
            'date' => now()->toDateString(),
            'source' => 'Procurement',
            'procurement_id' => $procurement->id,
            'supplier_id' => $procurement->supplier_id,
            'supplier_name' => $procurement->supplier_name,
            'reference_number' => $procurement->invoice_number ?: $procurement->procurement_number,
            'user_id' => Auth::id(),
            'notes' => "Penerimaan hasil pengadaan #{$procurement->procurement_number}" . ($procurement->invoice_number ? " (Faktur: {$procurement->invoice_number})" : ''),
            'total_items' => $totalItems,
            'total_quantity' => 0,
        ]);

        // 3. Mutasi Fisik Tiap Detail Barang & Pencatatan Kartu Stok (Ledger)
        foreach ($procurement->details as $detail) {
            $item = Item::where('id', $detail->item_id)->lockForUpdate()->firstOrFail();
            $baseQty = (int) $detail->base_quantity;
            $totalQuantity += $baseQty;

            // Rincian Stock In
            $stockIn->details()->create([
                'item_id' => $item->id,
                'item_name' => $item->name,
                'item_code' => $item->code,
                'quantity' => $detail->quantity,
                'unit' => $detail->unit,
                'conversion_factor' => $detail->conversion_factor,
                'base_quantity' => $baseQty,
                'notes' => $detail->notes,
            ]);

            // Update Current Stock Item
            $oldStock = (int) $item->current_stock;
            $newStock = $oldStock + $baseQty;
            $item->update(['current_stock' => $newStock]);

            $smallUnit = $item->effective_small_unit ?: 'Pcs';
            $ratioDesc = $detail->conversion_factor > 1 
                ? " ({$detail->quantity} {$detail->unit} @ {$detail->conversion_factor} {$smallUnit}/{$detail->unit})" 
                : " ({$baseQty} {$smallUnit})";

            // Catat ke Stock Ledger (IN)
            StockLedger::create([
                'item_id' => $item->id,
                'transaction_type' => 'IN',
                'quantity' => $baseQty,
                'balance_before' => $oldStock,
                'balance_after' => $newStock,
                'reference_type' => Procurement::class,
                'reference_id' => $procurement->id,
                'reference_number' => $procurement->procurement_number,
                'user_id' => Auth::id(),
                'description' => "Penerimaan Pengadaan #{$procurement->procurement_number}: {$baseQty} {$smallUnit}{$ratioDesc} - Rekanan: {$procurement->supplier_name}" . ($procurement->invoice_number ? " [Faktur: {$procurement->invoice_number}]" : ''),
            ]);
        }

        $stockIn->update(['total_quantity' => $totalQuantity]);

        // Catat Log Audit Penerimaan Fisik
        AuditLog::record(
            action: 'STOCK_IN',
            module: 'Penerimaan Barang',
            description: "Penerimaan fisik barang pengadaan PO no. {$procurement->procurement_number} ({$totalQuantity} unit fisik masuk gudang). Rekanan: {$procurement->supplier_name}",
            recordType: Procurement::class,
            recordId: $procurement->id,
            newValues: [
                'procurement_number' => $procurement->procurement_number,
                'stock_in_number' => $stockIn->transaction_number,
                'total_quantity' => $totalQuantity,
                'supplier' => $procurement->supplier_name,
            ]
        );

        return $stockIn;
    }
}
