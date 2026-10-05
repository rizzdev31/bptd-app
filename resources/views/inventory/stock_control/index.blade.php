@extends('layouts.app')

@section('title', 'Kendali Stok & Audit Persediaan ATK - BPTD Kelas II Jawa Timur')

@section('content')
<main class="main-content flex-1 max-w-430 w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

  <!-- ============================================================== -->
  <!-- 1. HEADER BAR: KENDALI STOK & AUDIT PERSEDIAAN ATK             -->
  <!-- ============================================================== -->
  <div class="no-print bg-white rounded-2xl border border-slate-200/80 shadow-2xs px-5 py-4 transition anim-fade-in-up">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      
      <!-- Sisi Kiri: Logo Emblem & Identitas BPTD -->
      <div class="flex items-center gap-3.5">
        <div class="w-11 h-11 rounded-xl bg-slate-50 border border-slate-200/80 p-1.5 flex items-center justify-center shrink-0 shadow-2xs">
          <img src="{{ asset('assets/logo-kemenhub.png') }}" alt="Logo Kemenhub" class="w-full h-full object-contain" />
        </div>
        <div>
          <div class="flex items-center gap-2 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
            <span>BPTD Kelas II Jawa Timur</span>
            <span>&bull;</span>
            <span class="text-blue-600 font-bold">Logistik ATK</span>
          </div>
          <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight leading-snug">
            Kendali Stok &amp; Audit Persediaan
          </h1>
        </div>
      </div>

      <!-- Sisi Kanan: Action Buttons -->
      <div class="flex items-center gap-2.5 shrink-0 self-start sm:self-center flex-wrap">
        <button
          type="button"
          onclick="openAdjustmentModal()"
          class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-bold text-xs transition shadow-xs cursor-pointer"
        >
          <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
          </svg>
          <span>Koreksi Stok (Adj)</span>
        </button>

        <button
          type="button"
          onclick="openOpnameModal()"
          class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs transition shadow-xs cursor-pointer"
        >
          <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
          </svg>
          <span>Sesi Opname Fisik</span>
        </button>
      </div>

    </div>
  </div>

  <!-- ============================================================== -->
  <!-- 2. METRIC QUICK CARDS (Status Kesehatan Persediaan Gudang)     -->
  <!-- ============================================================== -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Card 1: Total SKU Aktif & Total Unit -->
    <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up anim-delay-50">
      <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block truncate">Total Varian ATK</span>
        <span class="text-lg font-black text-slate-900 tracking-tight block">{{ number_format($totalActiveItems) }} Varian</span>
        <span class="text-[11px] font-bold text-blue-700 block truncate">{{ number_format($totalPhysicalUnits) }} Unit Fisik Total</span>
      </div>
    </div>

    <!-- Card 2: Stok Aman (Available) -->
    <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up anim-delay-100">
      <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block truncate">Stok Aman (Available)</span>
        <span class="text-lg font-black text-emerald-700 tracking-tight block">{{ number_format($availableItems) }} Varian</span>
        <span class="text-[11px] font-semibold text-emerald-600 block truncate">&gt; Batas Minimum</span>
      </div>
    </div>

    <!-- Card 3: Stok Menipis (Low Stock Warning) -->
    <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up anim-delay-150">
      <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <div class="flex items-center gap-1.5">
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block truncate">Stok Menipis</span>
          @if ($lowStockItems > 0)
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
          @endif
        </div>
        <span class="text-lg font-black text-amber-700 tracking-tight block">{{ number_format($lowStockItems) }} Varian</span>
        <span class="text-[11px] font-semibold text-amber-600 block truncate">&le; Batas Minimum</span>
      </div>
    </div>

    <!-- Card 4: Stok Habis (Out of Stock Alert) -->
    <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up anim-delay-200">
      <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <div class="flex items-center gap-1.5">
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block truncate">Stok Kosong</span>
          @if ($outOfStockItems > 0)
            <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
          @endif
        </div>
        <span class="text-lg font-black text-rose-700 tracking-tight block">{{ number_format($outOfStockItems) }} Varian</span>
        <span class="text-[11px] font-semibold text-rose-600 block truncate">0 Unit Tersedia</span>
      </div>
    </div>
  </div>

  <!-- ============================================================== -->
  <!-- 3. TAB NAVIGASI UTAMA (Segmented Control)                      -->
  <!-- ============================================================== -->
  <div class="flex items-center bg-slate-100/90 p-1 rounded-xl border border-slate-200/70 overflow-x-auto no-scrollbar gap-1">
    <button
      type="button"
      id="tab-btn-monitoring"
      onclick="switchControlTab('monitoring')"
      class="control-tab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all whitespace-nowrap bg-white text-blue-700 shadow-xs border border-slate-200/60 cursor-pointer"
    >
      <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
      </svg>
      <span>Status &amp; Monitoring</span>
      <span class="px-1.5 py-0.2 rounded-md bg-blue-50 text-blue-700 text-[10px] font-mono">{{ $totalActiveItems }}</span>
    </button>

    <button
      type="button"
      id="tab-btn-adjustment"
      onclick="switchControlTab('adjustment')"
      class="control-tab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all whitespace-nowrap text-slate-600 hover:text-slate-900 cursor-pointer"
    >
      <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
      </svg>
      <span>Koreksi Stok (Adjustment)</span>
      <span class="px-1.5 py-0.2 rounded-md bg-slate-200/70 text-slate-600 text-[10px] font-mono">{{ $adjustments->total() }}</span>
    </button>

    <button
      type="button"
      id="tab-btn-opname"
      onclick="switchControlTab('opname')"
      class="control-tab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all whitespace-nowrap text-slate-600 hover:text-slate-900 cursor-pointer"
    >
      <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
      </svg>
      <span>Stock Opname Fisik</span>
      <span class="px-1.5 py-0.2 rounded-md bg-slate-200/70 text-slate-600 text-[10px] font-mono">{{ $opnames->total() }}</span>
    </button>

    <button
      type="button"
      id="tab-btn-ledger"
      onclick="switchControlTab('ledger')"
      class="control-tab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all whitespace-nowrap text-slate-600 hover:text-slate-900 cursor-pointer"
    >
      <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
      </svg>
      <span>Buku Kartu Stok (Ledger)</span>
      <span class="px-1.5 py-0.2 rounded-md bg-slate-200/70 text-slate-600 text-[10px] font-mono">{{ $ledgers->total() }}</span>
    </button>
  </div>

  <!-- ============================================================== -->
  <!-- TAB PANE 1: MONITORING & ALERT STATUS STOK                     -->
  <!-- ============================================================== -->
  <div id="pane-monitoring" class="control-pane space-y-4">
    <!-- Filter Bar Monitoring -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs space-y-3">
      <form method="GET" action="{{ route('inventory.stock-control.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
        <input type="hidden" name="tab" value="monitoring" />

        <!-- Search Keyword -->
        <div class="lg:col-span-4 relative">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
            <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <input
            type="text"
            name="monitor_keyword"
            value="{{ request('monitor_keyword') }}"
            placeholder="Cari nama, kode, barcode, atau rak..."
            class="w-full pl-10 pr-4 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 font-medium"
          />
        </div>

        <!-- Filter Kategori -->
        <div class="lg:col-span-3">
          <select
            name="monitor_category_id"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden font-medium text-slate-700"
          >
            <option value="">Semua Kategori ATK</option>
            @foreach ($categories as $cat)
              <option value="{{ $cat->id }}" {{ request('monitor_category_id') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Filter Status Stok -->
        <div class="lg:col-span-3">
          <select
            name="monitor_status"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden font-medium text-slate-700"
          >
            <option value="">Semua Kondisi Stok</option>
            <option value="need_restock" {{ request('monitor_status') == 'need_restock' ? 'selected' : '' }}>Perlu Restock (Menipis + Habis)</option>
            <option value="low_stock" {{ request('monitor_status') == 'low_stock' ? 'selected' : '' }}>Stok Menipis (&le; Min)</option>
            <option value="out_of_stock" {{ request('monitor_status') == 'out_of_stock' ? 'selected' : '' }}>Stok Habis (0 Unit)</option>
            <option value="available" {{ request('monitor_status') == 'available' ? 'selected' : '' }}>Stok Aman (&gt; Min)</option>
          </select>
        </div>

        <!-- Tombol Filter -->
        <div class="lg:col-span-2 flex items-center gap-2">
          <button
            type="submit"
            class="flex-1 py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition text-center shadow-xs cursor-pointer"
          >
            Terapkan
          </button>
          @if (request()->hasAny(['monitor_keyword', 'monitor_category_id', 'monitor_status']))
            <a
              href="{{ route('inventory.stock-control.index', ['tab' => 'monitoring']) }}"
              class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition text-center cursor-pointer"
              title="Reset Filter"
            >
              Reset
            </a>
          @endif
        </div>
      </form>
    </div>

    <!-- Tabel Monitoring Stok -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs sm:text-sm text-slate-600">
          <thead class="bg-slate-50/80 text-slate-800 font-bold border-b border-slate-200 text-xs uppercase tracking-wider">
            <tr>
              <th class="py-3 px-3.5 sm:px-4 w-12 text-center">No</th>
              <th class="py-3 px-3.5 sm:px-4">Kode &amp; Varian ATK</th>
              <th class="py-3 px-3.5 sm:px-4">Kategori &amp; Lokasi</th>
              <th class="py-3 px-3.5 sm:px-4 text-center">Sisa Stok Fisik</th>
              <th class="py-3 px-3.5 sm:px-4 text-center">Ambang Min / Target</th>
              <th class="py-3 px-3.5 sm:px-4">Level Persediaan</th>
              <th class="py-3 px-3.5 sm:px-4 text-center">Status</th>
              <th class="py-3 px-3.5 sm:px-4 text-center">Aksi Cepat</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse ($monitoredItems as $idx => $item)
              <tr class="hover:bg-slate-50/80 transition">
                <td class="py-3 px-3.5 sm:px-4 text-center font-mono text-slate-400 text-xs">
                  {{ $monitoredItems->firstItem() + $idx }}
                </td>
                <td class="py-3 px-3.5 sm:px-4">
                  <div class="flex flex-col">
                    <span class="font-mono text-[11px] font-bold text-blue-700">{{ $item->code }}</span>
                    <span class="font-bold text-slate-900 leading-snug">{{ $item->name }}</span>
                    @if ($item->barcode)
                      <span class="text-[10px] text-slate-400 font-mono flex items-center gap-1 mt-0.5">
                        <svg class="w-3 h-3 text-slate-400 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        {{ $item->barcode }}
                      </span>
                    @endif
                  </div>
                </td>
                <td class="py-3 px-3.5 sm:px-4">
                  <span class="inline-block px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-semibold mb-0.5">
                    {{ $item->category->name ?? 'Umum' }}
                  </span>
                  <div class="text-[11px] text-slate-500 font-medium">
                    Rak/Bin: <span class="font-mono text-slate-700 font-bold">{{ $item->storage_location ?: '-' }}</span>
                  </div>
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-center">
                  <span class="font-extrabold text-slate-900 text-sm block">
                    {{ number_format($item->current_stock) }} {{ $item->effective_small_unit }}
                  </span>
                  @if ($item->has_multi_unit)
                    <span class="text-[10px] text-blue-600 font-medium block">
                      {{ $item->formatted_stock }}
                    </span>
                  @endif
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-center">
                  <span class="text-xs text-slate-600 font-bold block">
                    Min: {{ $item->minimum_stock }} {{ $item->effective_small_unit }}
                  </span>
                  <span class="text-[10.5px] text-slate-400 block">
                    Target: {{ $item->target_stock ?: ($item->minimum_stock * 2) }} {{ $item->effective_small_unit }}
                  </span>
                </td>
                <td class="py-3 px-3.5 sm:px-4 min-w-32">
                  <div class="space-y-1">
                    <div class="flex items-center justify-between text-[10px] font-bold">
                      <span class="text-slate-500">Kapasitas</span>
                      <span class="{{ $item->stock_status === 'out_of_stock' ? 'text-rose-600' : ($item->stock_status === 'low_stock' ? 'text-amber-600' : 'text-emerald-600') }}">
                        {{ $item->stock_percentage }}%
                      </span>
                    </div>
                    <div class="w-full h-1.5 rounded-full bg-slate-100 overflow-hidden">
                      <div
                        class="h-full rounded-full transition-all duration-500 {{ $item->stock_status === 'out_of_stock' ? 'bg-rose-500' : ($item->stock_status === 'low_stock' ? 'bg-amber-500' : 'bg-emerald-500') }}"
                        style="width: {{ min(100, max(5, $item->stock_percentage)) }}%"
                      ></div>
                    </div>
                  </div>
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-center">
                  @if ($item->stock_status === 'out_of_stock')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10.5px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                      <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                      Stok Habis
                    </span>
                  @elseif ($item->stock_status === 'low_stock')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10.5px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                      <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                      Menipis
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10.5px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                      Aman
                    </span>
                  @endif
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <button
                      type="button"
                      onclick="openSingleItemAdjustment({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->current_stock }}, '{{ $item->effective_small_unit }}', '{{ $item->unit }}', {{ $item->conversion_rate ?: 1 }})"
                      class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-bold transition flex items-center gap-1 cursor-pointer border border-amber-200/60"
                      title="Koreksi / Sesuaikan Stok Barang Ini"
                    >
                      <svg class="w-3.5 h-3.5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                      <span>Koreksi</span>
                    </button>
                    <a
                      href="{{ route('inventory.stock-control.index', ['tab' => 'ledger', 'ledger_item_id' => $item->id]) }}"
                      class="p-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer"
                      title="Lihat Buku Kartu Stok Barang Ini"
                    >
                      <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                      </svg>
                    </a>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="py-12 text-center text-slate-400">
                  <div class="flex flex-col items-center justify-center gap-2">
                    <svg class="w-10 h-10 text-slate-300 stroke-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="font-bold text-slate-600 text-sm">Tidak ada data persediaan ATK</p>
                    <p class="text-xs text-slate-400">Ubah kata kunci pencarian atau reset filter kondisi stok.</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination Monitoring -->
      @if ($monitoredItems->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
          {{ $monitoredItems->links() }}
        </div>
      @endif
    </div>
  </div>

  <!-- ============================================================== -->
  <!-- TAB PANE 2: RIWAYAT KOREKSI STOK (STOCK ADJUSTMENT)            -->
  <!-- ============================================================== -->
  <div id="pane-adjustment" class="control-pane space-y-4 hidden">
    <!-- Header & Filter Adjustment -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs space-y-3">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
        <div>
          <h3 class="font-black text-slate-900 text-base">Riwayat Penyesuaian &amp; Koreksi Stok (Stock Adjustment)</h3>
          <p class="text-xs text-slate-500 mt-0.5">Seluruh koreksi barang rusak, selisih hitung, temuan fisik, atau penyesuaian audit resmi.</p>
        </div>
        <button
          type="button"
          onclick="openAdjustmentModal()"
          class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-xs transition cursor-pointer self-start sm:self-auto"
        >
          <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
          </svg>
          <span>Buat Penyesuaian Baru</span>
        </button>
      </div>

      <form method="GET" action="{{ route('inventory.stock-control.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
        <input type="hidden" name="tab" value="adjustment" />

        <div class="lg:col-span-5 relative">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
            <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <input
            type="text"
            name="adj_keyword"
            value="{{ request('adj_keyword') }}"
            placeholder="Cari No. Bukti ADJ atau Alasan..."
            class="w-full pl-10 pr-4 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden font-medium"
          />
        </div>

        <div class="lg:col-span-2">
          <input
            type="date"
            name="adj_date_from"
            value="{{ request('adj_date_from') }}"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden font-medium text-slate-700"
            title="Dari Tanggal"
          />
        </div>

        <div class="lg:col-span-2">
          <input
            type="date"
            name="adj_date_to"
            value="{{ request('adj_date_to') }}"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden font-medium text-slate-700"
            title="Sampai Tanggal"
          />
        </div>

        <div class="lg:col-span-3 flex items-center gap-2">
          <button
            type="submit"
            class="flex-1 py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition text-center shadow-xs cursor-pointer"
          >
            Filter
          </button>
          @if (request()->hasAny(['adj_keyword', 'adj_date_from', 'adj_date_to']))
            <a
              href="{{ route('inventory.stock-control.index', ['tab' => 'adjustment']) }}"
              class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition text-center cursor-pointer"
            >
              Reset
            </a>
          @endif
        </div>
      </form>
    </div>

    <!-- Tabel Riwayat Adjustment -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs sm:text-sm text-slate-600">
          <thead class="bg-slate-50/80 text-slate-800 font-bold border-b border-slate-200 text-xs uppercase tracking-wider">
            <tr>
              <th class="py-3 px-3.5 sm:px-4 w-12 text-center">No</th>
              <th class="py-3 px-3.5 sm:px-4">No. Bukti / ADJ</th>
              <th class="py-3 px-3.5 sm:px-4">Tanggal</th>
              <th class="py-3 px-3.5 sm:px-4">Alasan Penyesuaian</th>
              <th class="py-3 px-3.5 sm:px-4 text-center">Jumlah Item</th>
              <th class="py-3 px-3.5 sm:px-4">Petugas Pencatat</th>
              <th class="py-3 px-3.5 sm:px-4">Catatan</th>
              <th class="py-3 px-3.5 sm:px-4 text-center">Rincian</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse ($adjustments as $idx => $adj)
              <tr class="hover:bg-slate-50/80 transition">
                <td class="py-3 px-3.5 sm:px-4 text-center font-mono text-slate-400 text-xs">
                  {{ $adjustments->firstItem() + $idx }}
                </td>
                <td class="py-3 px-3.5 sm:px-4">
                  <span class="font-mono text-xs font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200/60 block w-max">
                    {{ $adj->adjustment_number }}
                  </span>
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-slate-700 font-semibold whitespace-nowrap">
                  {{ $adj->adjustment_date->format('d/m/Y') }}
                </td>
                <td class="py-3 px-3.5 sm:px-4">
                  <span class="font-bold text-slate-900 block">{{ $adj->reason }}</span>
                  <span class="text-[11px] text-slate-400 font-mono">{{ $adj->type }}</span>
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-center">
                  <span class="inline-block px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 font-extrabold text-xs">
                    {{ $adj->total_items }} Varian
                  </span>
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-slate-700 font-medium">
                  {{ $adj->user->name ?? 'Petugas ATK' }}
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-xs text-slate-500 max-w-48 truncate" title="{{ $adj->notes }}">
                  {{ $adj->notes ?: '-' }}
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-center">
                  <button
                    type="button"
                    onclick="viewAdjustmentDetail({{ $adj->id }})"
                    class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold transition cursor-pointer"
                  >
                    Lihat
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="py-12 text-center text-slate-400">
                  <p class="font-bold text-slate-600 text-sm">Belum ada riwayat penyesuaian stok</p>
                  <p class="text-xs text-slate-400 mt-1">Gunakan tombol "Buat Penyesuaian Baru" untuk mencatat koreksi fisik.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($adjustments->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
          {{ $adjustments->links() }}
        </div>
      @endif
    </div>
  </div>

  <!-- ============================================================== -->
  <!-- TAB PANE 3: STOCK OPNAME FISIK & REKONSILIASI                  -->
  <!-- ============================================================== -->
  <div id="pane-opname" class="control-pane space-y-4 hidden">
    <!-- Header & Action Sesi Opname -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs space-y-3">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
        <div>
          <h3 class="font-black text-slate-900 text-base">Pemeriksaan Fisik Persediaan Berkala (Stock Opname)</h3>
          <p class="text-xs text-slate-500 mt-0.5">Sesi pencocokan fisik riil di gudang dengan catatan sistem untuk menghasilkan berita acara rekonsiliasi.</p>
        </div>
        <button
          type="button"
          onclick="openOpnameModal()"
          class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition cursor-pointer self-start sm:self-auto"
        >
          <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
          </svg>
          <span>Mulai Sesi Opname Baru</span>
        </button>
      </div>

      <!-- Quick Search Opname -->
      <form method="GET" action="{{ route('inventory.stock-control.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
        <input type="hidden" name="tab" value="opname" />

        <div class="lg:col-span-8 relative">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
            <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <input
            type="text"
            name="opn_keyword"
            value="{{ request('opn_keyword') }}"
            placeholder="Cari No. Sesi OPN atau Tim Pemeriksa..."
            class="w-full pl-10 pr-4 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden font-medium"
          />
        </div>

        <div class="lg:col-span-4 flex items-center gap-2">
          <button
            type="submit"
            class="flex-1 py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition text-center shadow-xs cursor-pointer"
          >
            Cari Sesi
          </button>
          @if (request()->has('opn_keyword'))
            <a
              href="{{ route('inventory.stock-control.index', ['tab' => 'opname']) }}"
              class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition text-center cursor-pointer"
            >
              Reset
            </a>
          @endif
        </div>
      </form>
    </div>

    <!-- Tabel Riwayat Sesi Opname -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs sm:text-sm text-slate-600">
          <thead class="bg-slate-50/80 text-slate-800 font-bold border-b border-slate-200 text-xs uppercase tracking-wider">
            <tr>
              <th class="py-3 px-3.5 sm:px-4 w-12 text-center">No</th>
              <th class="py-3 px-3.5 sm:px-4">No. Sesi / OPN</th>
              <th class="py-3 px-3.5 sm:px-4">Tanggal Opname</th>
              <th class="py-3 px-3.5 sm:px-4">Tim Pemeriksa Fisik</th>
              <th class="py-3 px-3.5 sm:px-4 text-center">Total Diperiksa</th>
              <th class="py-3 px-3.5 sm:px-4 text-center">Hasil Cocok</th>
              <th class="py-3 px-3.5 sm:px-4 text-center">Hasil Selisih</th>
              <th class="py-3 px-3.5 sm:px-4 text-center">Status</th>
              <th class="py-3 px-3.5 sm:px-4 text-center">Berita Acara</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse ($opnames as $idx => $opn)
              <tr class="hover:bg-slate-50/80 transition">
                <td class="py-3 px-3.5 sm:px-4 text-center font-mono text-slate-400 text-xs">
                  {{ $opnames->firstItem() + $idx }}
                </td>
                <td class="py-3 px-3.5 sm:px-4">
                  <span class="font-mono text-xs font-bold text-blue-800 bg-blue-50 px-2 py-0.5 rounded border border-blue-200/60 block w-max">
                    {{ $opn->opname_number }}
                  </span>
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-slate-700 font-semibold whitespace-nowrap">
                  {{ $opn->opname_date->format('d/m/Y') }}
                </td>
                <td class="py-3 px-3.5 sm:px-4 font-bold text-slate-900">
                  {{ $opn->conducted_by ?: 'Tim Petugas ATK' }}
                  <span class="block text-[11px] font-normal text-slate-400">Dicatat oleh: {{ $opn->user->name ?? '-' }}</span>
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-center font-bold text-slate-800">
                  {{ $opn->total_items }} Varian
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-center">
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    {{ $opn->total_matched }} Item
                  </span>
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-center">
                  @if ($opn->total_mismatched > 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-rose-50 text-rose-700 border border-rose-200/70">
                      <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                      {{ $opn->total_mismatched }} Selisih
                    </span>
                  @else
                    <span class="text-xs text-slate-400 font-semibold">0 Selisih (Nihil)</span>
                  @endif
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-center">
                  <span class="inline-block px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-700">
                    {{ $opn->status }}
                  </span>
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-center">
                  <button
                    type="button"
                    onclick="viewOpnameDetail({{ $opn->id }})"
                    class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold transition cursor-pointer"
                  >
                    Rincian
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="py-12 text-center text-slate-400">
                  <p class="font-bold text-slate-600 text-sm">Belum ada sesi stock opname yang tercatat</p>
                  <p class="text-xs text-slate-400 mt-1">Lakukan pemeriksaan fisik berkala dengan menekan tombol "Mulai Sesi Opname Baru".</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($opnames->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
          {{ $opnames->links() }}
        </div>
      @endif
    </div>
  </div>

  <!-- ============================================================== -->
  <!-- TAB PANE 4: BUKU KARTU STOK (STOCK LEDGER RESMI)               -->
  <!-- ============================================================== -->
  <div id="pane-ledger" class="control-pane space-y-4 hidden">
    <!-- Filter Kartu Stok -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs space-y-3">
      <div class="pb-2 border-b border-slate-100">
        <h3 class="font-black text-slate-900 text-base">Buku Besar Kartu Persediaan ATK (Stock Ledger Audit)</h3>
        <p class="text-xs text-slate-500 mt-0.5">Histori lengkap dan tak terbantahkan (*source of truth*) untuk setiap mutasi masuk, keluar, koreksi, dan opname.</p>
      </div>

      <form method="GET" action="{{ route('inventory.stock-control.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
        <input type="hidden" name="tab" value="ledger" />

        <!-- Filter Item -->
        <div class="lg:col-span-4">
          <select
            name="ledger_item_id"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden font-medium text-slate-700"
          >
            <option value="">Semua Barang ATK</option>
            @foreach ($allItems as $itm)
              <option value="{{ $itm->id }}" {{ request('ledger_item_id') == $itm->id ? 'selected' : '' }}>
                [{{ $itm->code }}] {{ $itm->name }} (Sisa: {{ $itm->current_stock }})
              </option>
            @endforeach
          </select>
        </div>

        <!-- Filter Jenis Mutasi -->
        <div class="lg:col-span-2">
          <select
            name="ledger_type"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden font-medium text-slate-700"
          >
            <option value="">Semua Mutasi</option>
            <option value="IN" {{ request('ledger_type') == 'IN' ? 'selected' : '' }}>IN (Masuk/Pengadaan)</option>
            <option value="OUT" {{ request('ledger_type') == 'OUT' ? 'selected' : '' }}>OUT (Distribusi Kasir)</option>
            <option value="ADJUSTMENT" {{ request('ledger_type') == 'ADJUSTMENT' ? 'selected' : '' }}>ADJUSTMENT (Koreksi)</option>
            <option value="OPNAME" {{ request('ledger_type') == 'OPNAME' ? 'selected' : '' }}>OPNAME (Opname Fisik)</option>
          </select>
        </div>

        <!-- Tanggal -->
        <div class="lg:col-span-2">
          <input
            type="date"
            name="ledger_date_from"
            value="{{ request('ledger_date_from') }}"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden font-medium text-slate-700"
            title="Dari Tanggal"
          />
        </div>

        <div class="lg:col-span-2">
          <input
            type="date"
            name="ledger_date_to"
            value="{{ request('ledger_date_to') }}"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden font-medium text-slate-700"
            title="Sampai Tanggal"
          />
        </div>

        <!-- Tombol Filter -->
        <div class="lg:col-span-2 flex items-center gap-2">
          <button
            type="submit"
            class="flex-1 py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition text-center shadow-xs cursor-pointer"
          >
            Filter
          </button>
          @if (request()->hasAny(['ledger_item_id', 'ledger_type', 'ledger_date_from', 'ledger_date_to', 'ledger_keyword']))
            <a
              href="{{ route('inventory.stock-control.index', ['tab' => 'ledger']) }}"
              class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition text-center cursor-pointer"
            >
              Reset
            </a>
          @endif
        </div>
      </form>
    </div>

    <!-- Tabel Buku Kartu Stok -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs sm:text-sm text-slate-600">
          <thead class="bg-slate-50/80 text-slate-800 font-bold border-b border-slate-200 text-xs uppercase tracking-wider">
            <tr>
              <th class="py-3 px-3.5 sm:px-4 w-12 text-center">No</th>
              <th class="py-3 px-3.5 sm:px-4">Waktu / Tanggal</th>
              <th class="py-3 px-3.5 sm:px-4">No. Dokumen / Bukti</th>
              <th class="py-3 px-3.5 sm:px-4">Barang ATK</th>
              <th class="py-3 px-3.5 sm:px-4 text-center">Jenis Mutasi</th>
              <th class="py-3 px-3.5 sm:px-4 text-right">Saldo Awal</th>
              <th class="py-3 px-3.5 sm:px-4 text-center">Perubahan Fisik</th>
              <th class="py-3 px-3.5 sm:px-4 text-right">Saldo Akhir</th>
              <th class="py-3 px-3.5 sm:px-4">Keterangan Mutasi</th>
              <th class="py-3 px-3.5 sm:px-4">Petugas</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse ($ledgers as $idx => $leg)
              <tr class="hover:bg-slate-50/80 transition">
                <td class="py-3 px-3.5 sm:px-4 text-center font-mono text-slate-400 text-xs">
                  {{ $ledgers->firstItem() + $idx }}
                </td>
                <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap text-slate-700">
                  <span class="font-bold block">{{ $leg->created_at->format('d/m/Y') }}</span>
                  <span class="text-[10.5px] font-mono text-slate-400">{{ $leg->created_at->format('H:i') }} WIB</span>
                </td>
                <td class="py-3 px-3.5 sm:px-4 whitespace-nowrap">
                  <span class="font-mono text-xs font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                    {{ $leg->reference_number ?: '-' }}
                  </span>
                </td>
                <td class="py-3 px-3.5 sm:px-4">
                  <div class="flex flex-col">
                    <span class="font-mono text-[10px] text-blue-700 font-bold">{{ $leg->item->code ?? '-' }}</span>
                    <span class="font-bold text-slate-900 leading-snug">{{ $leg->item->name ?? '-' }}</span>
                  </div>
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-center whitespace-nowrap">
                  @if ($leg->transaction_type === 'IN')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                      MASUK (IN)
                    </span>
                  @elseif ($leg->transaction_type === 'OUT')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-black bg-blue-50 text-blue-700 border border-blue-200">
                      KELUAR (OUT)
                    </span>
                  @elseif ($leg->transaction_type === 'ADJUSTMENT')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-black bg-amber-50 text-amber-800 border border-amber-200">
                      KOREKSI (ADJ)
                    </span>
                  @elseif ($leg->transaction_type === 'OPNAME')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200">
                      OPNAME (OPN)
                    </span>
                  @else
                    <span class="inline-block px-2 py-0.5 rounded text-[10.5px] font-bold bg-slate-100 text-slate-700">
                      {{ $leg->transaction_type }}
                    </span>
                  @endif
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-right font-mono text-slate-600 font-bold">
                  {{ number_format($leg->balance_before) }}
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-center">
                  @php
                    $diffVal = $leg->balance_after - $leg->balance_before;
                  @endphp
                  @if ($diffVal > 0)
                    <span class="font-mono text-xs font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">
                      +{{ number_format($diffVal) }} {{ $leg->item->effective_small_unit ?? '' }}
                    </span>
                  @elseif ($diffVal < 0)
                    <span class="font-mono text-xs font-black text-rose-700 bg-rose-50 px-2 py-0.5 rounded">
                      {{ number_format($diffVal) }} {{ $leg->item->effective_small_unit ?? '' }}
                    </span>
                  @else
                    <span class="font-mono text-xs font-bold text-slate-400">
                      0 {{ $leg->item->effective_small_unit ?? '' }}
                    </span>
                  @endif
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-right font-mono text-slate-900 font-black">
                  {{ number_format($leg->balance_after) }}
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-xs text-slate-600 max-w-56 truncate" title="{{ $leg->description }}">
                  {{ $leg->description ?: '-' }}
                </td>
                <td class="py-3 px-3.5 sm:px-4 text-xs text-slate-700 font-medium whitespace-nowrap">
                  {{ $leg->user->name ?? '-' }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="py-12 text-center text-slate-400">
                  <p class="font-bold text-slate-600 text-sm">Belum ada mutasi kartu stok tercatat</p>
                  <p class="text-xs text-slate-400 mt-1">Setiap transaksi pengeluaran POS kasir, penyesuaian, atau opname otomatis dibukukan di sini.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($ledgers->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
          {{ $ledgers->links() }}
        </div>
      @endif
    </div>
  </div>

</main>

<!-- ============================================================== -->
<!-- MODAL 1: FORMULIR PENYESUAIAN STOK (STOCK ADJUSTMENT)           -->
<!-- ============================================================== -->
<div id="modal-adjustment" class="fixed inset-y-0 inset-x-0 z-50 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 hidden">
  <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden anim-scale-up">
    <!-- Header Modal -->
    <div class="px-5 py-4 bg-linear-to-r from-[#0f2e54] to-[#0b2341] text-white flex items-center justify-between shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-amber-400/20 text-amber-400 flex items-center justify-center font-bold">
          <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
        </div>
        <div>
          <h4 class="font-black text-sm sm:text-base leading-tight">Penyesuaian &amp; Koreksi Stok Fisik</h4>
          <span class="text-[11px] text-blue-200/80 font-mono" id="adj-form-number-preview">No. Bukti: {{ $nextAdjustmentNumber }}</span>
        </div>
      </div>
      <button
        type="button"
        onclick="closeAdjustmentModal()"
        class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer text-lg font-bold"
      >
        &times;
      </button>
    </div>

    <!-- Body Modal Form -->
    <form id="form-stock-adjustment" onsubmit="handleAdjustmentSubmit(event)" class="overflow-y-auto p-5 space-y-4 flex-1">
      @csrf

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <!-- Tanggal Penyesuaian -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Koreksi *</label>
          <input
            type="date"
            name="adjustment_date"
            value="{{ date('Y-m-d') }}"
            required
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 font-semibold"
          />
        </div>

        <!-- Alasan Penyesuaian -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alasan Penyesuaian *</label>
          <select
            name="reason"
            required
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 font-semibold"
          >
            <option value="">Pilih Alasan Valid...</option>
            <option value="Barang Rusak / Kadaluarsa">Barang Rusak / Cacat / Kadaluarsa</option>
            <option value="Selisih Hitung / Hilang">Selisih Hitung Fisik / Hilang</option>
            <option value="Temuan Fisik Berlebih">Temuan Fisik Berlebih / Retur</option>
            <option value="Koreksi Input Sistem">Koreksi Kesalahan Input Sistem</option>
            <option value="Hibah / Penyesuaian Audit">Hibah / Penyesuaian Temuan Audit</option>
          </select>
        </div>
      </div>

      <!-- Item Selection Section -->
      <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-3">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
            <span class="w-1.5 h-3.5 rounded-full bg-blue-600"></span>
            Barang ATK yang Disesuaikan
          </span>
        </div>

        <!-- Selector Item -->
        <div>
          <label class="block text-[11px] font-bold text-slate-600 mb-1">Pilih Barang ATK</label>
          <select
            id="adj-item-select"
            onchange="handleAdjustmentItemChange(this)"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 bg-white focus:outline-hidden font-semibold text-slate-800"
          >
            <option value="">Pilih dari katalog persediaan...</option>
            @foreach ($allItems as $it)
              <option
                value="{{ $it->id }}"
                data-code="{{ $it->code }}"
                data-name="{{ $it->name }}"
                data-stock="{{ $it->current_stock }}"
                data-small-unit="{{ $it->effective_small_unit }}"
                data-primary-unit="{{ $it->unit ?: 'Pcs' }}"
                data-conversion="{{ $it->conversion_rate ?: 1 }}"
                data-has-multi="{{ $it->has_multi_unit ? '1' : '0' }}"
              >
                [{{ $it->code }}] {{ $it->name }} (Stok Sistem: {{ $it->current_stock }} {{ $it->effective_small_unit }})
              </option>
            @endforeach
          </select>
        </div>

        <!-- Detail Perhitungan Item Terpilih -->
        <div id="adj-item-calc-box" class="space-y-3 pt-2 border-t border-slate-200/80 hidden">
          <input type="hidden" name="items[0][item_id]" id="adj-input-item-id" value="" />
          <input type="hidden" name="items[0][mode]" value="actual" />

          <!-- Info Baris Item -->
          <div class="flex items-center justify-between bg-white p-2.5 rounded-xl border border-slate-200 text-xs">
            <div>
              <span class="font-mono text-[10px] text-blue-700 font-bold block" id="adj-display-code">-</span>
              <span class="font-bold text-slate-900 block" id="adj-display-name">-</span>
            </div>
            <div class="text-right">
              <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider block">Stok Sistem Saat Ini</span>
              <span class="font-mono font-black text-slate-800 text-sm" id="adj-display-system-stock">0 Pcs</span>
            </div>
          </div>

          <!-- Unit Selector (jika multi-satuan) -->
          <div id="adj-unit-switcher-container" class="hidden">
            <label class="block text-[11px] font-bold text-slate-600 mb-1">Satuan Input</label>
            <div class="inline-flex items-center p-0.5 rounded-lg bg-slate-200/70 text-xs font-bold" id="adj-unit-buttons">
              <!-- Rendered via JS -->
            </div>
            <input type="hidden" name="items[0][unit]" id="adj-input-unit" value="Pcs" />
          </div>

          <!-- Input Angka Fisik Nyata -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-center">
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                Stok Fisik Nyata Sebenarnya *
              </label>
              <div class="relative">
                <input
                  type="number"
                  min="0"
                  id="adj-input-actual-stock"
                  name="items[0][actual_stock]"
                  oninput="calculateAdjustmentDiff()"
                  required
                  class="w-full px-3 py-2.5 text-sm sm:text-base font-mono font-black rounded-xl border border-slate-300 bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 text-slate-900"
                  placeholder="0"
                />
                <span class="absolute inset-y-0 right-0 pr-3 flex items-center font-bold text-xs text-slate-500" id="adj-label-unit-suffix">
                  Pcs
                </span>
              </div>
            </div>

            <!-- Preview Hasil Selisih (+/-) -->
            <div class="bg-white p-3 rounded-xl border border-slate-200 text-xs flex flex-col justify-center">
              <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Perubahan Mutasi Kartu Stok</span>
              <div class="flex items-center gap-2 mt-1">
                <span class="text-sm sm:text-base font-black font-mono" id="adj-preview-diff">0 Pcs</span>
                <span class="text-[10.5px] px-2 py-0.5 rounded-md font-bold" id="adj-preview-badge">Tidak Berubah</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Catatan Tambahan -->
      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan Tambahan (Opsional)</label>
        <textarea
          name="notes"
          rows="2"
          placeholder="Berikan rincian nomor berita acara atau keterangan kondisi barang..."
          class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:outline-hidden font-medium"
        ></textarea>
      </div>

      <!-- Footer Tombol -->
      <div class="pt-2 border-t border-slate-200 flex items-center justify-end gap-2">
        <button
          type="button"
          onclick="closeAdjustmentModal()"
          class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer"
        >
          Batal
        </button>
        <button
          type="submit"
          id="btn-submit-adj"
          disabled
          class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold text-xs sm:text-sm shadow-md shadow-blue-600/20 transition cursor-pointer flex items-center gap-2"
        >
          <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          <span>Simpan &amp; Bukukan Penyesuaian</span>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ============================================================== -->
<!-- MODAL 2: SESI STOCK OPNAME FISIK & REKONSILIASI                -->
<!-- ============================================================== -->
<div id="modal-opname" class="fixed inset-y-0 inset-x-0 z-50 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 hidden">
  <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-4xl max-h-[92vh] flex flex-col overflow-hidden anim-scale-up">
    <!-- Header Modal -->
    <div class="px-5 py-4 bg-linear-to-r from-[#0f2e54] to-[#0b2341] text-white flex items-center justify-between shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-blue-400/20 text-blue-300 flex items-center justify-center font-bold">
          <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
          </svg>
        </div>
        <div>
          <h4 class="font-black text-sm sm:text-base leading-tight">Sesi Pemeriksaan Fisik &amp; Rekonsiliasi (Stock Opname)</h4>
          <span class="text-[11px] text-blue-200/80 font-mono">No. Sesi: {{ $nextOpnameNumber }}</span>
        </div>
      </div>
      <button
        type="button"
        onclick="closeOpnameModal()"
        class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer text-lg font-bold"
      >
        &times;
      </button>
    </div>

    <!-- Body Form Opname -->
    <form id="form-stock-opname" onsubmit="handleOpnameSubmit(event)" class="overflow-y-auto p-5 space-y-4 flex-1">
      @csrf

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Pemeriksaan *</label>
          <input
            type="date"
            name="opname_date"
            value="{{ date('Y-m-d') }}"
            required
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:outline-hidden font-semibold"
          />
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tim / Petugas Pemeriksa *</label>
          <input
            type="text"
            name="conducted_by"
            required
            placeholder="Contoh: Tim Pengurus Barang &amp; Auditor..."
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:outline-hidden font-semibold"
          />
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan Sesi</label>
          <input
            type="text"
            name="notes"
            placeholder="Opname Bulanan / Akhir Triwulan..."
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:outline-hidden font-medium"
          />
        </div>
      </div>

      <!-- Quick Opname Filter & Stats Bar -->
      <div class="flex items-center justify-between gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs flex-wrap">
        <div class="flex items-center gap-2">
          <input
            type="text"
            id="opname-search-box"
            oninput="filterOpnameModalRows(this.value)"
            placeholder="Cari item dalam daftar opname..."
            class="px-3 py-1.5 text-xs rounded-lg border border-slate-300 bg-white focus:outline-hidden font-medium w-60"
          />
        </div>
        <div class="flex items-center gap-3 font-bold">
          <span class="text-slate-600">Total: <strong id="opn-stat-total" class="font-black text-slate-900">{{ count($allItems) }}</strong> Item</span>
          <span class="text-emerald-700">Cocok: <strong id="opn-stat-matched" class="font-black">0</strong></span>
          <span class="text-rose-700">Selisih: <strong id="opn-stat-diff" class="font-black">0</strong></span>
        </div>
      </div>

      <!-- Tabel Input Hasil Fisik Opname -->
      <div class="border border-slate-200 rounded-2xl overflow-hidden max-h-96 overflow-y-auto">
        <table class="w-full text-left border-collapse text-xs text-slate-600">
          <thead class="bg-slate-100 text-slate-800 font-bold sticky top-0 z-10 border-b border-slate-200">
            <tr>
              <th class="py-2.5 px-3 w-10 text-center">No</th>
              <th class="py-2.5 px-3">Kode &amp; Nama Barang ATK</th>
              <th class="py-2.5 px-3 text-center">Stok Sistem</th>
              <th class="py-2.5 px-3 text-center w-36">Hitungan Fisik Riil</th>
              <th class="py-2.5 px-3 text-center">Selisih</th>
              <th class="py-2.5 px-3">Catatan Temuan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium" id="opname-tbody">
            @foreach ($allItems as $i => $it)
              <tr class="opn-row hover:bg-slate-50 transition" data-name="{{ strtolower($it->name) }}" data-code="{{ strtolower($it->code) }}">
                <td class="py-2.5 px-3 text-center font-mono text-slate-400">{{ $i + 1 }}</td>
                <td class="py-2.5 px-3">
                  <input type="hidden" name="items[{{ $i }}][item_id]" value="{{ $it->id }}" />
                  <span class="font-mono text-[10px] text-blue-700 font-bold block">{{ $it->code }}</span>
                  <span class="font-bold text-slate-900">{{ $it->name }}</span>
                </td>
                <td class="py-2.5 px-3 text-center font-mono font-bold text-slate-700">
                  <span id="opn-sys-{{ $it->id }}">{{ $it->current_stock }}</span> {{ $it->effective_small_unit }}
                </td>
                <td class="py-2.5 px-3 text-center">
                  <input
                    type="number"
                    min="0"
                    name="items[{{ $i }}][physical_stock]"
                    value="{{ $it->current_stock }}"
                    oninput="recalcOpnameRow({{ $it->id }}, this.value, {{ $it->current_stock }})"
                    class="w-24 px-2 py-1 text-center font-mono font-black text-xs rounded-lg border border-slate-300 bg-white focus:outline-hidden focus:ring-1 focus:ring-blue-500"
                  />
                  <span class="text-[10px] text-slate-400 block mt-0.5">{{ $it->effective_small_unit }}</span>
                </td>
                <td class="py-2.5 px-3 text-center">
                  <span
                    id="opn-badge-{{ $it->id }}"
                    class="opn-row-badge inline-block px-2 py-0.5 rounded font-mono font-bold text-[10.5px] bg-emerald-50 text-emerald-700 border border-emerald-200"
                  >
                    Cocok (0)
                  </span>
                </td>
                <td class="py-2.5 px-3">
                  <input
                    type="text"
                    name="items[{{ $i }}][notes]"
                    placeholder="Keterangan..."
                    class="w-full px-2 py-1 text-[11px] rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-hidden"
                  />
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Footer Modal Opname -->
      <div class="pt-2 border-t border-slate-200 flex items-center justify-between">
        <span class="text-[11px] text-slate-500 italic">* Rekonsiliasi akan memperbarui stok sistem secara atomik dan mencatat ke Stock Ledger.</span>
        <div class="flex items-center gap-2">
          <button
            type="button"
            onclick="closeOpnameModal()"
            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer"
          >
            Batal
          </button>
          <button
            type="submit"
            id="btn-submit-opn"
            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs sm:text-sm shadow-md shadow-blue-600/20 transition cursor-pointer flex items-center gap-2"
          >
            <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <span>Rekonsiliasi &amp; Simpan Opname</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- ============================================================== -->
<!-- MODAL 3: DETAIL PENYESUAIAN / OPNAME                           -->
<!-- ============================================================== -->
<div id="modal-detail-viewer" class="fixed inset-y-0 inset-x-0 z-50 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 hidden">
  <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-2xl max-h-[85vh] flex flex-col overflow-hidden anim-scale-up">
    <div class="px-5 py-4 bg-linear-to-r from-[#0f2e54] to-[#0b2341] text-white flex items-center justify-between shrink-0">
      <div>
        <h4 class="font-black text-sm sm:text-base leading-tight" id="detail-modal-title">Rincian Dokumen</h4>
        <span class="text-[11px] text-blue-200/80 font-mono" id="detail-modal-subtitle">-</span>
      </div>
      <button
        type="button"
        onclick="closeDetailModal()"
        class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer text-lg font-bold"
      >
        &times;
      </button>
    </div>

    <div class="overflow-y-auto p-5 space-y-4 flex-1">
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs bg-slate-50 p-3 rounded-xl border border-slate-200">
        <div>
          <span class="text-slate-400 block text-[10px] uppercase font-bold">Tanggal</span>
          <span class="font-bold text-slate-800" id="det-date">-</span>
        </div>
        <div>
          <span class="text-slate-400 block text-[10px] uppercase font-bold">Petugas</span>
          <span class="font-bold text-slate-800" id="det-officer">-</span>
        </div>
        <div>
          <span class="text-slate-400 block text-[10px] uppercase font-bold">Kategori/Status</span>
          <span class="font-bold text-slate-800" id="det-status">-</span>
        </div>
        <div>
          <span class="text-slate-400 block text-[10px] uppercase font-bold">Total Item</span>
          <span class="font-bold text-slate-800" id="det-total">-</span>
        </div>
      </div>

      <div class="border border-slate-200 rounded-xl overflow-hidden">
        <table class="w-full text-left text-xs text-slate-600">
          <thead class="bg-slate-100 text-slate-800 font-bold border-b border-slate-200">
            <tr>
              <th class="py-2 px-3">Kode &amp; Nama Barang</th>
              <th class="py-2 px-3 text-center">Stok Awal</th>
              <th class="py-2 px-3 text-center">Hasil Fisik</th>
              <th class="py-2 px-3 text-center">Selisih</th>
            </tr>
          </thead>
          <tbody id="detail-modal-tbody" class="divide-y divide-slate-100 font-medium">
            <!-- Rendered via JS -->
          </tbody>
        </table>
      </div>
    </div>

    <div class="p-3 border-t border-slate-200 bg-slate-50 flex justify-end">
      <button
        type="button"
        onclick="closeDetailModal()"
        class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold transition cursor-pointer"
      >
        Tutup
      </button>
    </div>
  </div>
</div>

</main>
@endsection

@push('scripts')
<script>
  let activeTab = 'monitoring';
  let selectedAdjItem = null;

  document.addEventListener('DOMContentLoaded', () => {
    // 1. Baca query string tab jika ada
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam && ['monitoring', 'adjustment', 'opname', 'ledger'].includes(tabParam)) {
      switchControlTab(tabParam, false);
    }
  });

  // Switch Tab Handler
  function switchControlTab(tabId, updateUrl = true) {
    activeTab = tabId;

    // Toggle Tombol Tab
    document.querySelectorAll('.control-tab-btn').forEach(btn => {
      btn.className = 'control-tab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all whitespace-nowrap text-slate-600 hover:text-slate-900 cursor-pointer';
    });

    const activeBtn = document.getElementById(`tab-btn-${tabId}`);
    if (activeBtn) {
      activeBtn.className = 'control-tab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all whitespace-nowrap bg-white text-blue-700 shadow-xs border border-slate-200/60 cursor-pointer';
    }

    // Toggle Konten Pane
    document.querySelectorAll('.control-pane').forEach(pane => {
      pane.classList.add('hidden');
    });

    const activePane = document.getElementById(`pane-${tabId}`);
    if (activePane) {
      activePane.classList.remove('hidden');
      activePane.classList.remove('anim-fade-in');
      void activePane.offsetWidth;
      activePane.classList.add('anim-fade-in');
    }

    if (updateUrl) {
      const url = new URL(window.location);
      url.searchParams.set('tab', tabId);
      window.history.replaceState({}, '', url);
    }

    if (window.showToast) {
      const tabLabels = {
        monitoring: 'Status & Monitoring Stok',
        adjustment: 'Koreksi Stok (Stock Adjustment)',
        opname: 'Stock Opname Fisik',
        ledger: 'Buku Kartu Stok (Ledger)'
      };
      window.showToast(`Beralih ke tab: ${tabLabels[tabId] || tabId}`, 'info');
    }
  }

  // ==============================================================
  // MODAL ADJUSTMENT LOGIC
  // ==============================================================
  function openAdjustmentModal() {
    const modal = document.getElementById('modal-adjustment');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
  }

  function closeAdjustmentModal() {
    const modal = document.getElementById('modal-adjustment');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
  }

  function openSingleItemAdjustment(itemId, itemName, currentStock, smallUnit, primaryUnit, convRate) {
    openAdjustmentModal();
    const select = document.getElementById('adj-item-select');
    select.value = itemId;
    handleAdjustmentItemChange(select);
  }

  function handleAdjustmentItemChange(selectEl) {
    const opt = selectEl.options[selectEl.selectedIndex];
    const calcBox = document.getElementById('adj-item-calc-box');
    const submitBtn = document.getElementById('btn-submit-adj');

    if (!selectEl.value || !opt) {
      calcBox.classList.add('hidden');
      submitBtn.disabled = true;
      selectedAdjItem = null;
      return;
    }

    selectedAdjItem = {
      id: selectEl.value,
      code: opt.dataset.code,
      name: opt.dataset.name,
      stock: parseInt(opt.dataset.stock, 10),
      smallUnit: opt.dataset.smallUnit || 'Pcs',
      primaryUnit: opt.dataset.primaryUnit || 'Pcs',
      conversionRate: parseInt(opt.dataset.conversion, 10) || 1,
      hasMultiUnit: opt.dataset.hasMulti === '1',
      selectedUnit: opt.dataset.smallUnit || 'Pcs'
    };

    document.getElementById('adj-input-item-id').value = selectedAdjItem.id;
    document.getElementById('adj-display-code').textContent = selectedAdjItem.code;
    document.getElementById('adj-display-name').textContent = selectedAdjItem.name;
    document.getElementById('adj-display-system-stock').textContent = `${selectedAdjItem.stock} ${selectedAdjItem.smallUnit}`;
    document.getElementById('adj-label-unit-suffix').textContent = selectedAdjItem.smallUnit;
    document.getElementById('adj-input-unit').value = selectedAdjItem.smallUnit;

    const actualInput = document.getElementById('adj-input-actual-stock');
    actualInput.value = selectedAdjItem.stock;

    // Unit Switcher jika barang multi-satuan
    const unitSwitcherContainer = document.getElementById('adj-unit-switcher-container');
    const unitButtons = document.getElementById('adj-unit-buttons');

    if (selectedAdjItem.hasMultiUnit) {
      unitSwitcherContainer.classList.remove('hidden');
      unitButtons.innerHTML = `
        <button
          type="button"
          onclick="setAdjUnit('${selectedAdjItem.smallUnit}')"
          id="adj-btn-unit-small"
          class="px-2.5 py-1 rounded-md transition bg-blue-600 text-white shadow-2xs font-black cursor-pointer"
        >
          ${selectedAdjItem.smallUnit} (Eceran)
        </button>
        <button
          type="button"
          onclick="setAdjUnit('${selectedAdjItem.primaryUnit}')"
          id="adj-btn-unit-primary"
          class="px-2.5 py-1 rounded-md transition text-slate-600 hover:text-slate-900 cursor-pointer"
        >
          ${selectedAdjItem.primaryUnit} (Kemasan @ ${selectedAdjItem.conversionRate} ${selectedAdjItem.smallUnit})
        </button>
      `;
    } else {
      unitSwitcherContainer.classList.add('hidden');
    }

    calcBox.classList.remove('hidden');
    submitBtn.disabled = false;
    calculateAdjustmentDiff();
  }

  function setAdjUnit(unit) {
    if (!selectedAdjItem) return;
    selectedAdjItem.selectedUnit = unit;
    document.getElementById('adj-input-unit').value = unit;
    document.getElementById('adj-label-unit-suffix').textContent = unit;

    const btnSmall = document.getElementById('adj-btn-unit-small');
    const btnPrimary = document.getElementById('adj-btn-unit-primary');

    if (unit === selectedAdjItem.smallUnit) {
      btnSmall.className = 'px-2.5 py-1 rounded-md transition bg-blue-600 text-white shadow-2xs font-black cursor-pointer';
      btnPrimary.className = 'px-2.5 py-1 rounded-md transition text-slate-600 hover:text-slate-900 cursor-pointer';
    } else {
      btnPrimary.className = 'px-2.5 py-1 rounded-md transition bg-blue-600 text-white shadow-2xs font-black cursor-pointer';
      btnSmall.className = 'px-2.5 py-1 rounded-md transition text-slate-600 hover:text-slate-900 cursor-pointer';
    }

    calculateAdjustmentDiff();
  }

  function calculateAdjustmentDiff() {
    if (!selectedAdjItem) return;

    const actualInput = document.getElementById('adj-input-actual-stock');
    const val = parseInt(actualInput.value, 10);
    const diffEl = document.getElementById('adj-preview-diff');
    const badgeEl = document.getElementById('adj-preview-badge');
    const submitBtn = document.getElementById('btn-submit-adj');

    if (isNaN(val) || val < 0) {
      diffEl.textContent = 'Tidak Valid';
      diffEl.className = 'text-sm font-black text-rose-600';
      badgeEl.textContent = 'Harus >= 0';
      badgeEl.className = 'text-[10.5px] px-2 py-0.5 rounded-md font-bold bg-rose-50 text-rose-700';
      submitBtn.disabled = true;
      return;
    }

    const rate = (selectedAdjItem.hasMultiUnit && selectedAdjItem.selectedUnit === selectedAdjItem.primaryUnit)
      ? selectedAdjItem.conversionRate
      : 1;

    const physicalActual = val * rate;
    const diff = physicalActual - selectedAdjItem.stock;

    if (diff > 0) {
      diffEl.textContent = `+${diff} ${selectedAdjItem.smallUnit}`;
      diffEl.className = 'text-base font-black font-mono text-emerald-700';
      badgeEl.textContent = 'Bertambah (+)';
      badgeEl.className = 'text-[10.5px] px-2 py-0.5 rounded-md font-bold bg-emerald-50 text-emerald-700 border border-emerald-200';
    } else if (diff < 0) {
      diffEl.textContent = `${diff} ${selectedAdjItem.smallUnit}`;
      diffEl.className = 'text-base font-black font-mono text-rose-700';
      badgeEl.textContent = 'Berkurang (-)';
      badgeEl.className = 'text-[10.5px] px-2 py-0.5 rounded-md font-bold bg-rose-50 text-rose-700 border border-rose-200';
    } else {
      diffEl.textContent = `0 ${selectedAdjItem.smallUnit}`;
      diffEl.className = 'text-base font-black font-mono text-slate-500';
      badgeEl.textContent = 'Sama / Tidak Berubah';
      badgeEl.className = 'text-[10.5px] px-2 py-0.5 rounded-md font-bold bg-slate-100 text-slate-600';
    }

    submitBtn.disabled = false;
  }

  async function handleAdjustmentSubmit(event) {
    event.preventDefault();
    const form = document.getElementById('form-stock-adjustment');
    const btn = document.getElementById('btn-submit-adj');

    btn.disabled = true;
    btn.innerHTML = '<span>Menyimpan &amp; Membukukan...</span>';

    try {
      const formData = new FormData(form);
      const res = await fetch("{{ route('inventory.stock-control.adjustment.store') }}", {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "Accept": "application/json"
        },
        body: formData
      });

      const data = await res.json();

      if (!res.ok) {
        let msg = data.message || 'Gagal menyimpan penyesuaian stok.';
        if (data.errors && data.errors.items) msg = data.errors.items[0];
        if (window.showToast) {
          window.showToast(msg, 'warning');
        } else {
          alert(msg);
        }
        btn.disabled = false;
        btn.innerHTML = '<span>Simpan &amp; Bukukan Penyesuaian</span>';
        return;
      }

      if (window.showToast) {
        window.showToast(data.message || 'Penyesuaian stok berhasil dibukukan.', 'success');
      }

      closeAdjustmentModal();
      setTimeout(() => {
        window.location.href = "{{ route('inventory.stock-control.index', ['tab' => 'adjustment']) }}";
      }, 700);

    } catch (err) {
      if (window.showToast) {
        window.showToast('Terjadi kesalahan jaringan atau server.', 'danger');
      }
      btn.disabled = false;
      btn.innerHTML = '<span>Simpan &amp; Bukukan Penyesuaian</span>';
    }
  }

  // ==============================================================
  // MODAL STOCK OPNAME LOGIC
  // ==============================================================
  function openOpnameModal() {
    const modal = document.getElementById('modal-opname');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
    updateOpnameStats();
  }

  function closeOpnameModal() {
    const modal = document.getElementById('modal-opname');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
  }

  function filterOpnameModalRows(keyword) {
    const query = keyword.toLowerCase().trim();
    document.querySelectorAll('.opn-row').forEach(row => {
      const name = row.dataset.name || '';
      const code = row.dataset.code || '';
      if (query === '' || name.includes(query) || code.includes(query)) {
        row.classList.remove('hidden');
      } else {
        row.classList.add('hidden');
      }
    });
  }

  function recalcOpnameRow(itemId, physicalVal, systemStock) {
    const badge = document.getElementById(`opn-badge-${itemId}`);
    const phys = parseInt(physicalVal, 10);
    const diff = isNaN(phys) ? 0 : (phys - systemStock);

    if (diff === 0) {
      badge.textContent = 'Cocok (0)';
      badge.className = 'opn-row-badge inline-block px-2 py-0.5 rounded font-mono font-bold text-[10.5px] bg-emerald-50 text-emerald-700 border border-emerald-200';
    } else if (diff > 0) {
      badge.textContent = `Lebih (+${diff})`;
      badge.className = 'opn-row-badge inline-block px-2 py-0.5 rounded font-mono font-bold text-[10.5px] bg-blue-50 text-blue-700 border border-blue-200';
    } else {
      badge.textContent = `Kurang (${diff})`;
      badge.className = 'opn-row-badge inline-block px-2 py-0.5 rounded font-mono font-bold text-[10.5px] bg-rose-50 text-rose-700 border border-rose-200';
    }

    updateOpnameStats();
  }

  function updateOpnameStats() {
    const badges = document.querySelectorAll('.opn-row-badge');
    let matched = 0;
    let diff = 0;

    badges.forEach(b => {
      if (b.textContent.includes('Cocok')) {
        matched++;
      } else {
        diff++;
      }
    });

    document.getElementById('opn-stat-matched').textContent = matched;
    document.getElementById('opn-stat-diff').textContent = diff;
  }

  async function handleOpnameSubmit(event) {
    event.preventDefault();
    if (!confirm('Apakah Anda yakin ingin merekonsiliasi seluruh hasil fisik ini? Stok sistem akan diperbarui secara permanen.')) {
      return;
    }

    const form = document.getElementById('form-stock-opname');
    const btn = document.getElementById('btn-submit-opn');

    btn.disabled = true;
    btn.innerHTML = '<span>Memproses Rekonsiliasi...</span>';

    try {
      const formData = new FormData(form);
      const res = await fetch("{{ route('inventory.stock-control.opname.store') }}", {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "Accept": "application/json"
        },
        body: formData
      });

      const data = await res.json();

      if (!res.ok) {
        let msg = data.message || 'Gagal memproses sesi stock opname.';
        if (window.showToast) {
          window.showToast(msg, 'warning');
        } else {
          alert(msg);
        }
        btn.disabled = false;
        btn.innerHTML = '<span>Rekonsiliasi &amp; Simpan Opname</span>';
        return;
      }

      if (window.showToast) {
        window.showToast(data.message || 'Sesi opname berhasil direkonsiliasi.', 'success');
      }

      closeOpnameModal();
      setTimeout(() => {
        window.location.href = "{{ route('inventory.stock-control.index', ['tab' => 'opname']) }}";
      }, 700);

    } catch (err) {
      if (window.showToast) {
        window.showToast('Terjadi kesalahan jaringan.', 'danger');
      }
      btn.disabled = false;
      btn.innerHTML = '<span>Rekonsiliasi &amp; Simpan Opname</span>';
    }
  }

  // ==============================================================
  // DETAIL VIEWER MODAL
  // ==============================================================
  async function viewAdjustmentDetail(id) {
    try {
      const res = await fetch(`/inventory/stock-control/adjustment/${id}`);
      const result = await res.json();
      if (!result.success) return;

      const data = result.data;
      document.getElementById('detail-modal-title').textContent = 'Rincian Penyesuaian Stok';
      document.getElementById('detail-modal-subtitle').textContent = `${data.adjustment_number} • Alasan: ${data.reason}`;
      document.getElementById('det-date').textContent = data.adjustment_date;
      document.getElementById('det-officer').textContent = data.officer_name;
      document.getElementById('det-status').textContent = data.type;
      document.getElementById('det-total').textContent = `${data.total_items} Item`;

      let rows = '';
      data.details.forEach(d => {
        const sign = d.difference > 0 ? `+${d.difference}` : `${d.difference}`;
        rows += `
          <tr class="hover:bg-slate-50">
            <td class="py-2 px-3">
              <span class="font-mono text-[10px] text-blue-700 font-bold block">${d.item_code}</span>
              <span class="font-bold text-slate-800">${d.item_name}</span>
            </td>
            <td class="py-2 px-3 text-center font-mono font-bold text-slate-600">${d.system_stock} ${d.unit}</td>
            <td class="py-2 px-3 text-center font-mono font-bold text-slate-900">${d.actual_stock} ${d.unit}</td>
            <td class="py-2 px-3 text-center font-mono font-black ${d.difference > 0 ? 'text-emerald-700' : (d.difference < 0 ? 'text-rose-700' : 'text-slate-400')}">
              ${sign} ${d.unit}
            </td>
          </tr>
        `;
      });

      document.getElementById('detail-modal-tbody').innerHTML = rows;
      openDetailModal();
    } catch (e) {
      if (window.showToast) window.showToast('Gagal memuat rincian penyesuaian.', 'danger');
    }
  }

  async function viewOpnameDetail(id) {
    try {
      const res = await fetch(`/inventory/stock-control/opname/${id}`);
      const result = await res.json();
      if (!result.success) return;

      const data = result.data;
      document.getElementById('detail-modal-title').textContent = 'Berita Acara Sesi Stock Opname';
      document.getElementById('detail-modal-subtitle').textContent = `${data.opname_number} • Tim: ${data.conducted_by}`;
      document.getElementById('det-date').textContent = data.opname_date;
      document.getElementById('det-officer').textContent = data.officer_name;
      document.getElementById('det-status').textContent = data.status;
      document.getElementById('det-total').textContent = `${data.total_items} Item (${data.total_matched} Cocok, ${data.total_mismatched} Selisih)`;

      let rows = '';
      data.details.forEach(d => {
        const sign = d.difference > 0 ? `+${d.difference}` : `${d.difference}`;
        rows += `
          <tr class="hover:bg-slate-50">
            <td class="py-2 px-3">
              <span class="font-mono text-[10px] text-blue-700 font-bold block">${d.item_code}</span>
              <span class="font-bold text-slate-800">${d.item_name}</span>
            </td>
            <td class="py-2 px-3 text-center font-mono font-bold text-slate-600">${d.system_stock} ${d.small_unit}</td>
            <td class="py-2 px-3 text-center font-mono font-bold text-slate-900">${d.physical_stock} ${d.small_unit}</td>
            <td class="py-2 px-3 text-center font-mono font-black ${d.difference > 0 ? 'text-blue-700' : (d.difference < 0 ? 'text-rose-700' : 'text-emerald-700')}">
              ${d.difference === 0 ? '0 (Cocok)' : sign + ' ' + d.small_unit}
            </td>
          </tr>
        `;
      });

      document.getElementById('detail-modal-tbody').innerHTML = rows;
      openDetailModal();
    } catch (e) {
      if (window.showToast) window.showToast('Gagal memuat rincian opname.', 'danger');
    }
  }

  function openDetailModal() {
    const modal = document.getElementById('modal-detail-viewer');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
  }

  function closeDetailModal() {
    const modal = document.getElementById('modal-detail-viewer');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
  }
</script>
@endpush
