<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExcelExportService
{
    /**
     * Menghasilkan file Excel (.xlsx) murni dengan pengaturan cetak A4 tersistematisasi.
     *
     * @param string $reportType 'stock' | 'stock_out' | 'stock_in' | 'procurement' | 'unit_usage'
     * @param mixed $data Collection data laporan
     * @param string $title Judul resmi laporan
     * @param string $dateFrom
     * @param string $dateTo
     * @return StreamedResponse
     */
    public function export(string $reportType, $data, string $title, string $dateFrom, string $dateTo): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr(str_replace(['/', '\\', '?', '*', ':', '[', ']'], '', $reportType), 0, 31));

        // ==============================================================
        // 1. SYSTEMIZED PAGE SETUP (A4 & FIT TO 1 PAGE WIDTH)
        // ==============================================================
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setPaperSize(PageSetup::PAPERSIZE_A4);
        $pageSetup->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $pageSetup->setFitToPage(true);
        $pageSetup->setFitToWidth(1);
        $pageSetup->setFitToHeight(0); // Biarkan tinggi mengalir bebas multi-halaman

        // Margin Kertas A4 Proporsional (dalam satuan Inci)
        $sheet->getPageMargins()->setTop(0.75);
        $sheet->getPageMargins()->setBottom(0.75);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        $sheet->getPageMargins()->setHeader(0.3);
        $sheet->getPageMargins()->setFooter(0.3);

        // Cetak Gridlines aktif
        $sheet->setShowGridlines(true);
        $sheet->setPrintGridlines(true);

        // Kunci baris header agar berulang di setiap lembar A4 saat dicetak (Print Titles)
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);

        // ==============================================================
        // 2. KOP RESMI PEMERINTAH (Baris 1 - 3)
        // ==============================================================
        $lastColumnLetter = $this->getLastColumnLetter($reportType);

        // Baris 1: KEMENTERIAN PERHUBUNGAN
        $sheet->mergeCells("A1:{$lastColumnLetter}1");
        $sheet->setCellValue('A1', 'KEMENTERIAN PERHUBUNGAN');
        $sheet->getStyle('A1')->getFont()->setName('Arial')->setSize(11)->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Baris 2: BALAI PENGELOLA TRANSPORTASI DARAT KELAS II JAWA TIMUR
        $sheet->mergeCells("A2:{$lastColumnLetter}2");
        $sheet->setCellValue('A2', 'BALAI PENGELOLA TRANSPORTASI DARAT KELAS II JAWA TIMUR');
        $sheet->getStyle('A2')->getFont()->setName('Arial')->setSize(13)->setBold(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Baris 3: Judul Laporan & Periode
        $periodInfo = $reportType === 'stock'
            ? "Data Posisi Stok Fisik Gudang per " . date('d F Y, H:i') . " WIB"
            : "Periode: " . date('d/m/Y', strtotime($dateFrom)) . " s/d " . date('d/m/Y', strtotime($dateTo));

        $sheet->mergeCells("A3:{$lastColumnLetter}3");
        $sheet->setCellValue('A3', strtoupper($title) . " (" . $periodInfo . ")");
        $sheet->getStyle('A3')->getFont()->setName('Arial')->setSize(10)->setBold(true)->setUnderline(true);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ==============================================================
        // 3. TABLE HEADER (Baris 4) & DATA ROWS (Baris 5 dst)
        // ==============================================================
        $headers = $this->getHeaders($reportType);
        $colIndex = 1;
        foreach ($headers as $h) {
            $cellCoord = $this->getColumnName($colIndex) . '4';
            $sheet->setCellValue($cellCoord, $h);
            $colIndex++;
        }

        // Styling Table Header: Navy Kemenhub (#1E3A8A), Teks Putih Tebal
        $headerRange = "A4:{$lastColumnLetter}4";
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'size' => 10,
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A8A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '0F172A'],
                ],
            ],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(28);

        // Isi Data Tabel
        $currentRow = 5;
        $this->populateData($sheet, $reportType, $data, $currentRow);

        $lastDataRow = $currentRow - 1;

        // AutoFilter pada Header Baris 4
        $sheet->setAutoFilter("A4:{$lastColumnLetter}{$lastDataRow}");

        // Freeze Panes (Kunci Header Baris 4 agar tetap terlihat saat di-scroll)
        $sheet->freezePane('A5');

        // ==============================================================
        // 4. SUMMARY TOTAL ROW (Jika Ada Kolom Angka / Kuantitas)
        // ==============================================================
        $totalRow = $this->addSummaryRow($sheet, $reportType, $currentRow, 5, $lastDataRow, $lastColumnLetter);
        $currentRow = $totalRow + 1;

        // ==============================================================
        // 5. BLOK TANDA TANGAN RESMI BPTD KELAS II JATIM
        // ==============================================================
        $this->addSignatureBlock($sheet, $currentRow + 2, $lastColumnLetter);

        // ==============================================================
        // 6. AUTO-FIT COLUMN WIDTHS DENGAN PADDING
        // ==============================================================
        for ($i = 1; $i <= count($headers); $i++) {
            $colLetter = $this->getColumnName($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // ==============================================================
        // 7. STREAM DOWNLOAD AS .XLSX
        // ==============================================================
        $filename = "Laporan_{$reportType}_" . date('Ymd_His') . ".xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(true);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    protected function getHeaders(string $reportType): array
    {
        return match ($reportType) {
            'stock' => [
                'No',
                'Kode SKU',
                'Nama Barang ATK',
                'Kategori',
                'Satuan Beli',
                'Satuan Eceran',
                'Batas Min',
                'Target Stok',
                'Stok Fisik Gudang',
                'Status Ketersediaan',
                'Lokasi Penyimpanan',
            ],
            'stock_out' => [
                'No',
                'No. Transaksi SBPB',
                'Tanggal',
                'Pegawai Penerima',
                'NIP',
                'Unit Kerja / Seksi',
                'Nama Barang ATK',
                'Kuantitas Kemasan',
                'Jumlah Fisik (Pcs)',
                'Petugas Gudang',
                'Keperluan / Catatan',
            ],
            'stock_in' => [
                'No',
                'No. Transaksi Masuk',
                'Tanggal',
                'Sumber Penerimaan',
                'Rekanan / Asal Barang',
                'No. Referensi / PO',
                'Nama Barang ATK',
                'Kuantitas Kemasan',
                'Jumlah Fisik (Pcs)',
                'Petugas Penerima',
                'Catatan',
            ],
            'procurement' => [
                'No',
                'No. Surat Pesanan (PO)',
                'Tanggal',
                'Rekanan / Vendor',
                'No. Faktur / SPK',
                'Rincian Item Dipesan',
                'Total Anggaran (Rp)',
                'Status Pengadaan',
                'Petugas Pengadaan',
                'Catatan',
            ],
            'unit_usage' => [
                'Peringkat',
                'Unit Kerja / Seksi BPTD',
                'Total Permintaan (SBPB)',
                'Ragam Varian Item',
                'Total Kuantitas Didistribusikan (Pcs)',
            ],
            default => ['No', 'Item', 'Jumlah'],
        };
    }

    protected function getLastColumnLetter(string $reportType): string
    {
        $count = count($this->getHeaders($reportType));
        return $this->getColumnName($count);
    }

    protected function populateData($sheet, string $reportType, $data, int &$currentRow): void
    {
        $currencyFormat = '_("Rp "* #,##0_);_("Rp "* (#,##0);_("Rp "* "-"_);_(@_)';
        $numberFormat = '#,##0';

        switch ($reportType) {
            case 'stock':
                $no = 1;
                foreach ($data as $item) {
                    $sheet->setCellValueExplicit("A{$currentRow}", $no++, DataType::TYPE_NUMERIC);
                    $sheet->setCellValueExplicit("B{$currentRow}", (string) $item->code, DataType::TYPE_STRING);
                    $sheet->setCellValue("C{$currentRow}", $item->name);
                    $sheet->setCellValue("D{$currentRow}", $item->category?->name ?? '-');
                    $sheet->setCellValue("E{$currentRow}", $item->unit ?: 'Pcs');
                    $sheet->setCellValue("F{$currentRow}", $item->effective_small_unit);
                    $sheet->setCellValueExplicit("G{$currentRow}", (int) $item->minimum_stock, DataType::TYPE_NUMERIC);
                    $sheet->setCellValueExplicit("H{$currentRow}", (int) $item->target_stock, DataType::TYPE_NUMERIC);
                    $sheet->setCellValueExplicit("I{$currentRow}", (int) $item->current_stock, DataType::TYPE_NUMERIC);
                    $sheet->setCellValue("J{$currentRow}", $item->stock_status_label);
                    $sheet->setCellValue("K{$currentRow}", $item->storage_location ?: 'Gudang Utama');

                    $this->applyDataRowStyle($sheet, $currentRow, 'K', $no % 2 === 0);
                    $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("G{$currentRow}:I{$currentRow}")->getNumberFormat()->setFormatCode($numberFormat);
                    $sheet->getStyle("G{$currentRow}:H{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("J{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $currentRow++;
                }
                break;

            case 'stock_out':
                $no = 1;
                foreach ($data as $out) {
                    foreach ($out->details as $d) {
                        $sheet->setCellValueExplicit("A{$currentRow}", $no++, DataType::TYPE_NUMERIC);
                        $sheet->setCellValueExplicit("B{$currentRow}", (string) $out->transaction_number, DataType::TYPE_STRING);
                        $sheet->setCellValue("C{$currentRow}", $out->transaction_date->format('d/m/Y'));
                        $sheet->setCellValue("D{$currentRow}", $out->recipient_name);
                        $sheet->setCellValueExplicit("E{$currentRow}", (string) ($out->recipient_nip ?: '-'), DataType::TYPE_STRING);
                        $sheet->setCellValue("F{$currentRow}", $out->recipient_unit ?: '-');
                        $sheet->setCellValue("G{$currentRow}", $d->item_name);
                        $sheet->setCellValue("H{$currentRow}", "{$d->quantity} {$d->unit}");
                        $sheet->setCellValueExplicit("I{$currentRow}", (int) $d->base_quantity, DataType::TYPE_NUMERIC);
                        $sheet->setCellValue("J{$currentRow}", $out->user?->name ?? 'Petugas Gudang');
                        $sheet->setCellValue("K{$currentRow}", $out->notes ?: '-');

                        $this->applyDataRowStyle($sheet, $currentRow, 'K', $no % 2 === 0);
                        $sheet->getStyle("A{$currentRow}:C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("H{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("I{$currentRow}")->getNumberFormat()->setFormatCode($numberFormat);
                        $sheet->getStyle("I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $currentRow++;
                    }
                }
                break;

            case 'stock_in':
                $no = 1;
                foreach ($data as $in) {
                    foreach ($in->details as $d) {
                        $sheet->setCellValueExplicit("A{$currentRow}", $no++, DataType::TYPE_NUMERIC);
                        $sheet->setCellValueExplicit("B{$currentRow}", (string) $in->transaction_number, DataType::TYPE_STRING);
                        $sheet->setCellValue("C{$currentRow}", $in->date->format('d/m/Y'));
                        $sheet->setCellValue("D{$currentRow}", $in->source);
                        $sheet->setCellValue("E{$currentRow}", $in->supplier_name ?: 'Internal BPTD');
                        $ref = $in->procurement ? $in->procurement->procurement_number : ($in->reference_number ?: '-');
                        $sheet->setCellValueExplicit("F{$currentRow}", (string) $ref, DataType::TYPE_STRING);
                        $sheet->setCellValue("G{$currentRow}", $d->item_name);
                        $sheet->setCellValue("H{$currentRow}", "{$d->quantity} {$d->unit}");
                        $sheet->setCellValueExplicit("I{$currentRow}", (int) $d->base_quantity, DataType::TYPE_NUMERIC);
                        $sheet->setCellValue("J{$currentRow}", $in->user?->name ?? 'Petugas Gudang');
                        $sheet->setCellValue("K{$currentRow}", $in->notes ?: '-');

                        $this->applyDataRowStyle($sheet, $currentRow, 'K', $no % 2 === 0);
                        $sheet->getStyle("A{$currentRow}:D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("F{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("H{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("I{$currentRow}")->getNumberFormat()->setFormatCode($numberFormat);
                        $sheet->getStyle("I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $currentRow++;
                    }
                }
                break;

            case 'procurement':
                $no = 1;
                foreach ($data as $p) {
                    $sheet->setCellValueExplicit("A{$currentRow}", $no++, DataType::TYPE_NUMERIC);
                    $sheet->setCellValueExplicit("B{$currentRow}", (string) $p->procurement_number, DataType::TYPE_STRING);
                    $sheet->setCellValue("C{$currentRow}", $p->date->format('d/m/Y'));
                    $sheet->setCellValue("D{$currentRow}", $p->supplier_name);
                    $sheet->setCellValueExplicit("E{$currentRow}", (string) ($p->invoice_number ?: '-'), DataType::TYPE_STRING);
                    
                    $itemsSummary = $p->details->map(fn($d) => "{$d->item_name} ({$d->quantity} {$d->unit})")->join(', ');
                    $sheet->setCellValue("F{$currentRow}", $itemsSummary);
                    $sheet->setCellValueExplicit("G{$currentRow}", (float) $p->total_amount, DataType::TYPE_NUMERIC);
                    $sheet->setCellValue("H{$currentRow}", $p->status_label);
                    $sheet->setCellValue("I{$currentRow}", $p->user?->name ?? 'Admin');
                    $sheet->setCellValue("J{$currentRow}", $p->notes ?: '-');

                    $this->applyDataRowStyle($sheet, $currentRow, 'J', $no % 2 === 0);
                    $sheet->getStyle("A{$currentRow}:C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("G{$currentRow}")->getNumberFormat()->setFormatCode($currencyFormat);
                    $sheet->getStyle("G{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("H{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $currentRow++;
                }
                break;

            case 'unit_usage':
                $rank = 1;
                foreach ($data as $u) {
                    $sheet->setCellValueExplicit("A{$currentRow}", $rank++, DataType::TYPE_NUMERIC);
                    $sheet->setCellValue("B{$currentRow}", $u->unit_name);
                    $sheet->setCellValueExplicit("C{$currentRow}", (int) $u->total_requests, DataType::TYPE_NUMERIC);
                    $sheet->setCellValueExplicit("D{$currentRow}", (int) $u->unique_items_count, DataType::TYPE_NUMERIC);
                    $sheet->setCellValueExplicit("E{$currentRow}", (int) $u->total_pieces, DataType::TYPE_NUMERIC);

                    $this->applyDataRowStyle($sheet, $currentRow, 'E', $rank % 2 === 0);
                    $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("C{$currentRow}:E{$currentRow}")->getNumberFormat()->setFormatCode($numberFormat);
                    $sheet->getStyle("C{$currentRow}:D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $currentRow++;
                }
                break;
        }
    }

    protected function applyDataRowStyle($sheet, int $row, string $lastCol, bool $isStripe): void
    {
        $range = "A{$row}:{$lastCol}{$row}";
        $style = [
            'font' => ['name' => 'Arial', 'size' => 9.5],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        if ($isStripe) {
            $style['fill'] = [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8FAFC'],
            ];
        }

        $sheet->getStyle($range)->applyFromArray($style);
        $sheet->getRowDimension($row)->setRowHeight(20);
    }

    protected function addSummaryRow($sheet, string $reportType, int $row, int $firstDataRow, int $lastDataRow, string $lastCol): int
    {
        if ($lastDataRow < $firstDataRow) {
            return $row; // Tidak ada data
        }

        $currencyFormat = '_("Rp "* #,##0_);_("Rp "* (#,##0);_("Rp "* "-"_);_(@_)';
        $numberFormat = '#,##0';

        switch ($reportType) {
            case 'stock':
                $sheet->mergeCells("A{$row}:H{$row}");
                $sheet->setCellValue("A{$row}", 'TOTAL KESELURUHAN STOK FISIK GUDANG (PCS):');
                $sheet->setCellValue("I{$row}", "=SUM(I{$firstDataRow}:I{$lastDataRow})");
                $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode($numberFormat);
                $this->styleSummaryRow($sheet, $row, $lastCol);
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                return $row + 1;

            case 'stock_out':
                $sheet->mergeCells("A{$row}:H{$row}");
                $sheet->setCellValue("A{$row}", 'TOTAL BARANG DIDISTRIBUSIKAN (PCS):');
                $sheet->setCellValue("I{$row}", "=SUM(I{$firstDataRow}:I{$lastDataRow})");
                $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode($numberFormat);
                $this->styleSummaryRow($sheet, $row, $lastCol);
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                return $row + 1;

            case 'stock_in':
                $sheet->mergeCells("A{$row}:H{$row}");
                $sheet->setCellValue("A{$row}", 'TOTAL FISIK MASUK GUDANG (PCS):');
                $sheet->setCellValue("I{$row}", "=SUM(I{$firstDataRow}:I{$lastDataRow})");
                $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode($numberFormat);
                $this->styleSummaryRow($sheet, $row, $lastCol);
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                return $row + 1;

            case 'procurement':
                $sheet->mergeCells("A{$row}:F{$row}");
                $sheet->setCellValue("A{$row}", 'TOTAL ANGGARAN BELANJA PENGADAAN:');
                $sheet->setCellValue("G{$row}", "=SUM(G{$firstDataRow}:G{$lastDataRow})");
                $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode($currencyFormat);
                $this->styleSummaryRow($sheet, $row, $lastCol);
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                return $row + 1;

            case 'unit_usage':
                $sheet->mergeCells("A{$row}:B{$row}");
                $sheet->setCellValue("A{$row}", 'TOTAL KESELURUHAN DISTRIBUSI:');
                $sheet->setCellValue("C{$row}", "=SUM(C{$firstDataRow}:C{$lastDataRow})");
                $sheet->setCellValue("E{$row}", "=SUM(E{$firstDataRow}:E{$lastDataRow})");
                $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode($numberFormat);
                $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode($numberFormat);
                $this->styleSummaryRow($sheet, $row, $lastCol);
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                return $row + 1;
        }

        return $row;
    }

    protected function styleSummaryRow($sheet, int $row, string $lastCol): void
    {
        $range = "A{$row}:{$lastCol}{$row}";
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['name' => 'Arial', 'size' => 10, 'bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0'],
            ],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '0F172A']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '0F172A']],
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '94A3B8']],
            ],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(24);
    }

    protected function addSignatureBlock($sheet, int $startRow, string $lastCol): void
    {
        // Posisi tanda tangan: Kiri (Mengetahui PPK/Kasubbag TU), Kanan (Pengelola Barang Persediaan ATK)
        $leftCol = 'B';
        $rightCol = $lastCol;

        // Baris Tanggal & Jabatan
        $sheet->setCellValue("{$leftCol}{$startRow}", 'Mengetahui,');
        $sheet->setCellValue("{$rightCol}{$startRow}", 'Surabaya, ' . date('d F Y'));
        $startRow++;

        $sheet->setCellValue("{$leftCol}{$startRow}", 'Kepala Subbagian Tata Usaha / PPK');
        $sheet->setCellValue("{$rightCol}{$startRow}", 'Pengelola Barang Persediaan ATK');
        $sheet->getStyle("{$leftCol}{$startRow}")->getFont()->setBold(true);
        $sheet->getStyle("{$rightCol}{$startRow}")->getFont()->setBold(true);

        // Ruang Tanda Tangan (3 baris kosong)
        $startRow += 4;

        // Nama & NIP
        $sheet->setCellValue("{$leftCol}{$startRow}", '( .................................................... )');
        $sheet->setCellValue("{$rightCol}{$startRow}", '( ' . (auth()->user()?->name ?? 'Petugas Logistik BPTD') . ' )');
        $sheet->getStyle("{$leftCol}{$startRow}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle("{$rightCol}{$startRow}")->getFont()->setBold(true)->setUnderline(true);
        $startRow++;

        $sheet->setCellValue("{$leftCol}{$startRow}", 'NIP. ....................................................');
        $sheet->setCellValue("{$rightCol}{$startRow}", 'NIP. ' . (auth()->user()?->nip ?? '....................................................'));
    }

    protected function getColumnName(int $index): string
    {
        $numeric = ($index - 1) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval(($index - 1) / 26);
        if ($num2 > 0) {
            return $this->getColumnName($num2) . $letter;
        }
        return $letter;
    }
}
