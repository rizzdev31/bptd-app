<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>SBPB - {{ $stockOut->transaction_number }} - BPTD Kelas II Jatim</title>
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
      padding: 20px;
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
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .kop-logo {
      width: 75px;
      height: 75px;
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
      margin-bottom: 16px;
    }

    /* Informasi Transaksi & Penerima */
    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 14px;
      font-size: 10pt;
    }
    .info-table td {
      padding: 2px 4px;
      vertical-align: top;
    }
    .info-label {
      width: 18%;
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
      margin-bottom: 20px;
      font-size: 10pt;
    }
    .items-table th, .items-table td {
      border: 1px solid #000;
      padding: 6px 8px;
    }
    .items-table th {
      background-color: #f1f5f9;
      font-weight: bold;
      text-align: center;
    }

    /* Kolom Tanda Tangan */
    .signature-container {
      width: 100%;
      margin-top: 30px;
      page-break-inside: avoid;
    }
    .signature-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 10pt;
      text-align: center;
    }
    .signature-table td {
      vertical-align: top;
      width: 50%;
    }
    .signature-space {
      height: 70px;
    }

    /* Tombol Print Mengambang untuk Layar */
    .print-bar {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: #2563eb;
      color: #fff;
      padding: 10px 18px;
      border-radius: 9999px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.25);
      cursor: pointer;
      font-family: sans-serif;
      font-size: 12px;
      font-weight: bold;
      display: flex;
      align-items: center;
      gap: 6px;
      border: none;
      z-index: 100;
    }
    .print-bar:hover {
      background: #1d4ed8;
    }

    @media print {
      body {
        padding: 0;
      }
      .no-print {
        display: none !important;
      }
    }
  </style>
