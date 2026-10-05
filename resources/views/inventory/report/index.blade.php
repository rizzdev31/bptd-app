@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi Persediaan ATK - BPTD Kelas II Jawa Timur')

@push('styles')
<style>
  .dash-interactive-card {
    transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.28s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.28s ease;
  }
  .dash-interactive-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -5px rgba(11, 35, 65, 0.08), 0 4px 6px -2px rgba(11, 35, 65, 0.03);
  }
  .no-scrollbar::-webkit-scrollbar {
    display: none;
  }
  .no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }
</style>
@endpush

@section('content')
<main class="main-content flex-1 max-w-430 w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

  <!-- ============================================================== -->
  <!-- 1. HEADER BAR: LAPORAN & REKAPITULASI ATK                      -->
  <!-- ============================================================== -->
  <div class="no-print bg-white rounded-2xl border border-slate-200/80 shadow-2xs px-5 py-4 transition anim-fade-in-up">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      
      <!-- Sisi Kiri: Emblem & Identitas -->
      <div class="flex items-center gap-3.5">
        <div class="w-11 h-11 rounded-xl bg-slate-50 border border-slate-200/80 p-1.5 flex items-center justify-center shrink-0 shadow-2xs">
          <img src="{{ asset('assets/logo-kemenhub.png') }}" alt="Logo Kemenhub" class="w-full h-full object-contain" />
        </div>
        <div>
          <div class="flex items-center gap-2 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
            <span>BPTD Kelas II Jawa Timur</span>
            <span>&bull;</span>
            <span class="text-blue-600 font-bold">Laporan &amp; Audit</span>
          </div>
          <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight leading-snug">
            Laporan &amp; Rekapitulasi Persediaan ATK
          </h1>
        </div>
      </div>

      <!-- Sisi Kanan: Export Action Buttons (Excel & PDF/Print) -->
      <div class="flex items-center gap-2 shrink-0 self-start sm:self-center flex-wrap">
        
        <!-- Tombol Unduh CSV -->
        <a
          href="{{ route('inventory.reports.export-csv', request()->query()) }}"
          class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-800 font-bold text-xs transition border border-slate-200/80 cursor-pointer shadow-2xs"
          title="Unduh Data Mentah (CSV)"
        >
          <svg class="w-4 h-4 text-slate-600 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          <span>CSV</span>
        </a>

        <!-- Tombol Ekspor Excel (.xlsx) -->
        <a
          href="{{ route('inventory.reports.export-excel', request()->query()) }}"
          class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs sm:text-sm transition shadow-xs cursor-pointer"
          title="Ekspor ke Format Microsoft Excel Asli (.xlsx) - Standar Cetak A4 Siap Edit"
        >
          <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span>Ekspor Excel (.xlsx)</span>
        </a>

        <!-- Tombol Cetak Dokumen / PDF -->
        <a
          href="{{ route('inventory.reports.print', request()->query()) }}"
          target="_blank"
          class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs sm:text-sm transition shadow-xs cursor-pointer"
          title="Cetak Laporan Resmi / Simpan sebagai PDF"
        >
          <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
          </svg>
          <span>Cetak / PDF</span>
        </a>

      </div>

    </div>
  </div>

  <!-- ============================================================== -->
  <!-- 2. METRIC QUICK CARDS (Statistik Ringkas)                      -->
  <!-- ============================================================== -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    
    <!-- Card 1: Stok Fisik Tersedia -->
    <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up">
      <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block truncate">Total Stok Gudang</span>
        <span class="text-lg font-black text-slate-900 tracking-tight block">{{ number_format($totalCurrentStock) }} Pcs</span>
        <span class="text-[11px] font-semibold text-blue-600 block truncate">{{ number_format($totalItems) }} Varian Terdaftar</span>
      </div>
    </div>

    <!-- Card 2: Pengeluaran Bulan Ini -->
    <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up anim-delay-100">
      <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block truncate">Distribusi Bulan Ini</span>
        <span class="text-lg font-black text-amber-700 tracking-tight block">{{ number_format($monthStockOutQuantity) }} Pcs</span>
        <span class="text-[11px] font-semibold text-amber-600 block truncate">Permintaan Terlayani</span>
      </div>
    </div>

    <!-- Card 3: Penerimaan Masuk Bulan Ini -->
    <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up anim-delay-150">
      <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block truncate">Penerimaan Bulan Ini</span>
        <span class="text-lg font-black text-emerald-700 tracking-tight block">{{ number_format($monthStockInQuantity) }} Pcs</span>
        <span class="text-[11px] font-semibold text-emerald-600 block truncate">Fisik Masuk Gudang</span>
      </div>
    </div>

    <!-- Card 4: Anggaran Pengadaan Bulan Ini -->
    <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up anim-delay-200">
      <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block truncate">Belanja Pengadaan</span>
        <span class="text-lg font-black text-slate-900 tracking-tight block truncate">Rp {{ number_format($monthProcurementSpent, 0, ',', '.') }}</span>
        <span class="text-[11px] font-semibold text-indigo-600 block truncate">Realisasi PO Diterima</span>
      </div>
    </div>

  </div>

  <!-- ============================================================== -->
  <!-- 3. TAB NAVIGASI UTAMA (Segmented Control)                      -->
  <!-- ============================================================== -->
  <div class="flex items-center bg-slate-100/90 p-1 rounded-xl border border-slate-200/70 overflow-x-auto no-scrollbar gap-1">
    
    <a
      href="{{ route('inventory.reports.index', ['type' => 'stock']) }}"
      class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all whitespace-nowrap {{ $reportType === 'stock' ? 'bg-white text-blue-700 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/60' }}"
    >
      <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
      </svg>
      <span>1. Stok &amp; Persediaan</span>
    </a>

    <a
      href="{{ route('inventory.reports.index', ['type' => 'stock_out']) }}"
      class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all whitespace-nowrap {{ $reportType === 'stock_out' ? 'bg-white text-blue-700 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/60' }}"
    >
      <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
      </svg>
      <span>2. Pengeluaran ATK (SBPB)</span>
    </a>

    <a
      href="{{ route('inventory.reports.index', ['type' => 'stock_in']) }}"
      class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all whitespace-nowrap {{ $reportType === 'stock_in' ? 'bg-white text-blue-700 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/60' }}"
    >
      <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
      </svg>
      <span>3. Penerimaan Masuk</span>
    </a>

    <a
      href="{{ route('inventory.reports.index', ['type' => 'procurement']) }}"
      class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all whitespace-nowrap {{ $reportType === 'procurement' ? 'bg-white text-blue-700 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/60' }}"
    >
      <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <span>4. Pengadaan &amp; PO</span>
    </a>

    <a
      href="{{ route('inventory.reports.index', ['type' => 'unit_usage']) }}"
      class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all whitespace-nowrap {{ $reportType === 'unit_usage' ? 'bg-white text-blue-700 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/60' }}"
    >
      <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
      </svg>
      <span>5. Konsumsi per Unit Kerja</span>
    </a>

  </div>

  <!-- ============================================================== -->
  <!-- 4. FILTER BAR RESPONSIF SESUAI TIPE LAPORAN                   -->
  <!-- ============================================================== -->
  <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs">
    <form method="GET" action="{{ route('inventory.reports.index') }}" class="space-y-3">
      <input type="hidden" name="type" value="{{ $reportType }}">

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        
        <!-- Search Keyword -->
        <div>
          <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
            Kata Kunci Pencarian
          </label>
          <div class="relative">
            <input
              type="text"
              name="q"
              value="{{ request('q') }}"
              placeholder="Cari item, no transaksi, nama..."
              class="w-full pl-9 pr-3 py-2 rounded-xl text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white transition"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>

        @if ($reportType === 'stock')
          <!-- Filter Kategori -->
          <div>
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
              Kategori Barang
            </label>
            <select
              name="category_id"
              class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white"
            >
              <option value="all">Semua Kategori</option>
              @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                  {{ $cat->name }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Filter Status Stok -->
          <div>
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
              Status Ketersediaan
            </label>
            <select
              name="status_stock"
              class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white"
            >
              <option value="all">Semua Status</option>
              <option value="available" {{ request('status_stock') === 'available' ? 'selected' : '' }}>Stok Aman (&gt; Minimum)</option>
              <option value="low_stock" {{ request('status_stock') === 'low_stock' ? 'selected' : '' }}>Stok Menipis (&le; Minimum)</option>
              <option value="out_of_stock" {{ request('status_stock') === 'out_of_stock' ? 'selected' : '' }}>Stok Kosong (0 Unit)</option>
            </select>
          </div>

        @else
          <!-- Filter Tanggal Mulai -->
          <div>
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
              Periode Mulai
            </label>
            <input
              type="date"
              name="date_from"
              value="{{ $dateFrom }}"
              class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white"
            />
          </div>

          <!-- Filter Tanggal Selesai -->
          <div>
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
              Periode Sampai
            </label>
            <input
              type="date"
              name="date_to"
              value="{{ $dateTo }}"
              class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white"
            />
          </div>
        @endif

        @if ($reportType === 'stock_out' || $reportType === 'unit_usage')
          <!-- Filter Unit Kerja -->
          <div>
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
              Unit Kerja / Seksi
            </label>
            <select
              name="work_unit"
              class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white"
            >
              <option value="all">Semua Unit Kerja</option>
              @foreach ($workUnits as $wu)
                <option value="{{ $wu->name }}" {{ request('work_unit') === $wu->name ? 'selected' : '' }}>
                  {{ $wu->name }}
                </option>
              @endforeach
            </select>
          </div>
        @endif

        @if ($reportType === 'stock_in')
          <!-- Filter Sumber Stock In -->
          <div>
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
              Sumber Penerimaan
            </label>
            <select
              name="source"
              class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white"
            >
              <option value="all">Semua Sumber</option>
              <option value="Procurement" {{ request('source') === 'Procurement' ? 'selected' : '' }}>Pengadaan (Procurement)</option>
              <option value="Direct" {{ request('source') === 'Direct' ? 'selected' : '' }}>Penerimaan Langsung</option>
              <option value="Hibah" {{ request('source') === 'Hibah' ? 'selected' : '' }}>Hibah</option>
              <option value="Sisa Kegiatan" {{ request('source') === 'Sisa Kegiatan' ? 'selected' : '' }}>Sisa Kegiatan</option>
            </select>
          </div>
        @endif

        @if ($reportType === 'procurement')
          <!-- Filter Status Pengadaan -->
          <div>
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
              Status Pengadaan
            </label>
            <select
              name="status"
              class="w-full px-3 py-2 rounded-xl text-xs border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white"
            >
              <option value="all">Semua Status</option>
              <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
              <option value="ordered" {{ request('status') === 'ordered' ? 'selected' : '' }}>Dipesan (Ordered)</option>
              <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Diterima (Received)</option>
              <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
          </div>
        @endif

        <!-- Action Filter Button -->
        <div class="flex items-end gap-2">
          <button
            type="submit"
            class="flex-1 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition cursor-pointer text-center"
          >
            Terapkan Filter
          </button>
          @if (request()->hasAny(['q', 'category_id', 'status_stock', 'date_from', 'date_to', 'work_unit', 'source', 'status']))
            <a
              href="{{ route('inventory.reports.index', ['type' => $reportType]) }}"
              class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition text-center"
              title="Reset Filter"
            >
              Reset
            </a>
          @endif
        </div>

      </div>
    </form>
  </div>

  <!-- ============================================================== -->
  <!-- 5. TABEL HASIL LAPORAN                                         -->
  <!-- ============================================================== -->
  <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
    <div class="overflow-x-auto">
      
      @if ($reportType === 'stock')
        <!-- TABEL 1: LAPORAN STOK & PERSEDIAAN -->
        <table class="w-full text-left text-xs sm:text-sm">
          <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] uppercase tracking-wider text-slate-500 font-semibold select-none">
            <tr>
              <th class="py-3 px-4">No.</th>
              <th class="py-3 px-4">Kode SKU</th>
              <th class="py-3 px-4">Nama Barang ATK</th>
              <th class="py-3 px-4">Kategori</th>
              <th class="py-3 px-4">Satuan Kemasan</th>
              <th class="py-3 px-4 text-center">Batas Min</th>
              <th class="py-3 px-4 text-center">Target</th>
              <th class="py-3 px-4 text-right">Stok Fisik Saat Ini</th>
              <th class="py-3 px-4 text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse ($reportData as $index => $item)
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="py-3 px-4 text-slate-400">{{ $reportData->firstItem() + $index }}.</td>
                <td class="py-3 px-4 font-mono font-bold text-blue-700">{{ $item->code }}</td>
                <td class="py-3 px-4">
                  <span class="font-bold text-slate-900 block">{{ $item->name }}</span>
                  <span class="text-[11px] text-slate-400">Lokasi: {{ $item->storage_location ?: 'Gudang Utama' }}</span>
                </td>
                <td class="py-3 px-4 text-slate-700">{{ $item->category?->name ?? '-' }}</td>
                <td class="py-3 px-4 text-slate-700">
                  @if ($item->conversion_rate > 1)
                    <span class="font-semibold text-slate-800">{{ $item->unit }}</span>
                    <span class="text-[10px] text-slate-400 block font-mono">1 {{ $item->unit }} = {{ $item->conversion_rate }} {{ $item->effective_small_unit }}</span>
                  @else
                    <span>{{ $item->effective_small_unit }}</span>
                  @endif
                </td>
                <td class="py-3 px-4 text-center font-mono">{{ $item->minimum_stock }} {{ $item->effective_small_unit }}</td>
                <td class="py-3 px-4 text-center font-mono text-slate-400">{{ $item->target_stock }}</td>
                <td class="py-3 px-4 text-right font-mono font-black text-sm {{ $item->current_stock <= $item->minimum_stock ? 'text-amber-700' : 'text-slate-900' }}">
                  {{ number_format($item->current_stock) }} {{ $item->effective_small_unit }}
                </td>
                <td class="py-3 px-4 text-center whitespace-nowrap">
                  @if ($item->stock_status === 'out_of_stock')
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">Stok Habis</span>
                  @elseif ($item->stock_status === 'low_stock')
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">Stok Menipis</span>
                  @else
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Tersedia</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="py-10 text-center text-slate-400 font-semibold">Tidak ada data persediaan yang cocok dengan filter.</td>
              </tr>
            @endforelse
          </tbody>
        </table>

      @elseif ($reportType === 'stock_out')
        <!-- TABEL 2: LAPORAN PENGELUARAN ATK (SBPB) -->
        <table class="w-full text-left text-xs sm:text-sm">
          <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] uppercase tracking-wider text-slate-500 font-semibold select-none">
            <tr>
              <th class="py-3 px-4">No. Transaksi &amp; Tanggal</th>
              <th class="py-3 px-4">Pegawai Penerima</th>
              <th class="py-3 px-4">Unit Kerja / Seksi</th>
              <th class="py-3 px-4">Rincian Barang yang Diambil</th>
              <th class="py-3 px-4 text-right">Total Kuantitas</th>
              <th class="py-3 px-4">Petugas Gudang</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse ($reportData as $out)
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="py-3 px-4 whitespace-nowrap">
                  <span class="font-mono font-bold text-amber-700">{{ $out->transaction_number }}</span>
                  <span class="text-[11px] text-slate-400 block">{{ $out->transaction_date->format('d/m/Y') }}</span>
                </td>
                <td class="py-3 px-4">
                  <span class="font-bold text-slate-900 block">{{ $out->recipient_name }}</span>
                  <span class="text-[11px] text-slate-400 font-mono">NIP: {{ $out->recipient_nip ?: '-' }}</span>
                </td>
                <td class="py-3 px-4 text-slate-700">
                  <span class="font-semibold block">{{ $out->recipient_unit ?: '-' }}</span>
                  <span class="text-[11px] text-slate-400">{{ $out->recipient_position ?: '-' }}</span>
                </td>
                <td class="py-3 px-4">
                  <ul class="space-y-0.5">
                    @foreach ($out->details as $d)
                      <li class="text-xs text-slate-800">
                        &bull; <strong>{{ $d->item_name }}</strong> : {{ $d->quantity }} {{ $d->unit }} 
                        @if ($d->conversion_factor > 1)
                          <span class="text-[10px] text-slate-400 font-mono">({{ $d->base_quantity }} Pcs)</span>
                        @endif
                      </li>
                    @endforeach
                  </ul>
                  @if ($out->notes)
                    <div class="text-[11px] text-slate-400 italic mt-1">Keperluan: {{ $out->notes }}</div>
                  @endif
                </td>
                <td class="py-3 px-4 text-right font-mono font-black text-amber-700 whitespace-nowrap">
                  {{ number_format($out->total_quantity) }} Pcs
                </td>
                <td class="py-3 px-4 text-slate-800 whitespace-nowrap">
                  {{ $out->user?->name ?? 'Petugas Gudang' }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="py-10 text-center text-slate-400 font-semibold">Tidak ada transaksi pengeluaran pada periode ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>

      @elseif ($reportType === 'stock_in')
        <!-- TABEL 3: LAPORAN PENERIMAAN MASUK -->
        <table class="w-full text-left text-xs sm:text-sm">
          <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] uppercase tracking-wider text-slate-500 font-semibold select-none">
            <tr>
              <th class="py-3 px-4">No. Transaksi &amp; Tanggal</th>
              <th class="py-3 px-4">Sumber / Asal</th>
              <th class="py-3 px-4">No. Referensi / PO</th>
              <th class="py-3 px-4">Rincian Barang Masuk</th>
              <th class="py-3 px-4 text-right">Kuantitas Fisik</th>
              <th class="py-3 px-4">Petugas Penerima</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse ($reportData as $in)
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="py-3 px-4 whitespace-nowrap">
                  <span class="font-mono font-bold text-emerald-700">{{ $in->transaction_number }}</span>
                  <span class="text-[11px] text-slate-400 block">{{ $in->date->format('d/m/Y') }}</span>
                </td>
                <td class="py-3 px-4">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold {{ $in->source === 'Procurement' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-700' }}">
                    {{ $in->source }}
                  </span>
                  <span class="font-bold text-slate-900 block mt-0.5">{{ $in->supplier_name ?: 'Internal BPTD' }}</span>
                </td>
                <td class="py-3 px-4 font-mono text-xs whitespace-nowrap">
                  @if ($in->procurement)
                    <span class="text-blue-600 font-bold">{{ $in->procurement->procurement_number }}</span>
                  @else
                    <span class="text-slate-600">{{ $in->reference_number ?: '-' }}</span>
                  @endif
                </td>
                <td class="py-3 px-4">
                  <ul class="space-y-0.5">
                    @foreach ($in->details as $d)
                      <li class="text-xs text-slate-800">
                        &bull; <strong>{{ $d->item_name }}</strong> : {{ $d->quantity }} {{ $d->unit }}
                        @if ($d->conversion_factor > 1)
                          <span class="text-[10px] text-emerald-600 font-mono">({{ $d->base_quantity }} Pcs)</span>
                        @endif
                      </li>
                    @endforeach
                  </ul>
                </td>
                <td class="py-3 px-4 text-right font-mono font-black text-emerald-700 whitespace-nowrap">
                  {{ number_format($in->total_quantity) }} Pcs
                </td>
                <td class="py-3 px-4 text-slate-800 whitespace-nowrap">
                  {{ $in->user?->name ?? 'Petugas Gudang' }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="py-10 text-center text-slate-400 font-semibold">Tidak ada catatan penerimaan barang pada periode ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>

      @elseif ($reportType === 'procurement')
        <!-- TABEL 4: LAPORAN PENGADAAN & PO -->
        <table class="w-full text-left text-xs sm:text-sm">
          <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] uppercase tracking-wider text-slate-500 font-semibold select-none">
            <tr>
              <th class="py-3 px-4">No. PO &amp; Tanggal</th>
              <th class="py-3 px-4">Rekanan / Vendor</th>
              <th class="py-3 px-4">No. Faktur / SPK</th>
              <th class="py-3 px-4">Rincian Barang Dipesan</th>
              <th class="py-3 px-4 text-right">Total Anggaran (Rp)</th>
              <th class="py-3 px-4 text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse ($reportData as $p)
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="py-3 px-4 whitespace-nowrap">
                  <span class="font-mono font-bold text-blue-700">{{ $p->procurement_number }}</span>
                  <span class="text-[11px] text-slate-400 block">{{ $p->date->format('d/m/Y') }}</span>
                </td>
                <td class="py-3 px-4">
                  <span class="font-bold text-slate-900 block">{{ $p->supplier_name }}</span>
                  <span class="text-[11px] text-slate-400">{{ $p->supplier?->contact_person }}</span>
                </td>
                <td class="py-3 px-4 font-mono text-xs whitespace-nowrap">
                  {{ $p->invoice_number ?: '-' }}
                </td>
                <td class="py-3 px-4">
                  <ul class="space-y-0.5">
                    @foreach ($p->details as $d)
                      <li class="text-xs text-slate-800">
                        &bull; <strong>{{ $d->item_name }}</strong> : {{ $d->quantity }} {{ $d->unit }} @ {{ $d->formatted_unit_price }}
                      </li>
                    @endforeach
                  </ul>
                </td>
                <td class="py-3 px-4 text-right font-mono font-black text-slate-900 whitespace-nowrap">
                  {{ $p->formatted_total_amount }}
                </td>
                <td class="py-3 px-4 text-center whitespace-nowrap">
                  <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $p->status_badge_class }}">
                    {{ $p->status_label }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="py-10 text-center text-slate-400 font-semibold">Tidak ada data pengadaan pada periode ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>

      @elseif ($reportType === 'unit_usage')
        <!-- TABEL 5: DISTRIBUSI PER UNIT KERJA -->
        <table class="w-full text-left text-xs sm:text-sm">
          <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] uppercase tracking-wider text-slate-500 font-semibold select-none">
            <tr>
              <th class="py-3 px-4 w-12">Peringkat</th>
              <th class="py-3 px-4">Unit Kerja / Seksi BPTD</th>
              <th class="py-3 px-4 text-center">Total Pengajuan (SBPB)</th>
              <th class="py-3 px-4 text-center">Ragam Varian Item</th>
              <th class="py-3 px-4 text-right">Total Kuantitas Didistribusikan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse ($reportData as $rank => $unit)
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="py-3 px-4 text-center font-bold font-mono">
                  @if ($rank === 0)
                    <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-800 inline-flex items-center justify-center text-xs">1</span>
                  @elseif ($rank === 1)
                    <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-800 inline-flex items-center justify-center text-xs">2</span>
                  @elseif ($rank === 2)
                    <span class="w-6 h-6 rounded-full bg-amber-50 text-amber-700 inline-flex items-center justify-center text-xs">3</span>
                  @else
                    <span class="text-slate-400 text-xs">{{ $rank + 1 }}</span>
                  @endif
                </td>
                <td class="py-3 px-4">
                  <span class="font-bold text-slate-900 block text-sm">{{ $unit->unit_name }}</span>
                </td>
                <td class="py-3 px-4 text-center font-mono font-bold text-slate-800">
                  {{ $unit->total_requests }} Kali Permintaan
                </td>
                <td class="py-3 px-4 text-center font-mono text-slate-600">
                  {{ $unit->unique_items_count }} Varian Item
                </td>
                <td class="py-3 px-4 text-right font-mono font-black text-blue-700 text-sm">
                  {{ number_format($unit->total_pieces) }} Pcs
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="py-10 text-center text-slate-400 font-semibold">Tidak ada data distribusi per unit kerja pada periode ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      @endif

    </div>

    <!-- Paginasi (jika query mendukung paginasi) -->
    @if (method_exists($reportData, 'hasPages') && $reportData->hasPages())
      <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50">
        {{ $reportData->links() }}
      </div>
    @endif

  </div>

</main>
@endsection
