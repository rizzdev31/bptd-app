@extends('layouts.app')

@section('title', 'Katalog dan Master Data ATK - BPTD Kelas II Jawa Timur')

@push('styles')
<style>
  /* Official Ministry Print Stylesheet */
  @media print {
    body {
      background-color: #ffffff !important;
      font-size: 11pt !important;
      color: #000000 !important;
    }
    #sidebar-slot,
    #navbar-slot,
    #footer-slot,
    .no-print,
    button,
    form[action*="items"] {
      display: none !important;
    }
    .print-only {
      display: block !important;
    }
    .table-container {
      border: 1px solid #000000 !important;
      box-shadow: none !important;
      border-radius: 0 !important;
    }
    table {
      width: 100% !important;
      border-collapse: collapse !important;
    }
    th, td {
      border: 1px solid #999999 !important;
      padding: 6px 8px !important;
      font-size: 9pt !important;
    }
    th {
      background-color: #f1f5f9 !important;
      color: #000000 !important;
    }
    .main-content {
      padding: 0 !important;
      margin: 0 !important;
      max-width: 100% !important;
    }
  }
  .print-only {
    display: none;
  }
  .gauge-circle {
    transform: rotate(-90deg);
  }
</style>
@endpush

@section('content')
<main class="main-content flex-1 max-w-[1720px] w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

  <!-- Printable Ministry Letterhead (Only visible during print) -->
  <div class="print-only mb-6 text-center border-b-2 border-slate-900 pb-4">
    <div class="flex items-center justify-center gap-4 mb-2">
      <img src="{{ asset('assets/logo-kemenhub.png') }}" alt="Logo Kemenhub" class="h-16 w-auto object-contain" />
      <div>
        <h2 class="text-base font-extrabold uppercase tracking-wide text-slate-900 leading-tight">
          Kementerian Perhubungan Republik Indonesia
        </h2>
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 leading-tight">
          Direktorat Jenderal Perhubungan Darat
        </h3>
        <h1 class="text-lg font-black uppercase tracking-tight text-slate-900 leading-snug">
          Balai Pengelola Transportasi Darat Kelas II Jawa Timur
        </h1>
        <p class="text-[10px] text-slate-600 mt-0.5 font-medium">
          Jl. Gayung Kebonsari No. 50, Gayungan, Kota Surabaya, Jawa Timur 60235
        </p>
      </div>
    </div>
    <div class="w-full border-t border-slate-400 mt-2 pt-2">
      <h4 class="text-sm font-bold uppercase tracking-wider text-slate-900">
        Laporan Rekapitulasi Katalog dan Master Data Inventaris ATK
      </h4>
      <p class="text-[11px] text-slate-600">
        Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB | Operator: {{ Auth::user()?->name ?? 'Petugas ATK' }}
      </p>
    </div>
  </div>

  <!-- Header Banner: Institutional Ministry Tone -->
  <div class="no-print bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5 sm:p-6 transition hover:shadow-md">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
      <!-- Title & Agency Info -->
      <div class="space-y-2">
        <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 text-[11px] tracking-wide font-medium">
            <svg class="w-3.5 h-3.5 text-blue-700" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
            </svg>
            KEMENTERIAN PERHUBUNGAN REPUBLIK INDONESIA
          </span>
          <span class="text-slate-300">/</span>
          <span class="text-slate-500 font-medium">BPTD KELAS II JAWA TIMUR</span>
          <span class="text-slate-300">/</span>
          <span class="text-blue-700 font-bold">SUBBAGIAN TATA USAHA &amp; LOGISTIK</span>
        </div>

        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-xl bg-[#0b2341] text-amber-400 flex items-center justify-center shrink-0 shadow-xs">
            <svg class="w-6 h-6 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
          </div>
          <div>
            <h2 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 leading-tight uppercase">
              Katalog dan Master Data ATK
            </h2>
            <p class="text-xs sm:text-[13px] text-slate-500 mt-0.5">
              Penatausahaan Buku Induk Barang Persediaan Habis Pakai, Pengawasan Safety Stock, dan Standar Distribusi Internal.
            </p>
          </div>
        </div>
      </div>

      <!-- Action Buttons: Institutional Palette -->
      <div class="flex flex-wrap items-center gap-2.5">
        <!-- Print / Export Button -->
        <button
          type="button"
          onclick="window.print()"
          class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs border border-slate-300 shadow-xs transition hover:border-slate-400 active:scale-95"
          title="Cetak format Berita Acara & Rekapitulasi ATK"
        >
          <svg class="w-4 h-4 text-slate-600 stroke-[2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
          </svg>
          <span>Cetak Rekap</span>
        </button>

        <!-- Registrasi Barang ATK Button -->
        <button
          type="button"
          onclick="openModal('modal-add-item')"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0b2341] hover:bg-[#13335e] text-white font-semibold text-xs sm:text-sm border border-amber-400/30 shadow-md shadow-slate-900/10 transition-all hover:scale-[1.02] active:scale-[0.98]"
        >
          <svg class="w-4 h-4 text-amber-400 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round"></path>
          </svg>
          <span>Registrasi Barang ATK</span>
        </button>
      </div>
    </div>
  </div>

  <!-- 4 Executive KPI Metric Cards with Circular Gauges -->
  <div class="no-print grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

    <!-- Card 1: Total Varian ATK -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs hover:shadow-md transition flex flex-col justify-between">
      <div class="flex items-center gap-4">
        <!-- Circular Gauge -->
        <div class="relative w-16 h-16 shrink-0 flex items-center justify-center">
          <svg class="w-full h-full gauge-circle" viewBox="0 0 36 36">
            <circle cx="18" cy="18" fill="none" r="14.5" stroke="#f1f5f9" stroke-width="3.2"></circle>
            <circle cx="18" cy="18" fill="none" r="14.5" stroke="#0b2341" stroke-dasharray="91" stroke-dashoffset="15" stroke-linecap="round" stroke-width="3.2"></circle>
          </svg>
          <span class="absolute text-xs font-black text-slate-900 font-mono">{{ $totalItems }}</span>
        </div>
        <!-- Card Details -->
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-[#0b2341]"></span>
            <p class="text-xs font-bold text-slate-700 uppercase tracking-wider">Total Katalog</p>
          </div>
          <h3 class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">{{ number_format($totalItems) }}</h3>
          <p class="text-[11px] text-slate-500 font-medium truncate">Item barang terdata</p>
        </div>
      </div>
      <div class="mt-3.5 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-600 font-medium">
        <span>Klasifikasi:</span>
        <span class="font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200/60">
          {{ $categories->count() }} Kategori Aktif
        </span>
      </div>
    </div>

    <!-- Card 2: Total Fisik Unit Terakumulasi -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs hover:shadow-md transition flex flex-col justify-between">
      <div class="flex items-center gap-4">
        <!-- Circular Gauge -->
        <div class="relative w-16 h-16 shrink-0 flex items-center justify-center">
          <svg class="w-full h-full gauge-circle" viewBox="0 0 36 36">
            <circle cx="18" cy="18" fill="none" r="14.5" stroke="#f1f5f9" stroke-width="3.2"></circle>
            <circle cx="18" cy="18" fill="none" r="14.5" stroke="#059669" stroke-dasharray="91" stroke-dashoffset="25" stroke-linecap="round" stroke-width="3.2"></circle>
          </svg>
          <div class="absolute w-7 h-7 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center">
            <svg class="w-3.5 h-3.5 stroke-[2.2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
        </div>
        <!-- Card Details -->
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
            <p class="text-xs font-bold text-slate-700 uppercase tracking-wider">Total Fisik Unit</p>
          </div>
          <h3 class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">{{ number_format($totalPhysicalStock) }}</h3>
          <p class="text-[11px] text-slate-500 font-medium truncate">Kuantitas riil di gudang</p>
        </div>
      </div>
      <div class="mt-3.5 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-600 font-medium">
        <span>Kondisi Fisik:</span>
        <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60">
          Stok Siap Distribusi
        </span>
      </div>
    </div>

    <!-- Card 3: Stok Menipis (Threshold Alert) -->
    <div class="bg-white rounded-2xl p-5 border border-amber-200/90 shadow-xs hover:shadow-md transition flex flex-col justify-between bg-gradient-to-br from-white via-white to-amber-50/30">
      <div class="flex items-center gap-4">
        <!-- Circular Gauge -->
        <div class="relative w-16 h-16 shrink-0 flex items-center justify-center">
          <svg class="w-full h-full gauge-circle" viewBox="0 0 36 36">
            <circle cx="18" cy="18" fill="none" r="14.5" stroke="#fef3c7" stroke-width="3.2"></circle>
            <circle cx="18" cy="18" fill="none" r="14.5" stroke="#d97706" stroke-dasharray="91" stroke-dashoffset="40" stroke-linecap="round" stroke-width="3.2"></circle>
          </svg>
          <span class="absolute text-xs font-black text-amber-700 font-mono">{{ $lowStockCount }}</span>
        </div>
        <!-- Card Details -->
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <p class="text-xs font-bold text-amber-700 uppercase tracking-wider">Stok Menipis</p>
          </div>
          <h3 class="text-2xl font-black text-amber-700 mt-0.5 tracking-tight">{{ number_format($lowStockCount) }}</h3>
          <p class="text-[11px] text-slate-500 font-medium truncate">&le; ambang minimum buffer</p>
        </div>
      </div>
      <div class="mt-3.5 pt-3 border-t border-amber-100 flex items-center justify-between text-[11px] text-amber-800 font-medium">
        <span>Pengadaan:</span>
        <a href="{{ route('inventory.items.index', ['stock_status' => 'low_stock']) }}" class="font-bold text-amber-700 hover:text-amber-800 underline">
          Filter Menipis &rarr;
        </a>
      </div>
    </div>

    <!-- Card 4: Stok Habis (Out of Stock Alert) -->
    <div class="bg-white rounded-2xl p-5 border border-rose-200/90 shadow-xs hover:shadow-md transition flex flex-col justify-between bg-gradient-to-br from-white via-white to-rose-50/30">
      <div class="flex items-center gap-4">
        <!-- Circular Gauge -->
        <div class="relative w-16 h-16 shrink-0 flex items-center justify-center">
          <svg class="w-full h-full gauge-circle" viewBox="0 0 36 36">
            <circle cx="18" cy="18" fill="none" r="14.5" stroke="#ffe4e6" stroke-width="3.2"></circle>
            <circle cx="18" cy="18" fill="none" r="14.5" stroke="#e11d48" stroke-dasharray="91" stroke-dashoffset="65" stroke-linecap="round" stroke-width="3.2"></circle>
          </svg>
          <span class="absolute text-xs font-black text-rose-700 font-mono">{{ $outOfStockCount }}</span>
        </div>
        <!-- Card Details -->
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-rose-600"></span>
            <p class="text-xs font-bold text-rose-700 uppercase tracking-wider">Stok Habis</p>
          </div>
          <h3 class="text-2xl font-black text-rose-700 mt-0.5 tracking-tight">{{ number_format($outOfStockCount) }}</h3>
          <p class="text-[11px] text-slate-500 font-medium truncate">Saldo persediaan 0 unit</p>
        </div>
      </div>
      <div class="mt-3.5 pt-3 border-t border-rose-100 flex items-center justify-between text-[11px] text-rose-800 font-medium">
        <span>Tindak Lanjut:</span>
        <a href="{{ route('inventory.items.index', ['stock_status' => 'out_of_stock']) }}" class="font-bold text-rose-700 hover:text-rose-800 underline">
          Filter Habis &rarr;
        </a>
      </div>
    </div>

  </div>

  <!-- Executive Filter & Search Console -->
  <div class="no-print bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-4">
    <!-- Quick Filter Segments -->
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
      <div class="flex flex-wrap items-center gap-1.5 text-xs">
        <span class="text-slate-400 font-semibold mr-1 uppercase text-[10px] tracking-wider">Status:</span>

        <!-- Segment: Semua -->
        <a
          href="{{ route('inventory.items.index', request()->except(['stock_status', 'page'])) }}"
          class="px-3 py-1.5 rounded-lg font-semibold text-xs transition {{ !request()->filled('stock_status') ? 'bg-[#0b2341] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
        >
          Semua Item ({{ $totalItems }})
        </a>

        <!-- Segment: Tersedia -->
        <a
          href="{{ route('inventory.items.index', array_merge(request()->except('page'), ['stock_status' => 'available'])) }}"
          class="px-3 py-1.5 rounded-lg font-semibold text-xs transition flex items-center gap-1.5 {{ request('stock_status') === 'available' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
        >
          <span class="w-1.5 h-1.5 rounded-full {{ request('stock_status') === 'available' ? 'bg-white' : 'bg-emerald-500' }}"></span>
          Tersedia
        </a>

        <!-- Segment: Menipis -->
        <a
          href="{{ route('inventory.items.index', array_merge(request()->except('page'), ['stock_status' => 'low_stock'])) }}"
          class="px-3 py-1.5 rounded-lg font-semibold text-xs transition flex items-center gap-1.5 {{ request('stock_status') === 'low_stock' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
        >
          <span class="w-1.5 h-1.5 rounded-full {{ request('stock_status') === 'low_stock' ? 'bg-white' : 'bg-amber-500' }}"></span>
          Stok Menipis ({{ $lowStockCount }})
        </a>

        <!-- Segment: Habis -->
        <a
          href="{{ route('inventory.items.index', array_merge(request()->except('page'), ['stock_status' => 'out_of_stock'])) }}"
          class="px-3 py-1.5 rounded-lg font-semibold text-xs transition flex items-center gap-1.5 {{ request('stock_status') === 'out_of_stock' ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
        >
          <span class="w-1.5 h-1.5 rounded-full {{ request('stock_status') === 'out_of_stock' ? 'bg-white' : 'bg-rose-500' }}"></span>
          Stok Habis ({{ $outOfStockCount }})
        </a>
      </div>

      <div class="text-[11px] text-slate-400 font-mono">
        Total Filtered: <span class="font-bold text-slate-700">{{ $items->total() }}</span> barang
      </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('inventory.items.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
      @if (request()->filled('stock_status'))
        <input type="hidden" name="stock_status" value="{{ request('stock_status') }}" />
      @endif

      <!-- Search Input -->
      <div class="lg:col-span-5 relative">
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
        <input
          type="text"
          name="keyword"
          value="{{ request('keyword') }}"
          placeholder="Cari Kode Barang (ATK-...), Barcode, atau Nama..."
          class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341] transition font-medium"
        />
      </div>

      <!-- Category Filter -->
      <div class="lg:col-span-3">
        <select
          name="category_id"
          class="w-full px-3 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341] transition font-medium text-slate-700"
        >
          <option value="">-- Semua Kategori ATK --</option>
          @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
              {{ $cat->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Status Operasional Filter -->
      <div class="lg:col-span-2">
        <select
          name="status"
          class="w-full px-3 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341] transition font-medium text-slate-700"
        >
          <option value="">-- Status Item --</option>
          <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif Beredar</option>
          <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        </select>
      </div>

      <!-- Filter Buttons -->
      <div class="lg:col-span-2 flex items-center gap-2">
        <button
          type="submit"
          class="flex-1 py-2.5 px-4 rounded-xl bg-[#0b2341] hover:bg-[#13335e] text-white font-semibold text-xs tracking-wider uppercase transition text-center shadow-xs"
        >
          Terapkan
        </button>
        @if (request()->hasAny(['keyword', 'category_id', 'stock_status', 'status']))
          <a
            href="{{ route('inventory.items.index') }}"
            class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition text-center"
            title="Bersihkan Semua Filter"
          >
            Reset
          </a>
        @endif
      </div>
    </form>
  </div>

  <!-- Ministry Master Table Card -->
  <div class="table-container bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
    <!-- Table Sub-header -->
    <div class="px-5 py-4 bg-slate-50/70 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
        <h3 class="font-bold text-slate-900 text-sm tracking-tight uppercase">
          Buku Induk Inventaris Barang ATK
        </h3>
        <span class="text-xs text-slate-500 font-normal">({{ $items->total() }} barang terdaftar)</span>
      </div>
      <div class="flex items-center gap-3 text-xs text-slate-500 font-medium">
        <span>Halaman <strong class="text-slate-800">{{ $items->currentPage() }}</strong> dari {{ $items->lastPage() }}</span>
      </div>
    </div>

    <!-- Table Element -->
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs sm:text-sm">
        <thead class="bg-[#0b2341] text-white uppercase tracking-wider text-[11px] font-bold border-b border-slate-200 select-none">
          <tr>
            <th class="py-3.5 px-4 w-12 text-center text-slate-300">No</th>
            <th class="py-3.5 px-4">Kode &amp; Barcode</th>
            <th class="py-3.5 px-4">Nama Barang &amp; Spesifikasi</th>
            <th class="py-3.5 px-4">Kategori</th>
            <th class="py-3.5 px-4 text-center">Stok Fisik</th>
            <th class="py-3.5 px-4 text-center">Safety Stock (Min/Max)</th>
            <th class="py-3.5 px-4 text-center">Status Ketersediaan</th>
            <th class="py-3.5 px-4">Lokasi Gudang</th>
            <th class="py-3.5 px-4 text-center no-print">Tindakan</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
          @forelse ($items as $index => $item)
            <tr class="hover:bg-slate-50/80 transition duration-150 {{ $item->status === 'inactive' ? 'bg-slate-50/40 opacity-70' : '' }}">
              <!-- Number -->
              <td class="py-3.5 px-4 text-center font-mono text-xs text-slate-500">
                {{ $items->firstItem() + $index }}
              </td>

              <!-- Kode ATK & Barcode -->
              <td class="py-3.5 px-4 whitespace-nowrap">
                <div class="space-y-1">
                  <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-100 text-slate-900 border border-slate-200 font-mono text-[11px] font-bold">
                    <svg class="w-3 h-3 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                    </svg>
                    <span>{{ $item->code }}</span>
                  </div>
                  @if ($item->barcode)
                    <div class="text-[10px] text-slate-500 font-mono flex items-center gap-1 pl-1">
                      <span class="text-slate-400">|||</span>
                      <span>{{ $item->barcode }}</span>
                    </div>
                  @endif
                </div>
              </td>

              <!-- Nama Barang -->
              <td class="py-3.5 px-4">
                <div class="space-y-0.5">
                  <div class="font-bold text-slate-900 text-sm hover:text-blue-700 transition">
                    {{ $item->name }}
                  </div>
                  @if ($item->description)
                    <p class="text-[11px] text-slate-500 line-clamp-1 max-w-sm">{{ $item->description }}</p>
                  @endif
                  @if ($item->supplier)
                    <p class="text-[10px] text-slate-400 font-medium">Rekanan: {{ $item->supplier->name }}</p>
                  @endif
                </div>
              </td>

              <!-- Kategori -->
              <td class="py-3.5 px-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                  {{ $item->category?->name ?? 'Umum' }}
                </span>
              </td>

              <!-- Stok Fisik & Gauge Bar -->
              <td class="py-3.5 px-4 text-center whitespace-nowrap">
                <div class="inline-flex flex-col items-center">
                  <div class="flex items-baseline gap-1 font-mono">
                    <span class="text-base font-black {{ $item->current_stock <= 0 ? 'text-rose-700' : ($item->current_stock <= $item->minimum_stock ? 'text-amber-700' : 'text-slate-900') }}">
                      {{ number_format($item->current_stock) }}
                    </span>
                    <span class="text-xs font-semibold text-slate-500">{{ $item->unit }}</span>
                  </div>
                  <!-- Mini Progress Bar -->
                  <div class="w-20 bg-slate-100 h-1.5 rounded-full mt-1 overflow-hidden border border-slate-200/60">
                    <div
                      class="h-full rounded-full transition-all {{ $item->current_stock <= 0 ? 'bg-rose-600' : ($item->current_stock <= $item->minimum_stock ? 'bg-amber-500' : 'bg-blue-600') }}"
                      style="width: {{ $item->stock_percentage }}%"
                    ></div>
                  </div>
                </div>
              </td>

              <!-- Min / Target Stock -->
              <td class="py-3.5 px-4 text-center text-xs whitespace-nowrap">
                <div class="font-mono text-slate-700">
                  <span class="font-bold text-amber-700" title="Safety Stock Minimum">{{ $item->minimum_stock }}</span>
                  <span class="text-slate-400 mx-1">/</span>
                  <span class="font-semibold text-slate-600" title="Target Stok">{{ $item->target_stock }}</span>
                </div>
                <span class="text-[10px] text-slate-400 block">{{ $item->unit }}</span>
              </td>

              <!-- Status Ketersediaan (Pill Capsule) -->
              <td class="py-3.5 px-4 text-center whitespace-nowrap">
                @if ($item->stock_status === 'available')
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    Tersedia
                  </span>
                @elseif ($item->stock_status === 'low_stock')
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-300">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                    Menipis
                  </span>
                @else
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-300">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                    Habis
                  </span>
                @endif
              </td>

              <!-- Lokasi Gudang -->
              <td class="py-3.5 px-4 text-xs whitespace-nowrap text-slate-600 font-medium">
                <div class="flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                  <span>{{ $item->storage_location ?? 'Gudang Utama' }}</span>
                </div>
              </td>

              <!-- Action Buttons -->
              <td class="py-3.5 px-4 text-center whitespace-nowrap no-print">
                <div class="inline-flex items-center gap-1">
                  <!-- Preview Button -->
                  <button
                    type="button"
                    onclick="openDetailModal({{ json_encode($item) }})"
                    class="p-1.5 rounded-lg text-slate-600 hover:text-blue-700 hover:bg-blue-50 border border-transparent hover:border-blue-200 transition"
                    title="Lihat Kartu Inventaris ATK"
                  >
                    <svg class="w-4 h-4 stroke-[2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </button>

                  <!-- Edit Button -->
                  <button
                    type="button"
                    onclick="openEditModal({{ json_encode($item) }})"
                    class="p-1.5 rounded-lg text-slate-600 hover:text-amber-700 hover:bg-amber-50 border border-transparent hover:border-amber-200 transition"
                    title="Ubah Data Barang"
                  >
                    <svg class="w-4 h-4 stroke-[2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>

                  <!-- Toggle Active Status Button -->
                  <form
                    action="{{ route('inventory.items.destroy', $item) }}"
                    method="POST"
                    class="inline"
                    onsubmit="window.confirmSubmit(event, {
                      title: '{{ $item->status === 'active' ? 'Nonaktifkan Item Barang' : 'Aktifkan Kembali Barang' }}',
                      message: '{{ $item->status === 'active' ? 'Barang yang dinonaktifkan tidak akan muncul pada pilihan formulir transaksi pengeluaran (Stock Out).' : 'Barang akan kembali beredar dan dapat didistribusikan kepada pemohon.' }}',
                      badgeText: '{{ addslashes($item->name) }} ({{ $item->code }})',
                      confirmText: '{{ $item->status === 'active' ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}',
                      confirmButtonClass: '{{ $item->status === 'active' ? 'bg-rose-600 hover:bg-rose-700 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}',
                      iconType: '{{ $item->status === 'active' ? 'warning' : 'primary' }}'
                    })"
                  >
                    @csrf
                    @method('DELETE')
                    <button
                      type="submit"
                      class="p-1.5 rounded-lg {{ $item->status === 'active' ? 'text-slate-400 hover:text-rose-700 hover:bg-rose-50' : 'text-slate-400 hover:text-emerald-700 hover:bg-emerald-50' }} border border-transparent transition"
                      title="{{ $item->status === 'active' ? 'Nonaktifkan Barang' : 'Aktifkan Barang' }}"
                    >
                      <svg class="w-4 h-4 stroke-[2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if ($item->status === 'active')
                          <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        @else
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        @endif
                      </svg>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="py-12 text-center text-slate-400">
                <div class="max-w-sm mx-auto text-center space-y-2">
                  <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                  </div>
                  <h4 class="font-bold text-slate-800 text-sm">Tidak Ada Data Barang Ditemukan</h4>
                  <p class="text-xs text-slate-500">
                    Data barang ATK tidak cocok dengan kriteria pencarian atau filter yang dipilih. Silakan atur ulang filter pencarian Anda.
                  </p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination Footer -->
    @if ($items->hasPages())
      <div class="no-print px-5 py-4 border-t border-slate-200 bg-slate-50/50 flex items-center justify-between">
        {{ $items->links() }}
      </div>
    @endif
  </div>

</main>

<!-- ============================================================== -->
<!-- MODAL 1: FORMULIR REGISTRASI BARANG ATK BARU                  -->
<!-- ============================================================== -->
<div id="modal-add-item" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl max-w-2xl w-full border border-slate-200 shadow-2xl overflow-hidden animate-scale-in">
    <!-- Header Modal Dinas -->
    <div class="px-6 py-4.5 bg-[#0b2341] text-white flex items-center justify-between border-b border-amber-400/30">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-white/10 text-amber-400 flex items-center justify-center shrink-0 border border-white/20">
          <svg class="w-5 h-5 stroke-[2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
          </svg>
        </div>
        <div>
          <h3 class="font-bold text-white text-base leading-tight uppercase tracking-wide">
            Formulir Registrasi Barang ATK Baru
          </h3>
          <p class="text-[11px] text-slate-300">
            Penambahan data inventaris BPTD Kelas II Jawa Timur
          </p>
        </div>
      </div>
      <button type="button" onclick="closeModal('modal-add-item')" class="text-slate-400 hover:text-white transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <form action="{{ route('inventory.items.store') }}" method="POST" class="p-6 space-y-5 max-h-[80vh] overflow-y-auto">
      @csrf

      <!-- BAGIAN I: IDENTIFIKASI BARANG -->
      <div class="space-y-3">
        <div class="flex items-center gap-2 border-b border-slate-200 pb-1.5">
          <span class="w-1.5 h-4 rounded-full bg-[#0b2341]"></span>
          <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider">I. Identitas &amp; Klasifikasi Barang</h4>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Kode ATK -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode Inventaris ATK <span class="text-rose-600">*</span></label>
            <input
              type="text"
              name="code"
              required
              value="{{ old('code', 'ATK-' . date('Y') . '-' . str_pad(rand(100, 999), 4, '0', STR_PAD_LEFT)) }}"
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 font-mono bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341] font-semibold"
            />
          </div>

          <!-- Barcode -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Barcode / SKU Scanner</label>
            <input
              type="text"
              name="barcode"
              value="{{ old('barcode') }}"
              placeholder="Contoh: 899123456789"
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 font-mono focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341]"
            />
          </div>

          <!-- Nama Barang -->
          <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Barang Lengkap <span class="text-rose-600">*</span></label>
            <input
              type="text"
              name="name"
              required
              value="{{ old('name') }}"
              placeholder="Contoh: Kertas HVS A4 80gr Sinar Dunia"
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341] font-medium"
            />
          </div>

          <!-- Kategori -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kategori Barang <span class="text-rose-600">*</span></label>
            <select
              name="category_id"
              required
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341] text-slate-700 font-medium"
            >
              <option value="">-- Pilih Kategori --</option>
              @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                  {{ $cat->name }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Satuan -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Satuan Baku <span class="text-rose-600">*</span></label>
            <select
              name="unit_id"
              required
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341] text-slate-700 font-medium"
            >
              <option value="">-- Pilih Satuan --</option>
              @foreach ($units as $u)
                <option value="{{ $u->id }}" {{ old('unit_id') == $u->id ? 'selected' : '' }}>
                  {{ $u->name }} ({{ $u->code }})
                </option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <!-- BAGIAN II: PARAMETER PENGENDALIAN STOK -->
      <div class="space-y-3 pt-2">
        <div class="flex items-center gap-2 border-b border-slate-200 pb-1.5">
          <span class="w-1.5 h-4 rounded-full bg-emerald-600"></span>
          <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider">II. Parameter Pengendalian Stok (Stock Control)</h4>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <!-- Stok Awal -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Stok Awal Masuk</label>
            <input
              type="number"
              name="current_stock"
              min="0"
              value="{{ old('current_stock', 0) }}"
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 font-mono focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341]"
            />
          </div>

          <!-- Minimum Stok -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Safety Stock (Min) <span class="text-rose-600">*</span></label>
            <input
              type="number"
              name="minimum_stock"
              required
              min="0"
              value="{{ old('minimum_stock', 10) }}"
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 font-mono focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341] text-amber-700 font-bold"
            />
          </div>

          <!-- Target Stok -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Target Ideal (Max) <span class="text-rose-600">*</span></label>
            <input
              type="number"
              name="target_stock"
              required
              min="0"
              value="{{ old('target_stock', 50) }}"
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 font-mono focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341] text-blue-700 font-bold"
            />
          </div>
        </div>
      </div>

      <!-- BAGIAN III: LOKASI & REKANAN -->
      <div class="space-y-3 pt-2">
        <div class="flex items-center gap-2 border-b border-slate-200 pb-1.5">
          <span class="w-1.5 h-4 rounded-full bg-blue-600"></span>
          <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider">III. Lokasi Gudang &amp; Rekanan</h4>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Lokasi Penyimpanan -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Lokasi Rak / Lemari</label>
            <input
              type="text"
              name="storage_location"
              value="{{ old('storage_location') }}"
              placeholder="Contoh: Gudang ATK - Rak B2"
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341]"
            />
          </div>

          <!-- Supplier -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Supplier / Penyedia</label>
            <select
              name="supplier_id"
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341] text-slate-700 font-medium"
            >
              <option value="">-- Pilih Rekanan (Opsional) --</option>
              @foreach ($suppliers as $sup)
                <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                  {{ $sup->name }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Status Operasional -->
          <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status Operasional <span class="text-rose-600">*</span></label>
            <select
              name="status"
              required
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341] text-slate-700 font-medium"
            >
              <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif Digunakan (Dapat Didistribusikan)</option>
              <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif / Tidak Beredar</option>
            </select>
          </div>

          <!-- Deskripsi -->
          <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Spesifikasi Tambahan / Catatan Barang</label>
            <textarea
              name="description"
              rows="2"
              placeholder="Spesifikasi merek, ukuran, warna, atau catatan khusus dinas..."
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341]"
            >{{ old('description') }}</textarea>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
        <button
          type="button"
          onclick="closeModal('modal-add-item')"
          class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition"
        >
          Batalkan
        </button>
        <button
          type="submit"
          class="px-5 py-2.5 rounded-xl bg-[#0b2341] hover:bg-[#13335e] text-white font-semibold text-xs border border-amber-400/30 shadow-md shadow-slate-900/10 transition"
        >
          Simpan Data Inventaris
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ============================================================== -->
<!-- MODAL 2: FORMULIR UBAH DATA BARANG ATK                        -->
<!-- ============================================================== -->
<div id="modal-edit-item" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl max-w-2xl w-full border border-slate-200 shadow-2xl overflow-hidden animate-scale-in">
    <!-- Header Modal Dinas -->
    <div class="px-6 py-4.5 bg-[#0b2341] text-white flex items-center justify-between border-b border-amber-400/30">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-white/10 text-amber-400 flex items-center justify-center shrink-0 border border-white/20">
          <svg class="w-5 h-5 stroke-[2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
          </svg>
        </div>
        <div>
          <h3 class="font-bold text-white text-base leading-tight uppercase tracking-wide">
            Ubah Data Inventaris ATK
          </h3>
          <p class="text-[11px] text-slate-300">
            Perbarui parameter barang persediaan BPTD Kelas II Jawa Timur
          </p>
        </div>
      </div>
      <button type="button" onclick="closeModal('modal-edit-item')" class="text-slate-400 hover:text-white transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <form id="form-edit-item" method="POST" class="p-6 space-y-5 max-h-[80vh] overflow-y-auto">
      @csrf
      @method('PUT')

      <!-- BAGIAN I -->
      <div class="space-y-3">
        <div class="flex items-center gap-2 border-b border-slate-200 pb-1.5">
          <span class="w-1.5 h-4 rounded-full bg-[#0b2341]"></span>
          <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider">I. Identitas &amp; Klasifikasi Barang</h4>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Kode ATK -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode Inventaris ATK <span class="text-rose-600">*</span></label>
            <input
              type="text"
              id="edit-code"
              name="code"
              required
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 font-mono bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341] font-semibold"
            />
          </div>

          <!-- Barcode -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Barcode / SKU Scanner</label>
            <input
              type="text"
              id="edit-barcode"
              name="barcode"
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 font-mono focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341]"
            />
          </div>

          <!-- Nama Barang -->
          <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Barang Lengkap <span class="text-rose-600">*</span></label>
            <input
              type="text"
              id="edit-name"
              name="name"
              required
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341] font-medium"
            />
          </div>

          <!-- Kategori -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kategori Barang <span class="text-rose-600">*</span></label>
            <select
              id="edit-category-id"
              name="category_id"
              required
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341] text-slate-700 font-medium"
            >
              <option value="">-- Pilih Kategori --</option>
              @foreach ($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>

          <!-- Satuan -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Satuan Baku <span class="text-rose-600">*</span></label>
            <select
              id="edit-unit-id"
              name="unit_id"
              required
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341] text-slate-700 font-medium"
            >
              <option value="">-- Pilih Satuan --</option>
              @foreach ($units as $u)
                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->code }})</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <!-- BAGIAN II -->
      <div class="space-y-3 pt-2">
        <div class="flex items-center gap-2 border-b border-slate-200 pb-1.5">
          <span class="w-1.5 h-4 rounded-full bg-emerald-600"></span>
          <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider">II. Parameter Pengendalian Stok</h4>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Minimum Stok -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Safety Stock (Min) <span class="text-rose-600">*</span></label>
            <input
              type="number"
              id="edit-minimum-stock"
              name="minimum_stock"
              required
              min="0"
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 font-mono focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341] text-amber-700 font-bold"
            />
          </div>

          <!-- Target Stok -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Target Ideal (Max) <span class="text-rose-600">*</span></label>
            <input
              type="number"
              id="edit-target-stock"
              name="target_stock"
              required
              min="0"
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 font-mono focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341] text-blue-700 font-bold"
            />
          </div>
        </div>
      </div>

      <!-- BAGIAN III -->
      <div class="space-y-3 pt-2">
        <div class="flex items-center gap-2 border-b border-slate-200 pb-1.5">
          <span class="w-1.5 h-4 rounded-full bg-blue-600"></span>
          <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider">III. Lokasi Gudang &amp; Rekanan</h4>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Lokasi Penyimpanan -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Lokasi Rak / Lemari</label>
            <input
              type="text"
              id="edit-storage-location"
              name="storage_location"
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341]"
            />
          </div>

          <!-- Supplier -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Supplier Rekanan</label>
            <select
              id="edit-supplier-id"
              name="supplier_id"
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341] text-slate-700 font-medium"
            >
              <option value="">-- Pilih Rekanan (Opsional) --</option>
              @foreach ($suppliers as $sup)
                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
              @endforeach
            </select>
          </div>

          <!-- Status Operasional -->
          <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status Operasional <span class="text-rose-600">*</span></label>
            <select
              id="edit-status"
              name="status"
              required
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341] text-slate-700 font-medium"
            >
              <option value="active">Aktif Digunakan (Dapat Didistribusikan)</option>
              <option value="inactive">Nonaktif / Tidak Beredar</option>
            </select>
          </div>

          <!-- Deskripsi -->
          <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Spesifikasi Tambahan / Catatan Barang</label>
            <textarea
              id="edit-description"
              name="description"
              rows="2"
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-[#0b2341]"
            ></textarea>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
        <button
          type="button"
          onclick="closeModal('modal-edit-item')"
          class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition"
        >
          Batalkan
        </button>
        <button
          type="submit"
          class="px-5 py-2.5 rounded-xl bg-[#0b2341] hover:bg-[#13335e] text-white font-semibold text-xs border border-amber-400/30 shadow-md shadow-slate-900/10 transition"
        >
          Perbarui Data Inventaris
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ============================================================== -->
<!-- MODAL 3: KARTU INVENTARIS BARANG ATK (DETAIL VIEW)            -->
<!-- ============================================================== -->
<div id="modal-detail-item" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl max-w-xl w-full border border-slate-200 shadow-2xl overflow-hidden animate-scale-in">
    <!-- Header Kartu Inventaris -->
    <div class="px-6 py-4 bg-[#0b2341] text-white flex items-center justify-between border-b border-amber-400/30">
      <div class="flex items-center gap-3">
        <img src="{{ asset('assets/logo-kemenhub.png') }}" alt="Logo" class="w-8 h-8 object-contain" />
        <div>
          <h3 class="font-bold text-white text-sm uppercase tracking-wide">
            Kartu Riwayat Inventaris ATK
          </h3>
          <p class="text-[10px] text-amber-400 font-mono tracking-wider">
            BPTD KELAS II JAWA TIMUR
          </p>
        </div>
      </div>
      <button type="button" onclick="closeModal('modal-detail-item')" class="text-slate-400 hover:text-white transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <!-- Body Kartu -->
    <div class="p-6 space-y-4 text-xs sm:text-sm">
      <!-- Title & Code Badge -->
      <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex items-start justify-between gap-3">
        <div>
          <span id="detail-code" class="inline-block px-2.5 py-0.5 rounded-md bg-[#0b2341] text-amber-400 font-mono font-bold text-xs">
            ATK-0000
          </span>
          <h4 id="detail-name" class="font-black text-slate-900 text-base mt-1.5 leading-snug">
            Nama Barang ATK
          </h4>
          <p id="detail-category" class="text-xs text-slate-500 font-medium mt-0.5">
            Kategori: Alat Tulis
          </p>
        </div>
        <div id="detail-status-pill">
          <!-- Filled by JS -->
        </div>
      </div>

      <!-- Grid Data -->
      <div class="grid grid-cols-2 gap-3 text-xs">
        <div class="p-3 bg-white border border-slate-200 rounded-xl">
          <span class="text-slate-400 text-[10px] uppercase font-bold block">Stok Fisik Tersedia</span>
          <span id="detail-current-stock" class="text-lg font-black text-slate-900 font-mono">0</span>
          <span id="detail-unit" class="text-slate-500 font-medium ml-1">Pcs</span>
        </div>

        <div class="p-3 bg-white border border-slate-200 rounded-xl">
          <span class="text-slate-400 text-[10px] uppercase font-bold block">Safety Stock (Min/Target)</span>
          <span id="detail-min-target" class="text-sm font-bold text-slate-800 font-mono">10 / 50</span>
          <span class="text-[10px] text-slate-400 block mt-0.5">Ambang batas persediaan</span>
        </div>

        <div class="p-3 bg-white border border-slate-200 rounded-xl">
          <span class="text-slate-400 text-[10px] uppercase font-bold block">Lokasi Penyimpanan</span>
          <span id="detail-location" class="font-bold text-slate-800">-</span>
        </div>

        <div class="p-3 bg-white border border-slate-200 rounded-xl">
          <span class="text-slate-400 text-[10px] uppercase font-bold block">Rekanan Supplier</span>
          <span id="detail-supplier" class="font-bold text-slate-800">-</span>
        </div>
      </div>

      <!-- Barcode & Deskripsi -->
      <div class="p-3 bg-slate-50/70 border border-slate-200 rounded-xl space-y-1.5 text-xs">
        <div class="flex items-center justify-between">
          <span class="text-slate-500 font-semibold">Barcode / SKU:</span>
          <span id="detail-barcode" class="font-mono text-slate-800 font-bold">-</span>
        </div>
        <div class="pt-1.5 border-t border-slate-200/60">
          <span class="text-slate-500 font-semibold block mb-0.5">Spesifikasi &amp; Catatan:</span>
          <p id="detail-description" class="text-slate-700 italic">Tidak ada catatan spesifikasi.</p>
        </div>
      </div>

      <!-- Footer Buttons -->
      <div class="pt-3 border-t border-slate-200 flex items-center justify-end">
        <button
          type="button"
          onclick="closeModal('modal-detail-item')"
          class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs transition"
        >
          Tutup Pratinjau
        </button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  function openModal(id) {
    const el = document.getElementById(id);
    if (el) {
      el.classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
    }
  }

  function closeModal(id) {
    const el = document.getElementById(id);
    if (el) {
      el.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }
  }

  function openEditModal(item) {
    const form = document.getElementById('form-edit-item');
    form.action = `/inventory/items/${item.id}`;

    document.getElementById('edit-code').value = item.code || '';
    document.getElementById('edit-barcode').value = item.barcode || '';
    document.getElementById('edit-name').value = item.name || '';
    document.getElementById('edit-category-id').value = item.category_id || '';
    document.getElementById('edit-unit-id').value = item.unit_id || '';
    document.getElementById('edit-minimum-stock').value = item.minimum_stock ?? 10;
    document.getElementById('edit-target-stock').value = item.target_stock ?? 50;
    document.getElementById('edit-storage-location').value = item.storage_location || '';
    document.getElementById('edit-supplier-id').value = item.supplier_id || '';
    document.getElementById('edit-status').value = item.status || 'active';
    document.getElementById('edit-description').value = item.description || '';

    openModal('modal-edit-item');
  }

  function openDetailModal(item) {
    document.getElementById('detail-code').textContent = item.code || 'ATK-????';
    document.getElementById('detail-name').textContent = item.name || '-';
    document.getElementById('detail-category').textContent = `Kategori: ${item.category?.name || 'Umum'}`;
    document.getElementById('detail-current-stock').textContent = item.current_stock ?? 0;
    document.getElementById('detail-unit').textContent = item.unit || 'Pcs';
    document.getElementById('detail-min-target').textContent = `${item.minimum_stock ?? 0} / ${item.target_stock ?? 0} ${item.unit || ''}`;
    document.getElementById('detail-location').textContent = item.storage_location || 'Gudang Utama';
    document.getElementById('detail-supplier').textContent = item.supplier?.name || '-';
    document.getElementById('detail-barcode').textContent = item.barcode || 'Tidak Ada Barcode';
    document.getElementById('detail-description').textContent = item.description || 'Tidak ada catatan spesifikasi tambahan.';

    const pillContainer = document.getElementById('detail-status-pill');
    let pillHtml = '';
    if (item.stock_status === 'available') {
      pillHtml = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-300"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>Tersedia</span>';
    } else if (item.stock_status === 'low_stock') {
      pillHtml = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-300"><span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-pulse"></span>Stok Menipis</span>';
    } else {
      pillHtml = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-300"><span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>Stok Habis</span>';
    }
    pillContainer.innerHTML = pillHtml;

    openModal('modal-detail-item');
  }

  // Close modals on Escape key
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
      closeModal('modal-add-item');
      closeModal('modal-edit-item');
      closeModal('modal-detail-item');
    }
  });
</script>
@endpush
@endsection