</head>
<body>

  <!-- Floating Print Button (Hanya tampil di browser) -->
  <button class="print-bar no-print" onclick="window.print()">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
    </svg>
    Cetak Dokumen SBPB
  </button>

  <!-- Kop Surat Resmi Kementerian -->
  <div class="kop-header">
    <img src="{{ asset('assets/logo-kemenhub.png') }}" alt="Logo Kementerian Perhubungan" class="kop-logo" />
    <div class="kop-text">
      <div class="kop-title-1 uppercase">KEMENTERIAN PERHUBUNGAN</div>
      <div class="kop-title-1 uppercase">DIREKTORAT JENDERAL PERHUBUNGAN DARAT</div>
      <div class="kop-title-2 uppercase">BALAI PENGELOLA TRANSPORTASI DARAT KELAS II JAWA TIMUR</div>
      <div class="kop-title-3">Jalan Gayung Kebonsari No. 50 Surabaya | Telp/Fax: (031) 8292275</div>
      <div class="kop-title-3">Laman: bptdjatim.kemenhub.go.id | Pos-el: bptd_jatim@kemenhub.go.id</div>
    </div>
    <div style="width: 75px;"></div>
  </div>

  <!-- Judul Dokumen -->
  <div class="text-center">
    <div class="doc-title uppercase">SURAT BUKTI PENGELUARAN BARANG (SBPB)</div>
    <div class="doc-title uppercase" style="font-size: 11pt; text-decoration: none; font-weight: normal; margin-top: 2px;">
      BERITA ACARA PENYERAHAN ALAT TULIS KANTOR (ATK)
    </div>
    <div class="doc-number">Nomor Bukti: <strong>{{ $stockOut->transaction_number }}</strong></div>
  </div>

  <p style="text-align: justify; margin-bottom: 12px; font-size: 10.5pt;">
    Pada hari ini, tanggal <strong>{{ \Carbon\Carbon::parse($stockOut->transaction_date)->translatedFormat('d F Y') }}</strong>, telah diserahkan sejumlah barang persediaan Alat Tulis Kantor (ATK) internal dari Pengelola Gudang ATK BPTD Kelas II Jawa Timur kepada pegawai yang bersangkutan sebagai berikut:
  </p>

  <!-- Identitas Penerima & Pengeluaran -->
  <table class="info-table">
    <tr>
      <td class="info-label">Nama Pegawai</td>
      <td class="info-sep">:</td>
      <td class="font-bold">{{ $stockOut->recipient_name }}</td>
      <td class="info-label">Tanggal Pengeluaran</td>
      <td class="info-sep">:</td>
      <td>{{ \Carbon\Carbon::parse($stockOut->transaction_date)->format('d/m/Y') }}</td>
    </tr>
    <tr>
      <td class="info-label">NIP</td>
      <td class="info-sep">:</td>
      <td style="font-family: monospace;">{{ $stockOut->recipient_nip ?: '-' }}</td>
      <td class="info-label">Petugas Pengelola ATK</td>
      <td class="info-sep">:</td>
      <td>{{ $stockOut->user?->name ?? 'Petugas Logistik' }}</td>
    </tr>
    <tr>
      <td class="info-label">Unit Kerja</td>
      <td class="info-sep">:</td>
      <td>{{ $stockOut->recipient_unit ?: '-' }}</td>
      <td class="info-label">Keperluan Dinas</td>
      <td class="info-sep">:</td>
      <td>{{ $stockOut->notes ?: 'Operasional Rutin Kantor' }}</td>
    </tr>
    @if ($stockOut->recipient_position)
      <tr>
        <td class="info-label">Jabatan</td>
        <td class="info-sep">:</td>
        <td colspan="4">{{ $stockOut->recipient_position }}</td>
      </tr>
    @endif
  </table>

  <!-- Rincian Barang yang Diterima -->
  <table class="items-table">
    <thead>
      <tr>
        <th style="width: 5%;">NO</th>
        <th style="width: 18%;">KODE ATK</th>
        <th style="width: 47%;">NAMA BARANG PERSEDIAAN ATK</th>
        <th style="width: 12%;">JUMLAH</th>
        <th style="width: 18%;">SATUAN BAKU</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($stockOut->details as $index => $item)
        <tr>
          <td class="text-center" style="font-family: monospace;">{{ $index + 1 }}</td>
          <td class="text-center" style="font-family: monospace; font-weight: bold;">{{ $item->item_code }}</td>
          <td>{{ $item->item_name }}</td>
          <td class="text-center font-bold" style="font-size: 11pt;">{{ number_format($item->quantity) }}</td>
          <td class="text-center">
            <span style="font-weight: bold;">{{ $item->unit }}</span>
            @if ($item->conversion_factor > 1)
              <div style="font-size: 8.5pt; color: #475569; font-style: italic; margin-top: 1px;">
                (Setara {{ number_format($item->base_quantity) }} {{ $item->item?->effective_small_unit ?? 'Pcs' }})
              </div>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr style="background-color: #f8fafc; font-weight: bold;">
        <td colspan="3" class="text-right">TOTAL PENGELUARAN FISIK :</td>
        <td class="text-center" style="font-size: 11pt;">{{ number_format($stockOut->total_quantity) }}</td>
        <td class="text-center">{{ $stockOut->total_items }} Varian Barang</td>
      </tr>
    </tfoot>
  </table>

  <p style="text-align: justify; font-size: 10pt; margin-bottom: 14px;">
    Barang-barang persediaan di atas telah diperiksa, dihitung fisik, serta diterima dalam kondisi baik, lengkap, dan siap digunakan untuk menunjang kelancaran pelaksanaan tugas kedinasan pada BPTD Kelas II Jawa Timur.
  </p>

  <!-- Kolom Tanda Tangan -->
  <div class="signature-container">
    <table class="signature-table">
      <tr>
        <td>
          Yang Menerima,<br>
          <strong>Pegawai / Pemohon ATK</strong>
          <div class="signature-space"></div>
          <div style="font-weight: bold; text-decoration: underline;">
            ( {{ $stockOut->recipient_name }} )
          </div>
          <div style="font-size: 9pt;">NIP. {{ $stockOut->recipient_nip ?: '.......................................................' }}</div>
        </td>
        <td>
          Surabaya, {{ \Carbon\Carbon::parse($stockOut->transaction_date)->translatedFormat('d F Y') }}<br>
          Yang Menyerahkan,<br>
          <strong>Petugas Pengelola ATK</strong>
          <div class="signature-space"></div>
          <div style="font-weight: bold; text-decoration: underline;">
            ( {{ $stockOut->user?->name ?? 'Petugas Gudang' }} )
          </div>
          <div style="font-size: 9pt;">NIP. {{ $stockOut->user?->nip ?? '.......................................................' }}</div>
        </td>
      </tr>
    </table>
  </div>

</body>
</html>
