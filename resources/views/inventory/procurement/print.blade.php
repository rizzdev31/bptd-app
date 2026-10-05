<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>{{ $type === 'baphp' ? 'BAPHP' : 'SURAT PESANAN PO' }} - {{ $procurement->procurement_number }} - BPTD Kelas II Jatim</title>
  <style>
    @page {
      size: A4 portrait;
      margin: 1.5cm 2cm 1.5cm 2cm;
    }
    body {
      font-family: 'Times New Roman', Times, serif;
      font-size: 11pt;
      line-height: 1.35;
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
      padding-bottom: 8px;
      margin-bottom: 14px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .kop-logo {
      width: 70px;
      height: 70px;
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
      font-size: 10pt;
    }

    /* Judul Dokumen */
    .doc-title {
      font-size: 12pt;
      font-weight: bold;
      text-decoration: underline;
      margin-bottom: 2px;
    }
    .doc-number {
      font-size: 10pt;
      margin-bottom: 14px;
    }

    /* Tabel Info */
    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 14px;
      font-size: 10pt;
    }
    .info-table td {
      padding: 2.5px 4px;
      vertical-align: top;
    }
    .info-label {
      width: 24%;
      font-weight: bold;
    }
    .info-sep {
      width: 2%;
    }

    /* Tabel Barang */
    .items-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      margin-bottom: 15px;
      font-size: 10pt;
    }
    .items-table th, .items-table td {
      border: 1px solid #000;
      padding: 5px 7px;
    }
    .items-table th {
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
      font-size: 10pt;
    }
    .sig-space {
      height: 65px;
    }

    /* Print Control Bar */
    .print-controls {
      background: #f8fafc;
      border: 1px solid #cbd5e1;
      padding: 10px 16px;
      margin-bottom: 20px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-family: system-ui, -apple-system, sans-serif;
      font-size: 13px;
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
    .btn-toggle {
      background: #e2e8f0;
      color: #1e293b;
      border: none;
      padding: 6px 12px;
      border-radius: 6px;
      font-weight: 600;
      text-decoration: none;
      margin-right: 6px;
    }
    .btn-toggle.active {
      background: #0284c7;
      color: #fff;
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

  <!-- Tombol Aksi Cetak & Beralih Dokumen (Tidak tercetak) -->
  <div class="print-controls">
    <div>
      <span style="font-weight: bold; margin-right: 10px;">Format Cetak:</span>
      <a href="{{ route('inventory.procurement.print', ['procurement' => $procurement->id, 'type' => 'po']) }}" class="btn-toggle {{ $type === 'po' ? 'active' : '' }}">
        Surat Pesanan (PO)
      </a>
      <a href="{{ route('inventory.procurement.print', ['procurement' => $procurement->id, 'type' => 'baphp']) }}" class="btn-toggle {{ $type === 'baphp' ? 'active' : '' }}">
        Berita Acara Terima (BAPHP)
      </a>
    </div>
    <div>
      <button class="btn-print" onclick="window.print()">Cetak Dokumen (Ctrl+P)</button>
    </div>
  </div>

  <!-- KOP SURAT RESMI KEMENTERIAN PERHUBUNGAN -->
  <div class="kop-header">
    <img src="{{ asset('assets/logo-kemenhub.png') }}" alt="Logo Kemenhub" class="kop-logo" />
    <div class="kop-text">
      <div class="kop-title-1">KEMENTERIAN PERHUBUNGAN</div>
      <div class="kop-title-1">DIREKTORAT JENDERAL PERHUBUNGAN DARAT</div>
      <div class="kop-title-2">BALAI PENGELOLA TRANSPORTASI DARAT KELAS II JAWA TIMUR</div>
      <div class="kop-title-3">Jalan Gayung Kebonsari No. 54, Surabaya, Jawa Timur | Telp. (031) 8291234</div>
    </div>
    <div style="width: 70px;"></div>
  </div>

  @if ($type === 'baphp')
    <!-- ============================================== -->
    <!-- DOKUMEN: BERITA ACARA PENERIMAAN HASIL PENGADAAN -->
    <!-- ============================================== -->
    <div class="text-center">
      <div class="doc-title uppercase">BERITA ACARA PENERIMAAN HASIL PENGADAAN (BAPHP)</div>
      <div class="doc-number">Nomor: BAPHP/{{ date('Ymd', strtotime($procurement->date)) }}/{{ $procurement->procurement_number }}</div>
    </div>

    <p style="text-align: justify; font-size: 10pt; line-height: 1.5; margin-bottom: 12px;">
      Pada hari ini <strong>{{ $procurement->received_at ? $procurement->received_at->isoFormat('dddd, D MMMM Y') : $procurement->date->isoFormat('dddd, D MMMM Y') }}</strong>, bertempat di Gudang Logistik BPTD Kelas II Jawa Timur, kami yang bertanda tangan di bawah ini telah melakukan pemeriksaan dan verifikasi fisik terhadap penyerahan barang hasil pengadaan dengan rincian sebagai berikut:
    </p>

    <table class="info-table">
      <tr>
        <td class="info-label">Dasar Surat Pesanan (PO)</td>
        <td class="info-sep">:</td>
        <td class="font-bold">{{ $procurement->procurement_number }}</td>
        <td class="info-label">Tanggal Pesanan</td>
        <td class="info-sep">:</td>
        <td>{{ $procurement->date->format('d/m/Y') }}</td>
      </tr>
      <tr>
        <td class="info-label">Penyedia / Rekanan</td>
        <td class="info-sep">:</td>
        <td>{{ $procurement->supplier_name }}</td>
        <td class="info-label">No. Faktur / Surat Jalan</td>
        <td class="info-sep">:</td>
        <td>{{ $procurement->invoice_number ?: '-' }}</td>
      </tr>
      <tr>
        <td class="info-label">Petugas Pemeriksa</td>
        <td class="info-sep">:</td>
        <td>{{ $procurement->receiver?->name ?? ($procurement->user?->name ?? 'Petugas Gudang') }}</td>
        <td class="info-label">Status Hasil Pemeriksaan</td>
        <td class="info-sep">:</td>
        <td class="font-bold">DITERIMA LENGKAP &amp; BAIK</td>
      </tr>
    </table>

  @else
    <!-- ============================================== -->
    <!-- DOKUMEN: SURAT PESANAN (PURCHASE ORDER)        -->
    <!-- ============================================== -->
    <div class="text-center">
      <div class="doc-title uppercase">SURAT PESANAN PENGADAAN BARANG (PURCHASE ORDER)</div>
      <div class="doc-number">Nomor: {{ $procurement->procurement_number }}</div>
    </div>

    <table class="info-table">
      <tr>
        <td class="info-label">Kepada Yth.</td>
        <td class="info-sep">:</td>
        <td class="font-bold">{{ $procurement->supplier_name }}</td>
        <td class="info-label">Tanggal Dokumen</td>
        <td class="info-sep">:</td>
        <td>{{ $procurement->date->format('d/m/Y') }}</td>
      </tr>
      <tr>
        <td class="info-label">Alamat Rekanan</td>
        <td class="info-sep">:</td>
        <td>{{ $procurement->supplier?->address ?: 'Sesuai Kontrak / Penawaran' }}</td>
        <td class="info-label">No. Referensi SPK</td>
        <td class="info-sep">:</td>
        <td>{{ $procurement->invoice_number ?: '-' }}</td>
      </tr>
      <tr>
        <td class="info-label">Kontak / Telepon</td>
        <td class="info-sep">:</td>
        <td>{{ $procurement->supplier?->phone ?: '-' }} ({{ $procurement->supplier?->contact_person ?: 'Bag. Penjualan' }})</td>
        <td class="info-label">Petugas Pengadaan</td>
        <td class="info-sep">:</td>
        <td>{{ $procurement->user?->name ?? 'Pejabat Pengadaan BPTD' }}</td>
      </tr>
    </table>

    <p style="font-size: 10pt; margin-bottom: 8px;">
      Bersama ini kami memesan Alat Tulis Kantor (ATK) dan perlengkapan inventaris operasional dengan rincian sebagai berikut:
    </p>
  @endif

  <!-- TABEL RINCIAN BARANG -->
  <table class="items-table">
    <thead>
      <tr>
        <th style="width: 4%;">No.</th>
        <th style="width: 13%;">Kode Barang</th>
        <th>Nama Barang ATK</th>
        <th style="width: 12%;">Satuan Beli</th>
        <th style="width: 14%;">Rasio Konversi</th>
        <th style="width: 14%;">Total Fisik</th>
        <th style="width: 16%;">Harga Satuan</th>
        <th style="width: 16%;">Subtotal (Rp)</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($procurement->details as $index => $item)
        @php
          $smallUnit = $item->item?->effective_small_unit ?: ($item->item?->small_unit ?: 'Pcs');
          $isMulti = $item->conversion_factor > 1;
        @endphp
        <tr>
          <td class="text-center">{{ $index + 1 }}.</td>
          <td class="text-center font-mono">{{ $item->item_code }}</td>
          <td>
            <strong>{{ $item->item_name }}</strong>
            @if ($item->notes)
              <div style="font-size: 8.5pt; color: #475569;">Ket: {{ $item->notes }}</div>
            @endif
          </td>
          <td class="text-center font-bold">{{ $item->quantity }} {{ $item->unit }}</td>
          <td class="text-center" style="font-size: 9pt;">
            @if ($isMulti)
              1 {{ $item->unit }} = {{ $item->conversion_factor }} {{ $smallUnit }}
            @else
              1 {{ $item->unit }} = 1 {{ $smallUnit }}
            @endif
          </td>
          <td class="text-center font-bold" style="color: #047857;">
            {{ number_format($item->base_quantity, 0, ',', '.') }} {{ $smallUnit }}
          </td>
          <td class="text-right font-mono">
            {{ $item->formatted_unit_price }}
            @if ($isMulti)
              <div style="font-size: 8pt; color: #475569;">
                (@ Rp {{ number_format($item->unit_price / $item->conversion_factor, 0, ',', '.') }}/{{ $smallUnit }})
              </div>
            @endif
          </td>
          <td class="text-right font-mono font-bold">{{ $item->formatted_subtotal }}</td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="7" class="text-right font-bold uppercase" style="background-color: #f8fafc;">
          Total Anggaran Pengadaan:
        </td>
        <td class="text-right font-mono font-bold" style="background-color: #f8fafc; font-size: 11pt;">
          {{ $procurement->formatted_total_amount }}
        </td>
      </tr>
    </tfoot>
  </table>

  @if ($procurement->notes)
    <div style="font-size: 9.5pt; margin-bottom: 12px;">
      <strong>Catatan / Keterangan Khusus:</strong> {{ $procurement->notes }}
    </div>
  @endif

  <!-- TANDA TANGAN BERITA ACARA ATAU SURAT PESANAN -->
  <div class="signature-section">
    <table class="sig-table">
      <tr>
        <td>
          <div>Mengetahui / Memeriksa,</div>
          <div class="font-bold">Pejabat Pengadaan / Logistik</div>
          <div class="sig-space"></div>
          <div class="font-bold" style="text-decoration: underline;">
            {{ $procurement->user?->name ?? '................................................' }}
          </div>
          <div style="font-size: 9pt;">NIP. {{ $procurement->user?->nip ?? '.........................................' }}</div>
        </td>
        <td>
          <div>Surabaya, {{ $procurement->date->format('d/m/Y') }}</div>
          <div class="font-bold">Pihak Rekanan / Penyedia</div>
          <div class="sig-space"></div>
          <div class="font-bold" style="text-decoration: underline;">
            {{ $procurement->supplier?->contact_person ?? ($procurement->supplier_name ?: '................................................') }}
          </div>
          <div style="font-size: 9pt;">{{ $procurement->supplier_name }}</div>
        </td>
      </tr>
    </table>
  </div>

</body>
</html>
