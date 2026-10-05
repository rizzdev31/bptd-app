<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\Procurement;
use App\Models\Recipient;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Supplier;
use App\Models\WorkUnit;
use App\Services\ReportExcelExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Tampilan Utama Laporan Persediaan & Mutasi ATK
     * Mendukung 5 Mode Laporan:
     * 1. stock (Laporan Persediaan & Posisi Stok Fisik)
     * 2. stock_out (Laporan Permintaan & Pengeluaran ATK)
     * 3. stock_in (Laporan Penerimaan Barang Masuk)
     * 4. procurement (Laporan Pengadaan & Anggaran Belanja)
     * 5. unit_usage (Laporan Distribusi per Seksi / Unit Kerja)
     */
    public function index(Request $request): View
    {
        $reportType = $request->query('type', 'stock');

        // Parameter Filter Umum
        $dateFrom = $request->query('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->query('date_to', now()->toDateString());
        $search = trim($request->query('q', ''));

        // Metadata Filter Dropdown
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        $workUnits = WorkUnit::where('status', 'active')->orderBy('name')->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $recipients = Recipient::where('status', 'active')->orderBy('name')->get();

        // 1. Eksekusi Query Sesuai Tipe Laporan
        $reportData = match ($reportType) {
            'stock_out' => $this->getStockOutData($request, $dateFrom, $dateTo, $search, true),
            'stock_in' => $this->getStockInData($request, $dateFrom, $dateTo, $search, true),
            'procurement' => $this->getProcurementData($request, $dateFrom, $dateTo, $search, true),
            'unit_usage' => $this->getUnitUsageData($request, $dateFrom, $dateTo, $search),
            default => $this->getStockData($request, $search, true),
        };

        // 2. Metrik Ringkasan Global
        $totalItems = Item::count();
        $totalCurrentStock = (int) Item::sum('current_stock');
        $lowStockItems = Item::lowStock()->count();
        $outOfStockItems = Item::outOfStock()->count();

        $monthStockOutQuantity = (int) StockOut::whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('total_quantity');

        $monthStockInQuantity = (int) StockIn::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('total_quantity');

        $monthProcurementSpent = (float) Procurement::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->whereIn('status', ['ordered', 'received', 'completed'])
            ->sum('total_amount');

        return view('inventory.report.index', compact(
            'reportType',
            'reportData',
            'dateFrom',
            'dateTo',
            'search',
            'categories',
            'workUnits',
            'suppliers',
            'recipients',
            'totalItems',
            'totalCurrentStock',
            'lowStockItems',
            'outOfStockItems',
            'monthStockOutQuantity',
            'monthStockInQuantity',
            'monthProcurementSpent'
        ));
    }

    /**
     * Ekspor Laporan ke Format Excel Asli (.xlsx) dengan Pengaturan Cetak A4 Tersistematisasi
     * Mengikuti seluruh parameter filter yang sedang aktif
     */
    public function exportExcel(Request $request, ReportExcelExportService $excelService): StreamedResponse
    {
        $reportType = $request->query('type', 'stock');
        $dateFrom = $request->query('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->query('date_to', now()->toDateString());
        $search = trim($request->query('q', ''));

        // Ambil data penuh tanpa paginasi sesuai filter aktif
        $data = match ($reportType) {
            'stock_out' => $this->getStockOutData($request, $dateFrom, $dateTo, $search, false),
            'stock_in' => $this->getStockInData($request, $dateFrom, $dateTo, $search, false),
            'procurement' => $this->getProcurementData($request, $dateFrom, $dateTo, $search, false),
            'unit_usage' => $this->getUnitUsageData($request, $dateFrom, $dateTo, $search),
            default => $this->getStockData($request, $search, false),
        };

        $title = $this->getReportTitle($reportType);

        return $excelService->export($reportType, $data, $title, $dateFrom, $dateTo);
    }

    /**
     * Ekspor Laporan ke Format CSV (UTF-8 BOM untuk Spreadsheet)
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $reportType = $request->query('type', 'stock');
        $dateFrom = $request->query('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->query('date_to', now()->toDateString());
        $search = trim($request->query('q', ''));

        $filename = "Laporan_{$reportType}_" . date('Ymd_His') . ".csv";

        return response()->streamDownload(function () use ($reportType, $request, $dateFrom, $dateTo, $search) {
            $handle = fopen('php://output', 'w');
            
            // Tulis UTF-8 BOM agar Excel membaca karakter khusus dengan benar
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            switch ($reportType) {
                case 'stock_out':
                    fputcsv($handle, ['No. Transaksi', 'Tanggal', 'Penerima', 'NIP', 'Unit Kerja', 'Item Barang', 'Kuantitas Kemasan', 'Fisik (Pcs)', 'Petugas', 'Catatan/Keperluan']);
                    $records = $this->getStockOutData($request, $dateFrom, $dateTo, $search, false);
                    foreach ($records as $row) {
                        foreach ($row->details as $d) {
                            fputcsv($handle, [
                                $row->transaction_number,
                                $row->transaction_date->format('Y-m-d'),
                                $row->recipient_name,
                                $row->recipient_nip ?: '-',
                                $row->recipient_unit ?: '-',
                                $d->item_name,
                                "{$d->quantity} {$d->unit}",
                                $d->base_quantity,
                                $row->user?->name ?? 'Admin',
                                $row->notes ?: '-',
                            ]);
                        }
                    }
                    break;

                case 'stock_in':
                    fputcsv($handle, ['No. Transaksi', 'Tanggal', 'Sumber', 'Supplier/Rekanan', 'No. Referensi', 'Item Barang', 'Jumlah Masuk (Pcs)', 'Petugas', 'Catatan']);
                    $records = $this->getStockInData($request, $dateFrom, $dateTo, $search, false);
                    foreach ($records as $row) {
                        foreach ($row->details as $d) {
                            fputcsv($handle, [
                                $row->transaction_number,
                                $row->date->format('Y-m-d'),
                                $row->source,
                                $row->supplier_name ?: '-',
                                $row->reference_number ?: '-',
                                $d->item_name,
                                $d->base_quantity,
                                $row->user?->name ?? 'Petugas Gudang',
                                $row->notes ?: '-',
                            ]);
                        }
                    }
                    break;

                case 'procurement':
                    fputcsv($handle, ['No. PO', 'Tanggal', 'Rekanan Penyedia', 'No. Faktur/SPK', 'Total Anggaran (Rp)', 'Status', 'Petugas Pengadaan', 'Catatan']);
                    $records = $this->getProcurementData($request, $dateFrom, $dateTo, $search, false);
                    foreach ($records as $row) {
                        fputcsv($handle, [
                            $row->procurement_number,
                            $row->date->format('Y-m-d'),
                            $row->supplier_name,
                            $row->invoice_number ?: '-',
                            (float) $row->total_amount,
                            $row->status_label,
                            $row->user?->name ?? 'Admin',
                            $row->notes ?: '-',
                        ]);
                    }
                    break;

                case 'unit_usage':
                    fputcsv($handle, ['Unit Kerja / Seksi', 'Total Pengajuan (SBPB)', 'Total Kuantitas Didistribusikan (Pcs)', 'Ragam Item Diminta']);
                    $records = $this->getUnitUsageData($request, $dateFrom, $dateTo, $search);
                    foreach ($records as $row) {
                        fputcsv($handle, [
                            $row->unit_name,
                            $row->total_requests,
                            $row->total_pieces,
                            $row->unique_items_count,
                        ]);
                    }
                    break;

                default: // stock
                    fputcsv($handle, ['Kode SKU', 'Nama Barang ATK', 'Kategori', 'Satuan Kemasan', 'Satuan Eceran', 'Stok Fisik Terkini', 'Batas Minimum', 'Target Stok', 'Status Ketersediaan', 'Lokasi Simpan']);
                    $records = $this->getStockData($request, $search, false);
                    foreach ($records as $row) {
                        fputcsv($handle, [
                            $row->code,
                            $row->name,
                            $row->category?->name ?? '-',
                            $row->unit ?: 'Pcs',
                            $row->effective_small_unit,
                            $row->current_stock,
                            $row->minimum_stock,
                            $row->target_stock,
                            $row->stock_status_label,
                            $row->storage_location ?: 'Gudang Utama',
                        ]);
                    }
                    break;
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Tampilan Cetak Resmi / PDF Laporan (A4 Landscape / Portrait)
     */
    public function print(Request $request): View
    {
        $reportType = $request->query('type', 'stock');
        $dateFrom = $request->query('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->query('date_to', now()->toDateString());
        $search = trim($request->query('q', ''));

        $data = match ($reportType) {
            'stock_out' => $this->getStockOutData($request, $dateFrom, $dateTo, $search, false),
            'stock_in' => $this->getStockInData($request, $dateFrom, $dateTo, $search, false),
            'procurement' => $this->getProcurementData($request, $dateFrom, $dateTo, $search, false),
            'unit_usage' => $this->getUnitUsageData($request, $dateFrom, $dateTo, $search),
            default => $this->getStockData($request, $search, false),
        };

        $title = $this->getReportTitle($reportType);

        return view('inventory.report.print', compact(
            'reportType',
            'data',
            'title',
            'dateFrom',
            'dateTo',
            'search'
        ));
    }

    // ==============================================================
    // HELPER QUERY INTERNAL UNTUK MASING-MASING LAPORAN
    // ==============================================================

    protected function getStockData(Request $request, string $search, bool $paginate)
    {
        $query = Item::with(['category', 'unitDetail'])
            ->orderBy('name', 'asc');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('storage_location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id') && $request->query('category_id') !== 'all') {
            $query->where('category_id', $request->query('category_id'));
        }

        if ($request->filled('status_stock') && $request->query('status_stock') !== 'all') {
            $status = $request->query('status_stock');
            if ($status === 'out_of_stock') {
                $query->outOfStock();
            } elseif ($status === 'low_stock') {
                $query->lowStock();
            } elseif ($status === 'available') {
                $query->whereColumn('current_stock', '>', 'minimum_stock');
            }
        }

        return $paginate ? $query->paginate(15)->withQueryString() : $query->get();
    }

    protected function getStockOutData(Request $request, string $dateFrom, string $dateTo, string $search, bool $paginate)
    {
        $query = StockOut::with(['details.item', 'recipient.workUnit', 'user'])
            ->whereDate('transaction_date', '>=', $dateFrom)
            ->whereDate('transaction_date', '<=', $dateTo)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhere('recipient_nip', 'like', "%{$search}%")
                    ->orWhere('recipient_unit', 'like', "%{$search}%");
            });
        }

        if ($request->filled('recipient_id') && $request->query('recipient_id') !== 'all') {
            $query->where('recipient_id', $request->query('recipient_id'));
        }

        if ($request->filled('work_unit') && $request->query('work_unit') !== 'all') {
            $query->where('recipient_unit', $request->query('work_unit'));
        }

        return $paginate ? $query->paginate(15)->withQueryString() : $query->get();
    }

    protected function getStockInData(Request $request, string $dateFrom, string $dateTo, string $search, bool $paginate)
    {
        $query = StockIn::with(['details.item', 'supplier', 'user', 'procurement'])
            ->whereDate('date', '>=', $dateFrom)
            ->whereDate('date', '<=', $dateTo)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhere('supplier_name', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('source') && $request->query('source') !== 'all') {
            $query->where('source', $request->query('source'));
        }

        if ($request->filled('supplier_id') && $request->query('supplier_id') !== 'all') {
            $query->where('supplier_id', $request->query('supplier_id'));
        }

        return $paginate ? $query->paginate(15)->withQueryString() : $query->get();
    }

    protected function getProcurementData(Request $request, string $dateFrom, string $dateTo, string $search, bool $paginate)
    {
        $query = Procurement::with(['details.item', 'supplier', 'user', 'receiver'])
            ->whereDate('date', '>=', $dateFrom)
            ->whereDate('date', '<=', $dateTo)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('procurement_number', 'like', "%{$search}%")
                    ->orWhere('supplier_name', 'like', "%{$search}%")
                    ->orWhere('invoice_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->query('status') !== 'all') {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('supplier_id') && $request->query('supplier_id') !== 'all') {
            $query->where('supplier_id', $request->query('supplier_id'));
        }

        return $paginate ? $query->paginate(15)->withQueryString() : $query->get();
    }

    protected function getUnitUsageData(Request $request, string $dateFrom, string $dateTo, string $search)
    {
        // Agregasi konsumsi persediaan per Unit Kerja / Seksi
        $stockOuts = StockOut::with('details')
            ->whereDate('transaction_date', '>=', $dateFrom)
            ->whereDate('transaction_date', '<=', $dateTo)
            ->get();

        $grouped = [];

        foreach ($stockOuts as $so) {
            $unitName = $so->recipient_unit ?: 'Seksi / Unit Kerja Lain';
            
            if ($search !== '' && stripos($unitName, $search) === false) {
                continue;
            }

            if ($request->filled('work_unit') && $request->query('work_unit') !== 'all' && $unitName !== $request->query('work_unit')) {
                continue;
            }

            if (!isset($grouped[$unitName])) {
                $grouped[$unitName] = (object) [
                    'unit_name' => $unitName,
                    'total_requests' => 0,
                    'total_pieces' => 0,
                    'items_map' => [],
                ];
            }

            $grouped[$unitName]->total_requests++;
            $grouped[$unitName]->total_pieces += (int) $so->total_quantity;

            foreach ($so->details as $d) {
                $grouped[$unitName]->items_map[$d->item_id] = ($grouped[$unitName]->items_map[$d->item_id] ?? 0) + $d->base_quantity;
            }
        }

        foreach ($grouped as $g) {
            $g->unique_items_count = count($g->items_map);
        }

        usort($grouped, fn($a, $b) => $b->total_pieces <=> $a->total_pieces);

        return collect($grouped);
    }

    protected function getReportTitle(string $reportType): string
    {
        return match ($reportType) {
            'stock_out' => 'Laporan Permintaan & Pengeluaran ATK (SBPB)',
            'stock_in' => 'Laporan Penerimaan Fisik Barang Masuk',
            'procurement' => 'Laporan Pengadaan & Belanja ATK',
            'unit_usage' => 'Laporan Konsumsi ATK per Unit Kerja / Seksi',
            default => 'Laporan Posisi Persediaan & Stok Gudang ATK',
        };
    }
}
