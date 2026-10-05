<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>{{ $title }} - BPTD Kelas II Jawa Timur</title>
  <style>
    @page {
      size: A4 landscape;
      margin: 1.2cm 1.5cm 1.2cm 1.5cm;
    }
    body {
      font-family: 'Times New Roman', Times, serif;
      font-size: 10pt;
      line-height: 1.3;
      color: #000;
      background: #fff;
      margin: 0;
      padding: 15px;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-left { text-align: left; }
    .font-bold { font-weight: bold; }
    .uppercase { text-transform: uppercase; }

    /* Kop Surat Resmi */
    .kop-header {
      border-bottom: 3px double #000;
      padding-bottom: 6px;
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .kop-logo {
      width: 65px;
      height: 65px;
      object-fit: contain;
    }
    .kop-text {
      text-align: center;
      flex: 1;
      padding: 0 10px;
    }
    .kop-title-1 {
      font-size: 11pt;
      font-weight: bold;
      letter-spacing: 0.5px;
    }
    .kop-title-2 {
      font-size: 13pt;
      font-weight: bold;
      letter-spacing: 0.5px;
    }
    .kop-title-3 {
      font-size: 9.5pt;
    }

    /* Judul Laporan */
    .doc-title {
      font-size: 12pt;
      font-weight: bold;
      text-decoration: underline;
      margin-bottom: 2px;
    }
    .doc-subtitle {
      font-size: 9.5pt;
      margin-bottom: 12px;
    }

    /* Tabel Laporan */
    .report-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      margin-bottom: 15px;
      font-size: 9pt;
    }
    .report-table th, .report-table td {
      border: 1px solid #000;
      padding: 4px 6px;
    }
    .report-table th {
      background-color: #f1f5f9;
      font-weight: bold;
      text-align: center;
    }

    /* Tanda Tangan */
    .signature-section {
      width: 100%;
      margin-top: 25px;
      page-break-inside: avoid;
    }
    .sig-table {
      width: 100%;
      border-collapse: collapse;
    }
    .sig-table td {
      vertical-align: top;
      text-align: center;
      width: 50%;
      font-size: 9.5pt;
    }
    .sig-space {
      height: 60px;
    }

    /* Print Toolbar */
    .print-controls {
      background: #f8fafc;
      border: 1px solid #cbd5e1;
      padding: 8px 14px;
      margin-bottom: 15px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-family: system-ui, sans-serif;
      font-size: 12px;
    }
    .btn-print {
      background: #2563eb;
      color: #fff;
      border: none;
      padding: 6px 14px;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
    }

    @media print {
      .print-controls {
        display: none !important;
      }
      body {
        padding: 0;
      }
    }
  </style>
</head>
<body>

  <!-- Controls Bar -->
  <div class="print-controls">
    <div>
      <span class="font-bold">Laporan:</span> {{ $title }}
      @if ($reportType !== 'stock')
        <span style="color: #64748b; margin-left: 8px;">({{ date('d/m/Y', strtotime($dateFrom)) }} - {{ date('d/m/Y', strtotime($dateTo)) }})</span>
      @endif
    </div>
    <div>
      <button class="btn-print" onclick="window.print()">Cetak Dokumen (Ctrl+P)</button>
    </div>
  </div>

  <!-- KOP SURAT RESMI -->
  <div class="kop-header">
    <img src="{{ asset('assets/logo-kemenhub.png') }}" alt="Logo Kemenhub" class="kop-logo" />
    <div class="kop-text">
      <div class="kop-title-1">KEMENTERIAN PERHUBUNGAN</div>
      <div class="kop-title-1">DIREKTORAT JENDERAL PERHUBUNGAN DARAT</div>
      <div class="kop-title-2">BALAI PENGELOLA TRANSPORTASI DARAT KELAS II JAWA TIMUR</div>
      <div class="kop-title-3">Jalan Gayung Kebonsari No. 54, Surabaya, Jawa Timur | Telp. (031) 8291234</div>
    </div>
    <div style="width: 65px;"></div>
  </div>

  <!-- JUDUL DOKUMEN -->
  <div class="text-center">
    <div class="doc-title uppercase">{{ $title }}</div>
    <div class="doc-subtitle">
      @if ($reportType !== 'stock')
        Periode: {{ date('d/m/Y', strtotime($dateFrom)) }} s/d {{ date('d/m/Y', strtotime($dateTo)) }}
      @else
        Data Posisi Persediaan Gudang per {{ date('d F Y, H:i') }} WIB
      @endif
      @if ($search)
        &bull; Filter Pencarian: "{{ $search }}"
      @endif
    </div>
  </div>

  <!-- ISI TABEL LAPORAN -->
  @if ($reportType === 'stock')
    <table class="report-table">
      <thead>
        <tr>
          <th style="width: 4%;">No.</th>
          <th style="width: 11%;">Kode SKU</th>
          <th>Nama Barang ATK</th>
          <th style="width: 14%;">Kategori</th>
          <th style="width: 13%;">Satuan Beli</th>
          <th style="width: 9%;">Batas Min</th>
          <th style="width: 12%;">Stok Fisik Saat Ini</th>
          <th style="width: 12%;">Status</th>
          <th style="width: 12%;">Lokasi Simpan</th>
        </tr>
      </thead>
      <tbody>
        @php $totalStockPcs = 0; @endphp
        @foreach ($data as $idx => $row)
          @php $totalStockPcs += $row->current_stock; @endphp
          <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td class="text-center font-mono">{{ $row->code }}</td>
            <td class="font-bold">{{ $row->name }}</td>
            <td>{{ $row->category?->name ?? '-' }}</td>
            <td>
              @if ($row->conversion_rate > 1)
                {{ $row->unit }} (Isi {{ $row->conversion_rate }} {{ $row->effective_small_unit }})
              @else
                {{ $row->effective_small_unit }}
              @endif
            </td>
            <td class="text-center">{{ $row->minimum_stock }} {{ $row->effective_small_unit }}</td>
            <td class="text-right font-bold">{{ number_format($row->current_stock) }} {{ $row->effective_small_unit }}</td>
            <td class="text-center font-bold">{{ $row->stock_status_label }}</td>
            <td>{{ $row->storage_location ?: 'Gudang Utama' }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr style="background-color: #f1f5f9; font-weight: bold;">
          <td colspan="6" class="text-right uppercase">Total Seluruh Persediaan Fisik (Pcs):</td>
          <td class="text-right font-bold">{{ number_format($totalStockPcs) }} Pcs</td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
    </table>

  @elseif ($reportType === 'stock_out')
    <table class="report-table">
      <thead>
        <tr>
          <th style="width: 4%;">No.</th>
          <th style="width: 14%;">No. Transaksi SBPB</th>
          <th style="width: 10%;">Tanggal</th>
          <th style="width: 15%;">Pegawai Penerima</th>
          <th style="width: 15%;">Unit Kerja / Seksi</th>
          <th>Rincian Barang yang Didistribusikan</th>
          <th style="width: 11%;">Total (Pcs)</th>
          <th style="width: 12%;">Petugas</th>
        </tr>
      </thead>
      <tbody>
        @php $totalDistPcs = 0; @endphp
        @foreach ($data as $idx => $out)
          @php $totalDistPcs += $out->total_quantity; @endphp
          <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td class="text-center font-mono">{{ $out->transaction_number }}</td>
            <td class="text-center">{{ $out->transaction_date->format('d/m/Y') }}</td>
            <td>
              <strong>{{ $out->recipient_name }}</strong>
              @if ($out->recipient_nip)
                <div style="font-size: 8pt; color: #475569;">NIP: {{ $out->recipient_nip }}</div>
              @endif
            </td>
            <td>{{ $out->recipient_unit ?: '-' }}</td>
            <td>
              @foreach ($out->details as $d)
                <div>&bull; {{ $d->item_name }} ({{ $d->quantity }} {{ $d->unit }})</div>
              @endforeach
            </td>
            <td class="text-right font-bold">{{ number_format($out->total_quantity) }} Pcs</td>
            <td>{{ $out->user?->name ?? 'Petugas Gudang' }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr style="background-color: #f1f5f9; font-weight: bold;">
          <td colspan="6" class="text-right uppercase">Total Seluruh Distribusi Fisik (Pcs):</td>
          <td class="text-right font-bold">{{ number_format($totalDistPcs) }} Pcs</td>
          <td></td>
        </tr>
      </tfoot>
    </table>

  @elseif ($reportType === 'stock_in')
    <table class="report-table">
      <thead>
        <tr>
          <th style="width: 4%;">No.</th>
          <th style="width: 15%;">No. Transaksi Masuk</th>
          <th style="width: 10%;">Tanggal</th>
          <th style="width: 12%;">Sumber</th>
          <th style="width: 16%;">Penyedia / Asal</th>
          <th style="width: 13%;">No. PO / Referensi</th>
          <th>Rincian Barang Masuk</th>
          <th style="width: 11%;">Kuantitas (Pcs)</th>
        </tr>
      </thead>
      <tbody>
        @php $totalInPcs = 0; @endphp
        @foreach ($data as $idx => $in)
          @php $totalInPcs += $in->total_quantity; @endphp
          <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td class="text-center font-mono">{{ $in->transaction_number }}</td>
            <td class="text-center">{{ $in->date->format('d/m/Y') }}</td>
            <td class="text-center">{{ $in->source }}</td>
            <td>{{ $in->supplier_name ?: 'Internal BPTD' }}</td>
            <td class="font-mono text-center">{{ $in->procurement ? $in->procurement->procurement_number : ($in->reference_number ?: '-') }}</td>
            <td>
              @foreach ($in->details as $d)
                <div>&bull; {{ $d->item_name }} ({{ $d->quantity }} {{ $d->unit }})</div>
              @endforeach
            </td>
            <td class="text-right font-bold">{{ number_format($in->total_quantity) }} Pcs</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr style="background-color: #f1f5f9; font-weight: bold;">
          <td colspan="7" class="text-right uppercase">Total Seluruh Fisik Masuk (Pcs):</td>
          <td class="text-right font-bold">{{ number_format($totalInPcs) }} Pcs</td>
        </tr>
      </tfoot>
    </table>

  @elseif ($reportType === 'procurement')
    <table class="report-table">
      <thead>
        <tr>
          <th style="width: 4%;">No.</th>
          <th style="width: 16%;">No. PO Pengadaan</th>
          <th style="width: 10%;">Tanggal</th>
          <th style="width: 18%;">Rekanan / Vendor</th>
          <th style="width: 14%;">No. Faktur / SPK</th>
          <th>Rincian Barang</th>
          <th style="width: 14%;">Total Anggaran (Rp)</th>
          <th style="width: 11%;">Status</th>
        </tr>
      </thead>
      <tbody>
        @php $totalBudgetPcs = 0; @endphp
        @foreach ($data as $idx => $p)
          @php $totalBudgetPcs += $p->total_amount; @endphp
          <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td class="text-center font-mono font-bold">{{ $p->procurement_number }}</td>
            <td class="text-center">{{ $p->date->format('d/m/Y') }}</td>
            <td>{{ $p->supplier_name }}</td>
            <td class="font-mono text-center">{{ $p->invoice_number ?: '-' }}</td>
            <td>
              @foreach ($p->details as $d)
                <div>&bull; {{ $d->item_name }} ({{ $d->quantity }} {{ $d->unit }})</div>
              @endforeach
            </td>
            <td class="text-right font-mono font-bold">{{ $p->formatted_total_amount }}</td>
            <td class="text-center">{{ $p->status_label }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr style="background-color: #f1f5f9; font-weight: bold;">
          <td colspan="6" class="text-right uppercase">Total Anggaran Belanja Pengadaan:</td>
          <td class="text-right font-mono font-bold">Rp {{ number_format($totalBudgetPcs, 0, ',', '.') }}</td>
          <td></td>
        </tr>
      </tfoot>
    </table>

  @elseif ($reportType === 'unit_usage')
    <table class="report-table">
      <thead>
        <tr>
          <th style="width: 8%;">Peringkat</th>
          <th>Unit Kerja / Seksi BPTD Kelas II Jatim</th>
          <th style="width: 22%;">Total Frekuensi Permintaan (SBPB)</th>
          <th style="width: 20%;">Ragam Varian Item</th>
          <th style="width: 25%;">Total Kuantitas Didistribusikan</th>
        </tr>
      </thead>
      <tbody>
        @php $totUnits = 0; $totReqs = 0; @endphp
        @foreach ($data as $idx => $u)
          @php 
            $totUnits += $u->total_pieces;
            $totReqs += $u->total_requests;
          @endphp
          <tr>
            <td class="text-center font-bold">{{ $idx + 1 }}</td>
            <td class="font-bold">{{ $u->unit_name }}</td>
            <td class="text-center font-bold">{{ $u->total_requests }} Kali Permintaan</td>
            <td class="text-center">{{ $u->unique_items_count }} Varian Item</td>
            <td class="text-right font-bold">{{ number_format($u->total_pieces) }} Pcs</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr style="background-color: #f1f5f9; font-weight: bold;">
          <td colspan="2" class="text-right uppercase">Total Seluruh Distribusi:</td>
          <td class="text-center font-bold">{{ $totReqs }} Kali</td>
          <td></td>
          <td class="text-right font-bold">{{ number_format($totUnits) }} Pcs</td>
        </tr>
      </tfoot>
    </table>
  @endif

  <!-- TANDA TANGAN RESMI -->
  <div class="signature-section">
    <table class="sig-table">
      <tr>
        <td>
          <div>Mengetahui,</div>
          <div class="font-bold">Kepala Subbagian Tata Usaha / PPK</div>
          <div class="sig-space"></div>
          <div class="font-bold" style="text-decoration: underline;">.........................................................</div>
          <div style="font-size: 8.5pt;">NIP. .....................................................</div>
        </td>
        <td>
          <div>Surabaya, {{ date('d F Y') }}</div>
          <div class="font-bold">Pengelola Barang Persediaan ATK</div>
          <div class="sig-space"></div>
          <div class="font-bold" style="text-decoration: underline;">{{ Auth::user()?->name ?? 'Petugas Logistik BPTD' }}</div>
          <div style="font-size: 8.5pt;">NIP. {{ Auth::user()?->nip ?? '.....................................................' }}</div>
        </td>
      </tr>
    </table>
  </div>

</body>
</html>
