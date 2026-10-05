<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditController extends Controller
{
    /**
     * Halaman Utama Audit Trail & Log Riwayat Aktivitas (PRD Seksi 28 & 52)
     */
    public function index(Request $request): View
    {
        $search = trim($request->query('q', ''));
        $module = $request->query('module', '');
        $action = $request->query('action', '');
        $userId = $request->query('user_id', '');
        $dateFrom = $request->query('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->query('date_to', now()->toDateString());
        $viewMode = $request->query('view', 'table'); // 'table' atau 'timeline'

        $query = AuditLog::with('user.role');

        // Filter Pencarian Teks Bebas
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('user_nip', 'like', "%{$search}%")
                  ->orWhere('record_id', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        // Filter Modul
        if ($module !== '') {
            $query->where('module', $module);
        }

        // Filter Aksi
        if ($action !== '') {
            $query->where('action', $action);
        }

        // Filter Pengguna Pelaksana
        if ($userId !== '') {
            $query->where('user_id', $userId);
        }

        // Filter Rentang Tanggal
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Paginated Logs
        $logs = (clone $query)->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        // 4 Metrik Ringkasan Audit
        $totalLogs = AuditLog::count();
        $stockMutationLogs = AuditLog::whereIn('action', ['STOCK_OUT', 'STOCK_IN', 'ADJUSTMENT', 'OPNAME'])->count();
        $todayLogs = AuditLog::whereDate('created_at', today())->count();
        $uniqueUsersCount = AuditLog::distinct('user_id')->count('user_id');

        // Dropdown List Filters
        $availableModules = [
            'Master ATK',
            'Permintaan ATK',
            'Kendali Stok',
            'Pengadaan',
            'Penerimaan Barang',
            'Pegawai',
            'Role & Hak Akses',
            'Autentikasi',
        ];

        $availableActions = [
            'CREATE' => 'Tambah Data (CREATE)',
            'UPDATE' => 'Perubahan Data (UPDATE)',
            'STATUS_CHANGE' => 'Ubah Status',
            'STOCK_OUT' => 'Distribusi SBPB (STOCK OUT)',
            'STOCK_IN' => 'Penerimaan Stok (STOCK IN)',
            'ADJUSTMENT' => 'Penyesuaian Stok (ADJUSTMENT)',
            'OPNAME' => 'Stock Opname (OPNAME)',
            'CANCEL' => 'Pembatalan (CANCEL)',
            'LOGIN' => 'Login Masuk',
            'LOGOUT' => 'Logout Keluar',
        ];

        $users = User::orderBy('name')->get();

        return view('inventory.audit.index', compact(
            'logs',
            'totalLogs',
            'stockMutationLogs',
            'todayLogs',
            'uniqueUsersCount',
            'availableModules',
            'availableActions',
            'users',
            'search',
            'module',
            'action',
            'userId',
            'dateFrom',
            'dateTo',
            'viewMode'
        ));
    }

    /**
     * Detail JSON untuk Modal Inspeksi Log (Audit Inspector Modal)
     */
    public function show(AuditLog $auditLog): JsonResponse
    {
        $auditLog->load('user.role');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $auditLog->id,
                'action' => $auditLog->action,
                'action_label' => $auditLog->action_label,
                'action_badge_class' => $auditLog->action_badge_class,
                'module' => $auditLog->module,
                'module_badge_class' => $auditLog->module_badge_class,
                'description' => $auditLog->description,
                'record_type' => $auditLog->record_type,
                'record_id' => $auditLog->record_id,
                'old_values' => $auditLog->old_values,
                'new_values' => $auditLog->new_values,
                'has_diff' => !empty($auditLog->old_values) && !empty($auditLog->new_values),
                'ip_address' => $auditLog->ip_address ?: '127.0.0.1',
                'user_agent' => $auditLog->user_agent ?: 'Desktop Browser',
                'created_at_formatted' => $auditLog->created_at->translatedFormat('d F Y, H:i:s') . ' WIB',
                'created_at_relative' => $auditLog->created_at->diffForHumans(),
                'user' => [
                    'id' => $auditLog->user_id,
                    'name' => $auditLog->user_name ?: ($auditLog->user?->name ?? 'Sistem / Tamu'),
                    'nip' => $auditLog->user_nip ?: ($auditLog->user?->nip ?? '-'),
                    'role' => $auditLog->user?->role?->label ?? 'Petugas / User',
                ],
            ],
        ]);
    }

    /**
     * Ekspor Log Audit ke Excel (.xlsx) dengan Template Cetak A4 Tersistematisasi
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $records = $this->getFilteredLogs($request);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Audit Trail');

        // 1. SYSTEMIZED PAGE SETUP (A4 Landscape & Fit to 1 Page Wide)
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setPaperSize(PageSetup::PAPERSIZE_A4);
        $pageSetup->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $pageSetup->setFitToPage(true);
        $pageSetup->setFitToWidth(1);
        $pageSetup->setFitToHeight(0);

        $sheet->getPageMargins()->setTop(0.75);
        $sheet->getPageMargins()->setBottom(0.75);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setRight(0.5);

        $sheet->setShowGridlines(true);
        $sheet->setPrintGridlines(true);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);

        // 2. KOP RESMI PEMERINTAH (Baris 1 - 3)
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'KEMENTERIAN PERHUBUNGAN');
        $sheet->getStyle('A1')->getFont()->setName('Arial')->setSize(11)->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A2', 'BALAI PENGELOLA TRANSPORTASI DARAT KELAS II JAWA TIMUR');
        $sheet->getStyle('A2')->getFont()->setName('Arial')->setSize(13)->setBold(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $periodInfo = ($dateFrom && $dateTo) 
            ? "Periode: " . date('d/m/Y', strtotime($dateFrom)) . " s/d " . date('d/m/Y', strtotime($dateTo))
            : "Data Riwayat Aktivitas Sistem per " . date('d/m/Y, H:i') . " WIB";

        $sheet->mergeCells('A3:I3');
        $sheet->setCellValue('A3', 'LOG AUDIT TRAIL & REKAM JEJAK AKTIVITAS PERSADAAN (' . $periodInfo . ')');
        $sheet->getStyle('A3')->getFont()->setName('Arial')->setSize(10)->setBold(true)->setUnderline(true);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 3. TABLE HEADER (Baris 4)
        $headers = [
            'No',
            'Waktu Kejadian (WIB)',
            'Nama Pengguna',
            'NIP',
            'Modul Sistem',
            'Jenis Aksi',
            'Rincian Aktivitas',
            'Objek Rekaman',
            'Alamat IP',
        ];

        $col = 1;
        foreach ($headers as $h) {
            $coord = $this->getColumnLetter($col) . '4';
            $sheet->setCellValue($coord, $h);
            $col++;
        }

        $sheet->getStyle('A4:I4')->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'size' => 10,
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A8A'], // Navy Kemenhub
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '0F172A'],
                ],
            ],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(28);

        // 4. DATA ROWS
        $currentRow = 5;
        $no = 1;

        foreach ($records as $log) {
            $sheet->setCellValueExplicit("A{$currentRow}", $no++, DataType::TYPE_NUMERIC);
            $sheet->setCellValue("B{$currentRow}", $log->created_at->format('Y-m-d H:i:s'));
            $sheet->setCellValue("C{$currentRow}", $log->user_name ?: ($log->user?->name ?? 'Sistem / Tamu'));
            $sheet->setCellValueExplicit("D{$currentRow}", (string) ($log->user_nip ?: ($log->user?->nip ?? '-')), DataType::TYPE_STRING);
            $sheet->setCellValue("E{$currentRow}", $log->module);
            $sheet->setCellValue("F{$currentRow}", $log->action_label . " ({$log->action})");
            $sheet->setCellValue("G{$currentRow}", $log->description);
            
            $recordRef = $log->record_type ? "{$log->record_type} #{$log->record_id}" : '-';
            $sheet->setCellValue("H{$currentRow}", $recordRef);
            $sheet->setCellValue("I{$currentRow}", $log->ip_address ?: '127.0.0.1');

            // Format Baris Data
            $sheet->getStyle("A{$currentRow}:I{$currentRow}")->applyFromArray([
                'font' => ['name' => 'Arial', 'size' => 9],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);

            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Striping halus
            if ($no % 2 === 0) {
                $sheet->getStyle("A{$currentRow}:I{$currentRow}")->getFill()->applyFromArray([
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8FAFC'],
                ]);
            }

            $currentRow++;
        }

        $lastDataRow = max(5, $currentRow - 1);
        $sheet->setAutoFilter("A4:I{$lastDataRow}");
        $sheet->freezePane('A5');

        // Auto-fit Columns
        for ($i = 1; $i <= 9; $i++) {
            $sheet->getColumnDimension($this->getColumnLetter($i))->setAutoSize(true);
        }

        $filename = "Audit_Trail_BPTD_" . date('Ymd_His') . ".xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(true);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Ekspor Log Audit ke Format CSV (UTF-8 BOM)
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $records = $this->getFilteredLogs($request);
        $filename = "Audit_Trail_BPTD_" . date('Ymd_His') . ".csv";

        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, ['Waktu (WIB)', 'Nama Pengguna', 'NIP', 'Modul', 'Aksi', 'Rincian Aktivitas', 'Objek Rekaman', 'Alamat IP']);

            foreach ($records as $log) {
                fputcsv($handle, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user_name ?: ($log->user?->name ?? 'Sistem / Tamu'),
                    $log->user_nip ?: ($log->user?->nip ?? '-'),
                    $log->module,
                    $log->action,
                    $log->description,
                    $log->record_type ? "{$log->record_type} #{$log->record_id}" : '-',
                    $log->ip_address ?: '127.0.0.1',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Helper query filter penuh untuk ekspor data
     */
    protected function getFilteredLogs(Request $request)
    {
        $search = trim($request->query('q', ''));
        $module = $request->query('module', '');
        $action = $request->query('action', '');
        $userId = $request->query('user_id', '');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = AuditLog::with('user.role');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('user_nip', 'like', "%{$search}%")
                  ->orWhere('record_id', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($module !== '') {
            $query->where('module', $module);
        }
        if ($action !== '') {
            $query->where('action', $action);
        }
        if ($userId !== '') {
            $query->where('user_id', $userId);
        }
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return $query->orderByDesc('created_at')->orderByDesc('id')->get();
    }

    protected function getColumnLetter(int $colIndex): string
    {
        $letters = '';
        while ($colIndex > 0) {
            $mod = ($colIndex - 1) % 26;
            $letters = chr(65 + $mod) . $letters;
            $colIndex = (int) (($colIndex - $mod) / 26);
        }
        return $letters;
    }
}
