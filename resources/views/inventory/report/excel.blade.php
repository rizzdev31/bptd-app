<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>{{ $title }}</title>
  <style>
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 11pt;
    }
    .kop-title {
      font-size: 14pt;
      font-weight: bold;
      text-align: center;
    }
    .kop-sub {
      font-size: 11pt;
      text-align: center;
      margin-bottom: 15px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    th {
      background-color: #1e3a8a;
      color: #ffffff;
      font-weight: bold;
      text-align: center;
      border: 1px solid #94a3b8;
      padding: 8px;
    }
    td {
      border: 1px solid #cbd5e1;
      padding: 6px 8px;
      vertical-align: middle;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-left { text-align: left; }
    .font-bold { font-weight: bold; }
    .total-row {
      background-color: #f1f5f9;
      font-weight: bold;
    }
  </style>
</head>
<body>

  <!-- KOP DOKUMEN LAPORAN RESMI BPTD KELAS II JATIM -->
  <table style="border: none; margin-bottom: 20px;">
    <tr>
      <td colspan="8" style="border: none; text-align: center;">
        <div style="font-size: 13pt; font-weight: bold;">KEMENTERIAN PERHUBUNGAN</div>
        <div style="font-size: 14pt; font-weight: bold;">BALAI PENGELOLA TRANSPORTASI DARAT KELAS II JAWA TIMUR</div>
        <div style="font-size: 11pt; font-weight: bold; text-decoration: underline; margin-top: 8px;">{{ strtoupper($title) }}</div>
        @if ($reportType !== 'stock')
          <div style="font-size: 10pt; color: #475569; margin-top: 4px;">
            Periode: {{ date('d/m/Y', strtotime($dateFrom)) }} s/d {{ date('d/m/Y', strtotime($dateTo)) }}
          </div>
        @else
          <div style="font-size: 10pt; color: #475569; margin-top: 4px;">
            Posisi Data: Per {{ date('d/m/Y H:i') }} WIB
          </div>
        @endif
      </td>
    </tr>
  </table>

  @if ($reportType === 'stock')
    <!-- TABEL EXCEL STOK -->
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Kode SKU</th>
          <th>Nama Barang ATK</th>
          <th>Kategori</th>
          <th>Satuan Kemasan</th>
          <th>Satuan Eceran</th>
          <th>Batas Minimum</th>
          <th>Target Stok</th>
          <th>Stok Fisik Gudang</th>
          <th>Status Ketersediaan</th>
          <th>Lokasi Penyimpanan</th>
        </tr>
      </thead>
      <tbody>
        @php $totalQty = 0; @endphp
        @foreach ($data as $idx => $row)
          @php $totalQty += $row->current_stock; @endphp
          <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td style="mso-number-format:'\@';">{{ $row->code }}</td>
            <td class="font-bold">{{ $row->name }}</td>
            <td>{{ $row->category?->name ?? '-' }}</td>
            <td>{{ $row->unit ?: 'Pcs' }}</td>
            <td>{{ $row->effective_small_unit }}</td>
            <td class="text-center">{{ $row->minimum_stock }}</td>
            <td class="text-center">{{ $row->target_stock }}</td>
            <td class="text-right font-bold">{{ $row->current_stock }}</td>
            <td class="text-center">{{ $row->stock_status_label }}</td>
            <td>{{ $row->storage_location ?: 'Gudang Utama' }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr class="total-row">
          <td colspan="8" class="text-right font-bold">TOTAL KESELURUHAN STOK (PCS):</td>
          <td class="text-right font-bold">{{ $totalQty }}</td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
    </table>

  @elseif ($reportType === 'stock_out')
    <!-- TABEL EXCEL PENGELUARAN -->
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>No. Transaksi SBPB</th>
          <th>Tanggal</th>
          <th>Pegawai Penerima</th>
          <th>NIP</th>
          <th>Unit Kerja / Seksi</th>
          <th>Nama Barang ATK</th>
          <th>Jumlah Kemasan</th>
          <th>Jumlah Fisik (Pcs)</th>
          <th>Petugas Gudang</th>
          <th>Keperluan / Catatan</th>
        </tr>
      </thead>
      <tbody>
        @php $no = 1; $totalOutPcs = 0; @endphp
        @foreach ($data as $out)
          @foreach ($out->details as $d)
            @php $totalOutPcs += $d->base_quantity; @endphp
            <tr>
              <td class="text-center">{{ $no++ }}</td>
              <td style="mso-number-format:'\@';">{{ $out->transaction_number }}</td>
              <td class="text-center">{{ $out->transaction_date->format('d/m/Y') }}</td>
              <td class="font-bold">{{ $out->recipient_name }}</td>
              <td style="mso-number-format:'\@';">{{ $out->recipient_nip ?: '-' }}</td>
              <td>{{ $out->recipient_unit ?: '-' }}</td>
              <td>{{ $d->item_name }}</td>
              <td class="text-center">{{ $d->quantity }} {{ $d->unit }}</td>
              <td class="text-right font-bold">{{ $d->base_quantity }}</td>
              <td>{{ $out->user?->name ?? 'Petugas Gudang' }}</td>
              <td>{{ $out->notes ?: '-' }}</td>
            </tr>
          @endforeach
        @endforeach
      </tbody>
      <tfoot>
        <tr class="total-row">
          <td colspan="8" class="text-right font-bold">TOTAL BARANG DIDISTRIBUSIKAN (PCS):</td>
          <td class="text-right font-bold">{{ $totalOutPcs }}</td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
    </table>

  @elseif ($reportType === 'stock_in')
    <!-- TABEL EXCEL PENERIMAAN -->
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>No. Transaksi Masuk</th>
          <th>Tanggal</th>
          <th>Sumber Penerimaan</th>
          <th>Rekanan / Asal Barang</th>
          <th>No. Referensi / PO</th>
          <th>Nama Barang ATK</th>
          <th>Jumlah Kemasan</th>
          <th>Jumlah Fisik (Pcs)</th>
          <th>Petugas Penerima</th>
          <th>Catatan</th>
        </tr>
      </thead>
      <tbody>
        @php $no = 1; $totalInPcs = 0; @endphp
        @foreach ($data as $in)
          @foreach ($in->details as $d)
            @php $totalInPcs += $d->base_quantity; @endphp
            <tr>
              <td class="text-center">{{ $no++ }}</td>
              <td style="mso-number-format:'\@';">{{ $in->transaction_number }}</td>
              <td class="text-center">{{ $in->date->format('d/m/Y') }}</td>
              <td>{{ $in->source }}</td>
              <td class="font-bold">{{ $in->supplier_name ?: 'Internal BPTD' }}</td>
              <td style="mso-number-format:'\@';">{{ $in->procurement ? $in->procurement->procurement_number : ($in->reference_number ?: '-') }}</td>
              <td>{{ $d->item_name }}</td>
              <td class="text-center">{{ $d->quantity }} {{ $d->unit }}</td>
              <td class="text-right font-bold">{{ $d->base_quantity }}</td>
              <td>{{ $in->user?->name ?? 'Petugas Gudang' }}</td>
              <td>{{ $in->notes ?: '-' }}</td>
            </tr>
          @endforeach
        @endforeach
      </tbody>
      <tfoot>
        <tr class="total-row">
          <td colspan="8" class="text-right font-bold">TOTAL BARANG MASUK FISIK (PCS):</td>
          <td class="text-right font-bold">{{ $totalInPcs }}</td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
    </table>

  @elseif ($reportType === 'procurement')
    <!-- TABEL EXCEL PENGADAAN -->
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>No. Surat Pesanan (PO)</th>
          <th>Tanggal</th>
          <th>Rekanan / Vendor</th>
          <th>No. Faktur / SPK</th>
          <th>Total Anggaran Belanja (Rp)</th>
          <th>Status Pengadaan</th>
          <th>Petugas Pengadaan</th>
          <th>Catatan</th>
        </tr>
      </thead>
      <tbody>
        @php $totalBudget = 0; @endphp
        @foreach ($data as $idx => $p)
          @php $totalBudget += $p->total_amount; @endphp
          <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td style="mso-number-format:'\@';">{{ $p->procurement_number }}</td>
            <td class="text-center">{{ $p->date->format('d/m/Y') }}</td>
            <td class="font-bold">{{ $p->supplier_name }}</td>
            <td style="mso-number-format:'\@';">{{ $p->invoice_number ?: '-' }}</td>
            <td class="text-right font-bold" style="mso-number-format:'\#\,\#\#0';">{{ (float) $p->total_amount }}</td>
            <td class="text-center">{{ $p->status_label }}</td>
            <td>{{ $p->user?->name ?? 'Admin' }}</td>
            <td>{{ $p->notes ?: '-' }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr class="total-row">
          <td colspan="5" class="text-right font-bold">TOTAL KESELURUHAN ANGGARAN:</td>
          <td class="text-right font-bold" style="mso-number-format:'\#\,\#\#0';">{{ $totalBudget }}</td>
          <td colspan="3"></td>
        </tr>
      </tfoot>
    </table>

  @elseif ($reportType === 'unit_usage')
    <!-- TABEL EXCEL DISTRIBUSI UNIT KERJA -->
    <table>
      <thead>
        <tr>
          <th>Peringkat</th>
          <th>Unit Kerja / Seksi BPTD</th>
          <th>Total Frekuensi Pengajuan (SBPB)</th>
          <th>Ragam Varian Item</th>
          <th>Total Kuantitas Didistribusikan (Pcs)</th>
        </tr>
      </thead>
      <tbody>
        @php $totalUnitPieces = 0; $totalRequests = 0; @endphp
        @foreach ($data as $idx => $u)
          @php 
            $totalUnitPieces += $u->total_pieces;
            $totalRequests += $u->total_requests;
          @endphp
          <tr>
            <td class="text-center font-bold">{{ $idx + 1 }}</td>
            <td class="font-bold">{{ $u->unit_name }}</td>
            <td class="text-center">{{ $u->total_requests }} Kali</td>
            <td class="text-center">{{ $u->unique_items_count }} Item</td>
            <td class="text-right font-bold">{{ $u->total_pieces }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr class="total-row">
          <td colspan="2" class="text-right font-bold">TOTAL KESELURUHAN DISTRIBUSI:</td>
          <td class="text-center font-bold">{{ $totalRequests }} Kali</td>
          <td></td>
          <td class="text-right font-bold">{{ $totalUnitPieces }}</td>
        </tr>
      </tfoot>
    </table>
  @endif

  <!-- TANDA TANGAN PENGESAHAN DOKUMEN -->
  <br><br>
  <table style="border: none; width: 100%;">
    <tr>
      <td colspan="4" style="border: none; text-align: center; width: 50%;">
        <div>Mengetahui,</div>
        <div style="font-weight: bold;">Kepala Subbagian Tata Usaha / PPK</div>
        <br><br><br>
        <div style="font-weight: bold; text-decoration: underline;">.........................................................</div>
        <div>NIP. .....................................................</div>
      </td>
      <td colspan="4" style="border: none; text-align: center; width: 50%;">
        <div>Surabaya, {{ date('d F Y') }}</div>
        <div style="font-weight: bold;">Pengelola Barang Persediaan ATK</div>
        <br><br><br>
        <div style="font-weight: bold; text-decoration: underline;">{{ Auth::user()?->name ?? 'Petugas Logistik BPTD' }}</div>
        <div>NIP. {{ Auth::user()?->nip ?? '.....................................................' }}</div>
      </td>
    </tr>
  </table>

</body>
</html>
