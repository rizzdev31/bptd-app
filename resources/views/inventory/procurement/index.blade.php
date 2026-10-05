@extends('layouts.app')

@section('title', 'Pengadaan & Penerimaan ATK - BPTD Kelas II Jawa Timur')

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
  <!-- 1. HEADER BAR: IDENTITAS PENGADAAN & PENERIMAAN ATK            -->
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
            <span class="text-blue-600 font-bold">Logistik ATK</span>
          </div>
          <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight leading-snug">
            Pengadaan &amp; Penerimaan ATK
          </h1>
        </div>
      </div>

      <!-- Sisi Kanan: Action Buttons -->
      <div class="flex items-center gap-2.5 shrink-0 self-start sm:self-center flex-wrap">
        <button
          type="button"
          onclick="openDirectStockInModal()"
          class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-800 font-bold text-xs transition shadow-2xs cursor-pointer border border-slate-200/80"
          title="Catat barang masuk langsung tanpa PO (Hibah/Sisa Kegiatan)"
        >
          <svg class="w-4 h-4 text-emerald-600 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
          </svg>
          <span>Penerimaan Langsung</span>
        </button>

        <button
          type="button"
          onclick="openSupplierModal()"
          class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-800 font-bold text-xs transition shadow-2xs cursor-pointer border border-slate-200/80"
          title="Tambah data rekanan/penyedia baru"
        >
          <svg class="w-4 h-4 text-blue-600 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
          <span>+ Rekanan</span>
        </button>

        <button
          type="button"
          onclick="openProcurementModal()"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs sm:text-sm transition shadow-xs cursor-pointer"
        >
          <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
          </svg>
          <span>Buat Pengadaan (PO)</span>
        </button>
      </div>

    </div>
  </div>

  <!-- ============================================================== -->
  <!-- 2. METRIC QUICK CARDS (Statistik Pengadaan & Gudang)           -->
  <!-- ============================================================== -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    
    <!-- Card 1: Anggaran Pengadaan Bulan Ini -->
    <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up">
      <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block truncate">Anggaran Bulan Ini</span>
        <span class="text-lg font-black text-slate-900 tracking-tight block truncate">Rp {{ number_format($monthlyBudgetSpent, 0, ',', '.') }}</span>
        <span class="text-[11px] font-semibold text-blue-600 block truncate">Pesanan &amp; Diterima</span>
      </div>
    </div>

    <!-- Card 2: Menunggu Diterima di Gudang -->
    <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up anim-delay-100">
      <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <div class="flex items-center gap-1.5">
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block truncate">Menunggu Diterima</span>
          @if ($pendingReceiptCount > 0)
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
          @endif
        </div>
        <span class="text-lg font-black text-amber-700 tracking-tight block">{{ number_format($pendingReceiptCount) }} PO Aktif</span>
        <span class="text-[11px] font-semibold text-amber-600 block truncate">Status: Dipesan (Ordered)</span>
      </div>
    </div>

    <!-- Card 3: Fisik Masuk Gudang Bulan Ini -->
    <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up anim-delay-150">
      <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block truncate">Fisik Masuk Gudang</span>
        <span class="text-lg font-black text-emerald-700 tracking-tight block">{{ number_format($monthlyPhysicalReceivedPieces) }} Pcs</span>
        <span class="text-[11px] font-semibold text-emerald-600 block truncate">Bulan Berjalan</span>
      </div>
    </div>

    <!-- Card 4: Total Rekanan Aktif -->
    <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up anim-delay-200">
      <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block truncate">Rekanan Terdaftar</span>
        <span class="text-lg font-black text-slate-900 tracking-tight block">{{ number_format($activeSupplierCount) }} Rekanan</span>
        <span class="text-[11px] font-semibold text-indigo-600 block truncate">Penyedia ATK Aktif</span>
      </div>
    </div>

  </div>

  <!-- ============================================================== -->
  <!-- 3. TAB NAVIGASI UTAMA (Segmented Control)                      -->
  <!-- ============================================================== -->
  <div class="flex items-center bg-slate-100/90 p-1 rounded-xl border border-slate-200/70 overflow-x-auto no-scrollbar gap-1">
    
    <button
      type="button"
      id="tab-btn-procurement"
      onclick="switchProcurementTab('procurement')"
      class="procurement-tab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all whitespace-nowrap {{ $activeTab === 'procurement' ? 'bg-white text-blue-700 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/60' }} cursor-pointer"
    >
      <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <span>Pengadaan &amp; PO</span>
      <span class="px-2 py-0.5 rounded-full text-[11px] font-mono font-bold {{ $activeTab === 'procurement' ? 'bg-blue-100 text-blue-800' : 'bg-slate-200 text-slate-600' }}">
        {{ $procurements->total() }}
      </span>
    </button>

    <button
      type="button"
      id="tab-btn-stock_in"
      onclick="switchProcurementTab('stock_in')"
      class="procurement-tab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all whitespace-nowrap {{ $activeTab === 'stock_in' ? 'bg-white text-blue-700 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/60' }} cursor-pointer"
    >
      <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
      </svg>
      <span>Riwayat Penerimaan (Stock In)</span>
      <span class="px-2 py-0.5 rounded-full text-[11px] font-mono font-bold {{ $activeTab === 'stock_in' ? 'bg-blue-100 text-blue-800' : 'bg-slate-200 text-slate-600' }}">
        {{ $stockIns->total() }}
      </span>
    </button>

    <button
      type="button"
      id="tab-btn-supplier"
      onclick="switchProcurementTab('supplier')"
      class="procurement-tab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all whitespace-nowrap {{ $activeTab === 'supplier' ? 'bg-white text-blue-700 shadow-xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/60' }} cursor-pointer"
    >
      <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
      </svg>
      <span>Data Rekanan / Supplier</span>
      <span class="px-2 py-0.5 rounded-full text-[11px] font-mono font-bold {{ $activeTab === 'supplier' ? 'bg-blue-100 text-blue-800' : 'bg-slate-200 text-slate-600' }}">
        {{ $suppliers->total() }}
      </span>
    </button>

  </div>

  <!-- ============================================================== -->
  <!-- 4. TAB 1 CONTENT: DAFTAR PENGADAAN & PO                        -->
  <!-- ============================================================== -->
  <div id="panel-procurement" class="procurement-panel {{ $activeTab === 'procurement' ? '' : 'hidden' }} space-y-4">
    
    <!-- Filter Bar Pengadaan -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs">
      <form method="GET" action="{{ route('inventory.procurement.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <input type="hidden" name="tab" value="procurement">

        <!-- Search Keyword -->
        <div class="relative lg:col-span-2">
          <input
            type="text"
            name="q_procurement"
            value="{{ request('q_procurement') }}"
            placeholder="Cari no. PO, nama supplier, no faktur..."
            class="w-full pl-9 pr-4 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 focus:bg-white transition"
          />
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>

        <!-- Filter Status -->
        <div>
          <select
            name="status_filter"
            onchange="this.form.submit()"
            class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white transition"
          >
            <option value="all">Semua Status</option>
            <option value="draft" {{ request('status_filter') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="ordered" {{ request('status_filter') === 'ordered' ? 'selected' : '' }}>Dipesan (Ordered)</option>
            <option value="received" {{ request('status_filter') === 'received' ? 'selected' : '' }}>Diterima (Received)</option>
            <option value="cancelled" {{ request('status_filter') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
          </select>
        </div>

        <!-- Filter Rekanan -->
        <div>
          <select
            name="supplier_filter"
            onchange="this.form.submit()"
            class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white transition"
          >
            <option value="all">Semua Rekanan</option>
            @foreach ($activeSuppliers as $sup)
              <option value="{{ $sup->id }}" {{ request('supplier_filter') == $sup->id ? 'selected' : '' }}>
                {{ $sup->name }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Tombol Filter & Reset -->
        <div class="flex items-center gap-2">
          <button
            type="submit"
            class="flex-1 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition text-center cursor-pointer"
          >
            Terapkan
          </button>
          @if (request()->hasAny(['q_procurement', 'status_filter', 'supplier_filter', 'date_from', 'date_to']))
            <a
              href="{{ route('inventory.procurement.index', ['tab' => 'procurement']) }}"
              class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition text-center"
              title="Reset Filter"
            >
              Reset
            </a>
          @endif
        </div>

      </form>
    </div>

    <!-- Tabel Daftar Pengadaan -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs sm:text-sm">
          <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] uppercase tracking-wider text-slate-500 font-semibold select-none">
            <tr>
              <th scope="col" class="py-3 px-4">No. PO &amp; Tanggal</th>
              <th scope="col" class="py-3 px-4">Rekanan / Vendor</th>
              <th scope="col" class="py-3 px-4">No. Faktur / SPK</th>
              <th scope="col" class="py-3 px-4">Rincian Barang</th>
              <th scope="col" class="py-3 px-4 text-right">Total Anggaran</th>
              <th scope="col" class="py-3 px-4 text-center">Status</th>
              <th scope="col" class="py-3 px-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse ($procurements as $p)
              <tr class="hover:bg-slate-50/80 transition-colors">
                
                <!-- No PO & Tanggal -->
                <td class="py-3 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <span class="font-mono font-bold text-blue-700">{{ $p->procurement_number }}</span>
                  </div>
                  <span class="text-[11px] text-slate-400 block mt-0.5">
                    {{ $p->date->format('d/m/Y') }} &bull; Oleh: {{ $p->user?->name ?? 'Admin' }}
                  </span>
                </td>

                <!-- Rekanan -->
                <td class="py-3 px-4">
                  <div class="font-bold text-slate-900">{{ $p->supplier_name }}</div>
                  @if ($p->supplier?->phone)
                    <span class="text-[11px] text-slate-400 block">{{ $p->supplier->phone }}</span>
                  @endif
                </td>

                <!-- No Faktur / Surat Jalan -->
                <td class="py-3 px-4 whitespace-nowrap">
                  @if ($p->invoice_number)
                    <span class="px-2 py-0.5 rounded-md bg-slate-100 font-mono text-[11px] font-semibold text-slate-700 border border-slate-200">
                      {{ $p->invoice_number }}
                    </span>
                  @else
                    <span class="text-slate-400 text-xs italic">- Belum Diisi -</span>
                  @endif
                </td>

                <!-- Rincian Barang -->
                <td class="py-3 px-4">
                  <span class="font-bold text-slate-800">{{ $p->details->count() }} Jenis Item</span>
                  <div class="text-[11px] text-slate-500 truncate max-w-xs" title="{{ $p->details->pluck('item_name')->join(', ') }}">
                    {{ $p->details->take(2)->map(fn($d) => "{$d->item_name} ({$d->quantity} {$d->unit})")->join(', ') }}
                    @if ($p->details->count() > 2)
                      <span class="text-blue-600 font-semibold">+{{ $p->details->count() - 2 }} lainnya</span>
                    @endif
                  </div>
                </td>

                <!-- Total Anggaran -->
                <td class="py-3 px-4 text-right whitespace-nowrap">
                  <span class="font-black text-slate-900">{{ $p->formatted_total_amount }}</span>
                </td>

                <!-- Status Badge -->
                <td class="py-3 px-4 text-center whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $p->status_badge_class }}">
                    {{ $p->status_label }}
                  </span>
                  @if ($p->received_at)
                    <span class="text-[10px] text-slate-400 block mt-0.5">
                      Diterima {{ $p->received_at->format('d/m/Y H:i') }}
                    </span>
                  @endif
                </td>

                <!-- Aksi -->
                <td class="py-3 px-4 text-right whitespace-nowrap">
                  <div class="inline-flex items-center gap-1.5">
                    
                    <!-- Tombol Detail -->
                    <button
                      type="button"
                      onclick="showProcurementDetail({{ $p->id }})"
                      class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-600 text-xs font-bold transition flex items-center gap-1 cursor-pointer"
                      title="Lihat Detail Pengadaan"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      <span>Detail</span>
                    </button>

                    <!-- Tombol Konfirmasi Terima (Jika ordered / draft) -->
                    @if (in_array($p->status, ['ordered', 'draft']))
                      <button
                        type="button"
                        onclick="confirmReceiveProcurement({{ $p->id }}, '{{ $p->procurement_number }}', '{{ $p->invoice_number }}')"
                        class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center gap-1 shadow-2xs cursor-pointer"
                        title="Konfirmasi Barang Telah Tiba di Gudang"
                      >
                        <svg class="w-3.5 h-3.5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Terima</span>
                      </button>
                    @endif

                    <!-- Dropdown Cetak PO / BAPHP -->
                    <div class="relative inline-block text-left" x-data="{ open: false }">
                      <a
                        href="{{ route('inventory.procurement.print', ['procurement' => $p->id, 'type' => 'po']) }}"
                        target="_blank"
                        class="px-2 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition inline-flex items-center gap-1"
                        title="Cetak Surat Pesanan (PO)"
                      >
                        <svg class="w-3.5 h-3.5 stroke-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak</span>
                      </a>
                    </div>

                    <!-- Tombol Batal (Jika belum diterima) -->
                    @if (in_array($p->status, ['ordered', 'draft']))
                      <button
                        type="button"
                        onclick="cancelProcurement({{ $p->id }}, '{{ $p->procurement_number }}')"
                        class="p-1.5 rounded-lg hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition cursor-pointer"
                        title="Batalkan Pengadaan"
                      >
                        <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    @endif

                  </div>
                </td>

              </tr>
            @empty
              <tr>
                <td colspan="7" class="py-12 text-center text-slate-400">
                  <div class="flex flex-col items-center justify-center gap-2">
                    <svg class="w-10 h-10 text-slate-300 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="font-semibold text-slate-600">Belum ada data pengadaan ATK</span>
                    <span class="text-xs">Klik tombol "Buat Pengadaan (PO)" untuk membuat transaksi baru.</span>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination Pengadaan -->
      @if ($procurements->hasPages())
        <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50">
          {{ $procurements->links() }}
        </div>
      @endif

    </div>

  </div>

  <!-- ============================================================== -->
  <!-- 5. TAB 2 CONTENT: RIWAYAT PENERIMAAN FISIK (STOCK IN)         -->
  <!-- ============================================================== -->
  <div id="panel-stock_in" class="procurement-panel {{ $activeTab === 'stock_in' ? '' : 'hidden' }} space-y-4">
    
    <!-- Filter Riwayat Stock In -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs">
      <form method="GET" action="{{ route('inventory.procurement.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <input type="hidden" name="tab" value="stock_in">

        <div class="relative sm:col-span-2">
          <input
            type="text"
            name="q_stock_in"
            value="{{ request('q_stock_in') }}"
            placeholder="Cari no. transaksi IN, rekanan, referensi surat jalan..."
            class="w-full pl-9 pr-4 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white transition"
          />
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>

        <div class="flex items-center gap-2">
          <select
            name="source_filter"
            onchange="this.form.submit()"
            class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white transition"
          >
            <option value="all">Semua Sumber</option>
            <option value="Procurement" {{ request('source_filter') === 'Procurement' ? 'selected' : '' }}>Pengadaan (Procurement)</option>
            <option value="Direct" {{ request('source_filter') === 'Direct' ? 'selected' : '' }}>Penerimaan Langsung</option>
            <option value="Hibah" {{ request('source_filter') === 'Hibah' ? 'selected' : '' }}>Hibah / Pemberian</option>
            <option value="Sisa Kegiatan" {{ request('source_filter') === 'Sisa Kegiatan' ? 'selected' : '' }}>Sisa Kegiatan</option>
          </select>

          @if (request()->hasAny(['q_stock_in', 'source_filter']))
            <a
              href="{{ route('inventory.procurement.index', ['tab' => 'stock_in']) }}"
              class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition text-center"
              title="Reset Filter"
            >
              Reset
            </a>
          @endif
        </div>

      </form>
    </div>

    <!-- Tabel Riwayat Stock In -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs sm:text-sm">
          <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] uppercase tracking-wider text-slate-500 font-semibold select-none">
            <tr>
              <th scope="col" class="py-3 px-4">No. Transaksi Masuk</th>
              <th scope="col" class="py-3 px-4">Tanggal Masuk</th>
              <th scope="col" class="py-3 px-4">Sumber / Asal</th>
              <th scope="col" class="py-3 px-4">No. Referensi / PO</th>
              <th scope="col" class="py-3 px-4">Total Item</th>
              <th scope="col" class="py-3 px-4 text-right">Total Kuantitas Fisik</th>
              <th scope="col" class="py-3 px-4">Petugas Penerima</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse ($stockIns as $in)
              <tr class="hover:bg-slate-50/80 transition-colors">
                
                <!-- No Transaksi -->
                <td class="py-3 px-4 whitespace-nowrap">
                  <span class="font-mono font-bold text-emerald-700">{{ $in->transaction_number }}</span>
                </td>

                <!-- Tanggal -->
                <td class="py-3 px-4 whitespace-nowrap">
                  <span class="font-semibold text-slate-900">{{ $in->date->format('d/m/Y') }}</span>
                  <span class="text-[11px] text-slate-400 block">{{ $in->created_at->format('H:i') }} WIB</span>
                </td>

                <!-- Sumber -->
                <td class="py-3 px-4">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $in->source === 'Procurement' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                    {{ $in->source }}
                  </span>
                  <span class="text-xs text-slate-700 font-semibold block mt-0.5">{{ $in->supplier_name ?: 'Internal BPTD' }}</span>
                </td>

                <!-- No Referensi / PO -->
                <td class="py-3 px-4 whitespace-nowrap">
                  @if ($in->procurement)
                    <a href="javascript:void(0)" onclick="showProcurementDetail({{ $in->procurement_id }})" class="font-mono font-semibold text-blue-600 hover:underline">
                      {{ $in->procurement->procurement_number }}
                    </a>
                  @elseif ($in->reference_number)
                    <span class="font-mono text-xs text-slate-700">{{ $in->reference_number }}</span>
                  @else
                    <span class="text-slate-400 italic text-xs">-</span>
                  @endif
                </td>

                <!-- Total Item -->
                <td class="py-3 px-4">
                  <span class="font-bold text-slate-800">{{ $in->total_items }} Varian Item</span>
                  <div class="text-[11px] text-slate-500 truncate max-w-xs" title="{{ $in->details->pluck('item_name')->join(', ') }}">
                    {{ $in->details->take(2)->map(fn($d) => "{$d->item_name} ({$d->quantity} {$d->unit})")->join(', ') }}
                    @if ($in->details->count() > 2)
                      <span class="text-emerald-600 font-semibold">+{{ $in->details->count() - 2 }} lainnya</span>
                    @endif
                  </div>
                </td>

                <!-- Total Kuantitas -->
                <td class="py-3 px-4 text-right whitespace-nowrap">
                  <span class="font-black text-emerald-700 text-sm">{{ number_format($in->total_quantity) }} Pcs</span>
                </td>

                <!-- Petugas -->
                <td class="py-3 px-4 whitespace-nowrap">
                  <span class="font-semibold text-slate-800">{{ $in->user?->name ?? 'Petugas Gudang' }}</span>
                </td>

              </tr>
            @empty
              <tr>
                <td colspan="7" class="py-12 text-center text-slate-400">
                  <div class="flex flex-col items-center justify-center gap-2">
                    <svg class="w-10 h-10 text-slate-300 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span class="font-semibold text-slate-600">Belum ada riwayat penerimaan barang fisik</span>
                    <span class="text-xs">Barang akan tercatat di sini saat pengadaan dikonfirmasi terima atau saat penerimaan langsung.</span>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($stockIns->hasPages())
        <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50">
          {{ $stockIns->links() }}
        </div>
      @endif

    </div>

  </div>

  <!-- ============================================================== -->
  <!-- 6. TAB 3 CONTENT: DATA REKANAN / SUPPLIER                      -->
  <!-- ============================================================== -->
  <div id="panel-supplier" class="procurement-panel {{ $activeTab === 'supplier' ? '' : 'hidden' }} space-y-4">
    
    <!-- Top Filter Rekanan -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-3">
      <form method="GET" action="{{ route('inventory.procurement.index') }}" class="w-full sm:w-80">
        <input type="hidden" name="tab" value="supplier">
        <div class="relative">
          <input
            type="text"
            name="q_supplier"
            value="{{ request('q_supplier') }}"
            placeholder="Cari nama rekanan, kontak, telepon..."
            class="w-full pl-9 pr-4 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white transition"
          />
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </form>

      <button
        type="button"
        onclick="openSupplierModal()"
        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm transition shadow-xs cursor-pointer"
      >
        <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        <span>Tambah Rekanan Baru</span>
      </button>
    </div>

    <!-- Grid / Tabel Rekanan -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs sm:text-sm">
          <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] uppercase tracking-wider text-slate-500 font-semibold select-none">
            <tr>
              <th scope="col" class="py-3 px-4">Kode &amp; Nama Rekanan</th>
              <th scope="col" class="py-3 px-4">Kontak Person</th>
              <th scope="col" class="py-3 px-4">Telepon &amp; Email</th>
              <th scope="col" class="py-3 px-4">NPWP</th>
              <th scope="col" class="py-3 px-4">Alamat</th>
              <th scope="col" class="py-3 px-4 text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse ($suppliers as $s)
              <tr class="hover:bg-slate-50/80 transition-colors">
                
                <!-- Kode & Nama -->
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-2">
                    <span class="font-mono font-bold text-[11px] px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                      {{ $s->code }}
                    </span>
                    <span class="font-bold text-slate-900 text-sm">{{ $s->name }}</span>
                  </div>
                  @if ($s->items_count > 0)
                    <span class="text-[11px] text-slate-400 block mt-0.5">{{ $s->items_count }} item ATK terkait</span>
                  @endif
                </td>

                <!-- Kontak Person -->
                <td class="py-3.5 px-4">
                  <span class="font-semibold text-slate-800">{{ $s->contact_person ?: '-' }}</span>
                </td>

                <!-- Telepon & Email -->
                <td class="py-3.5 px-4 whitespace-nowrap">
                  <div class="font-semibold text-slate-800">{{ $s->phone ?: '-' }}</div>
                  @if ($s->email)
                    <span class="text-[11px] text-slate-400 block">{{ $s->email }}</span>
                  @endif
                </td>

                <!-- NPWP -->
                <td class="py-3.5 px-4 whitespace-nowrap">
                  <span class="font-mono text-xs text-slate-700">{{ $s->tax_number ?: '-' }}</span>
                </td>

                <!-- Alamat -->
                <td class="py-3.5 px-4 max-w-xs">
                  <span class="text-xs text-slate-600 block line-clamp-2">{{ $s->address ?: '-' }}</span>
                </td>

                <!-- Status -->
                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $s->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                    {{ $s->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </td>

              </tr>
            @empty
              <tr>
                <td colspan="6" class="py-12 text-center text-slate-400">
                  <div class="flex flex-col items-center justify-center gap-2">
                    <svg class="w-10 h-10 text-slate-300 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="font-semibold text-slate-600">Belum ada data rekanan vendor</span>
                    <span class="text-xs">Klik tombol "Tambah Rekanan Baru" untuk mendaftarkan vendor rekanan BPTD.</span>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($suppliers->hasPages())
        <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50">
          {{ $suppliers->links() }}
        </div>
      @endif

    </div>

  </div>

  <!-- ============================================================== -->
  <!-- 7. MODAL: BUAT PENGADAAN (PO) BARU                             -->
  <!-- ============================================================== -->
  <div
    id="modal-procurement-form"
    class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 hidden transition-opacity"
  >
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-4xl w-full max-h-[92vh] flex flex-col overflow-hidden anim-scale-in">
      
      <!-- Modal Header -->
      <div class="px-5 py-4 border-b border-slate-200/80 flex items-center justify-between bg-slate-50/80 shrink-0">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-tight">
              Buat Surat Pesanan Pengadaan (PO)
            </h2>
            <p class="text-xs text-slate-500 font-medium">
              Formulir pengadaan barang ATK ke pihak rekanan / penyedia
            </p>
          </div>
        </div>
        <button
          type="button"
          onclick="closeProcurementModal()"
          class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer"
        >
          <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Modal Body (Scrollable) -->
      <form id="form-procurement" method="POST" action="{{ route('inventory.procurement.store') }}" class="flex-1 overflow-y-auto p-5 space-y-5">
        @csrf

        <!-- Baris Info Utama: Tanggal, Supplier, Faktur, Status Awal -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 bg-slate-50/70 p-4 rounded-xl border border-slate-200/70">
          
          <!-- Tanggal -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
              Tanggal Pengadaan <span class="text-rose-500">*</span>
            </label>
            <input
              type="date"
              name="date"
              required
              value="{{ date('Y-m-d') }}"
              class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
            />
          </div>

          <!-- Supplier / Rekanan -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                Rekanan / Vendor <span class="text-rose-500">*</span>
              </label>
              <button
                type="button"
                onclick="openSupplierModal()"
                class="text-[11px] text-blue-600 font-bold hover:underline cursor-pointer"
              >
                + Baru
              </button>
            </div>
            <select
              id="select-procurement-supplier"
              name="supplier_id"
              required
              class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
            >
              <option value="">-- Pilih Rekanan Penyedia --</option>
              @foreach ($activeSuppliers as $sup)
                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
              @endforeach
            </select>
          </div>

          <!-- No. Faktur / Surat Jalan -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
              No. Faktur / Surat Jalan
            </label>
            <input
              type="text"
              name="invoice_number"
              placeholder="Contoh: INV-2026/X/001"
              class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
            />
          </div>

          <!-- Status Awal -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
              Status Awal <span class="text-rose-500">*</span>
            </label>
            <select
              name="status"
              required
              id="procurement-status-select"
              class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white font-semibold"
            >
              <option value="ordered" selected>Dipesan (Ordered)</option>
              <option value="draft">Simpan Draft</option>
              <option value="received">Langsung Diterima (Tambah Stok)</option>
            </select>
          </div>

        </div>

        <!-- Tabel Dinamis Item Pengadaan (Repeater) -->
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <h3 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
              <span>Daftar Barang yang Diadakan</span>
              <span id="items-count-badge" class="px-2 py-0.5 rounded-full text-[11px] font-mono bg-blue-100 text-blue-800">1 Item</span>
            </h3>
            <button
              type="button"
              onclick="addProcurementItemRow()"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs transition cursor-pointer"
            >
              <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
              </svg>
              <span>Tambah Baris</span>
            </button>
          </div>

          <!-- Container Baris Item -->
          <div class="border border-slate-200/80 rounded-2xl overflow-hidden shadow-2xs">
            <div class="overflow-x-auto">
              <table class="w-full text-left text-xs sm:text-sm" id="table-procurement-items">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-600 font-bold border-b border-slate-200">
                  <tr>
                    <th class="py-2.5 px-3 min-w-[200px]">Barang ATK</th>
                    <th class="py-2.5 px-3 min-w-[170px]">Satuan Beli &amp; Rasio</th>
                    <th class="py-2.5 px-3 w-28 text-center">Jumlah Beli</th>
                    <th class="py-2.5 px-3 min-w-[140px] text-center bg-emerald-50/60 text-emerald-900 border-x border-emerald-100/80">Fisik Masuk Gudang</th>
                    <th class="py-2.5 px-3 min-w-[160px]">Harga Satuan Beli (Rp)</th>
                    <th class="py-2.5 px-3 min-w-[140px] text-right">Subtotal (Rp)</th>
                    <th class="py-2.5 px-3 w-10 text-center"></th>
                  </tr>
                </thead>
                <tbody id="procurement-items-tbody" class="divide-y divide-slate-100">
                  <!-- Baris Template awal akan di-render oleh JS -->
                </tbody>
              </table>
            </div>

            <!-- Footer Grand Total & Physical Pieces Summary -->
            <div class="p-3 bg-slate-50/90 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
              <div class="flex items-center gap-2 text-xs text-slate-600">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-100/80 text-emerald-800 font-bold font-mono">
                  <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                  </svg>
                  <span id="label-total-physical-pieces">0 Fisik Unit Masuk</span>
                </span>
                <span class="text-slate-300">&bull;</span>
                <span class="text-[11px] text-slate-500 font-medium">Stok fisik gudang otomatis dihitung dan disimpan dalam satuan terkecil.</span>
              </div>
              <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Grand Total:</span>
                <span id="label-grand-total" class="text-base sm:text-lg font-black text-blue-700 font-mono">Rp 0</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Catatan -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
            Catatan / Keperluan Pengadaan
          </label>
          <textarea
            name="notes"
            rows="2"
            placeholder="Keterangan anggaran, sumber dana DIPA, atau catatan pengiriman..."
            class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white transition"
          ></textarea>
        </div>

      </form>

      <!-- Modal Footer -->
      <div class="px-5 py-3 border-t border-slate-200/80 bg-slate-50/80 flex items-center justify-between shrink-0">
        <button
          type="button"
          onclick="closeProcurementModal()"
          class="px-4 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs sm:text-sm border border-slate-200 transition cursor-pointer"
        >
          Batal
        </button>

        <button
          type="button"
          onclick="submitProcurementForm()"
          id="btn-submit-procurement"
          class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs sm:text-sm transition shadow-xs cursor-pointer"
        >
          <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          <span id="btn-submit-procurement-text">Simpan Pengadaan</span>
        </button>
      </div>

    </div>
  </div>

  <!-- ============================================================== -->
  <!-- 8. MODAL: PENERIMAAN LANGSUNG (DIRECT STOCK IN)               -->
  <!-- ============================================================== -->
  <div
    id="modal-direct-stockin"
    class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 hidden transition-opacity"
  >
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-3xl w-full max-h-[92vh] flex flex-col overflow-hidden anim-scale-in">
      
      <!-- Header -->
      <div class="px-5 py-4 border-b border-slate-200/80 flex items-center justify-between bg-slate-50/80 shrink-0">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-tight">
              Penerimaan Stok Langsung ke Gudang
            </h2>
            <p class="text-xs text-slate-500 font-medium">
              Penerimaan tanpa PO formal (Hibah, Sisa Kegiatan Kantor, Pembelian Tunai)
            </p>
          </div>
        </div>
        <button
          type="button"
          onclick="closeDirectStockInModal()"
          class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer"
        >
          <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Form Direct Stock In -->
      <form id="form-direct-stockin" method="POST" action="{{ route('inventory.procurement.direct-stock-in.store') }}" class="flex-1 overflow-y-auto p-5 space-y-5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50/70 p-4 rounded-xl border border-slate-200/70">
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
              Tanggal Terima <span class="text-rose-500">*</span>
            </label>
            <input
              type="date"
              name="date"
              required
              value="{{ date('Y-m-d') }}"
              class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
              Sumber Penerimaan <span class="text-rose-500">*</span>
            </label>
            <select
              name="source"
              required
              class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white font-semibold"
            >
              <option value="Direct">Penerimaan Langsung</option>
              <option value="Hibah">Hibah / Pemberian</option>
              <option value="Sisa Kegiatan">Sisa Kegiatan / Pengembalian</option>
              <option value="Lainnya">Lainnya</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
              Asal / Pemberi / Toko
            </label>
            <input
              type="text"
              name="supplier_name"
              placeholder="Contoh: Toko Buku Siswa / Balai X"
              class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white"
            />
          </div>
        </div>

        <!-- Tabel Item Direct -->
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <h3 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-wider">
              Daftar Barang Masuk
            </h3>
            <button
              type="button"
              onclick="addDirectItemRow()"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs transition cursor-pointer"
            >
              <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
              </svg>
              <span>Tambah Baris</span>
            </button>
          </div>

          <div class="border border-slate-200/80 rounded-2xl overflow-hidden shadow-2xs">
            <div class="overflow-x-auto">
              <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-600 font-bold border-b border-slate-200">
                  <tr>
                    <th class="py-2.5 px-3 min-w-[200px]">Pilih Barang ATK</th>
                    <th class="py-2.5 px-3 min-w-[170px]">Satuan Masuk &amp; Rasio</th>
                    <th class="py-2.5 px-3 w-28 text-center">Jumlah Masuk</th>
                    <th class="py-2.5 px-3 min-w-[140px] text-center bg-emerald-50/60 text-emerald-900 border-x border-emerald-100/80">Fisik Masuk Gudang</th>
                    <th class="py-2.5 px-3 w-10 text-center"></th>
                  </tr>
                </thead>
                <tbody id="direct-items-tbody" class="divide-y divide-slate-100">
                  <!-- Baris direct diisi JS -->
                </tbody>
              </table>
            </div>
            <!-- Footer Total Fisik Direct Stock In -->
            <div class="p-3 bg-slate-50/90 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs">
              <span class="text-[11px] text-slate-500 font-medium">
                * Barang langsung menambah saldo stok fisik inventaris gudang setelah disimpan.
              </span>
              <span id="label-direct-total-physical" class="font-mono font-bold text-emerald-800 bg-emerald-100/80 px-2.5 py-1 rounded-lg">
                0 Fisik Unit Masuk
              </span>
            </div>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
            Catatan Penerimaan
          </label>
          <textarea
            name="notes"
            rows="2"
            placeholder="Keterangan barang masuk..."
            class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-slate-50 focus:bg-white transition"
          ></textarea>
        </div>

      </form>

      <!-- Footer -->
      <div class="px-5 py-3 border-t border-slate-200/80 bg-slate-50/80 flex items-center justify-between shrink-0">
        <button
          type="button"
          onclick="closeDirectStockInModal()"
          class="px-4 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs sm:text-sm border border-slate-200 transition cursor-pointer"
        >
          Batal
        </button>

        <button
          type="button"
          onclick="submitDirectStockInForm()"
          class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm transition shadow-xs cursor-pointer"
        >
          <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          <span>Simpan &amp; Tambah Stok</span>
        </button>
      </div>

    </div>
  </div>

  <!-- ============================================================== -->
  <!-- 9. MODAL: DETAIL PENGADAAN & KONFIRMASI PENERIMAAN             -->
  <!-- ============================================================== -->
  <div
    id="modal-procurement-detail"
    class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 hidden transition-opacity"
  >
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-3xl w-full max-h-[92vh] flex flex-col overflow-hidden anim-scale-in">
      
      <!-- Detail Header -->
      <div class="px-5 py-4 border-b border-slate-200/80 flex items-center justify-between bg-slate-50/80 shrink-0">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-tight flex items-center gap-2">
              <span id="detail-po-number">PO-...</span>
              <span id="detail-status-badge" class="px-2.5 py-0.5 rounded-full text-xs font-bold border">Status</span>
            </h2>
            <p class="text-xs text-slate-500 font-medium" id="detail-header-sub">
              Rincian berkas pengadaan ATK
            </p>
          </div>
        </div>
        <button
          type="button"
          onclick="closeProcurementDetailModal()"
          class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer"
        >
          <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Detail Body -->
      <div class="flex-1 overflow-y-auto p-5 space-y-4">
        
        <!-- Info Ringkas -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50/80 p-3.5 rounded-xl border border-slate-200/70 text-xs">
          <div>
            <span class="text-slate-400 font-semibold block">Tanggal Pengadaan</span>
            <span id="detail-date" class="font-bold text-slate-800">-</span>
          </div>
          <div>
            <span class="text-slate-400 font-semibold block">Rekanan / Vendor</span>
            <span id="detail-supplier" class="font-bold text-slate-800">-</span>
          </div>
          <div>
            <span class="text-slate-400 font-semibold block">No. Faktur / Surat Jalan</span>
            <span id="detail-invoice" class="font-bold font-mono text-slate-800">-</span>
          </div>
          <div>
            <span class="text-slate-400 font-semibold block">Petugas Logistik</span>
            <span id="detail-officer" class="font-bold text-slate-800">-</span>
          </div>
        </div>

        <!-- Tabel Rincian Item -->
        <div class="border border-slate-200/80 rounded-2xl overflow-hidden shadow-2xs">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
              <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-600 font-bold border-b border-slate-200">
                <tr>
                  <th class="py-2.5 px-3 min-w-[180px]">Nama Barang ATK</th>
                  <th class="py-2.5 px-3 text-center min-w-[110px]">Kuantitas Beli</th>
                  <th class="py-2.5 px-3 text-center min-w-[130px]">Rasio Konversi</th>
                  <th class="py-2.5 px-3 text-center min-w-[130px] bg-emerald-50/60 text-emerald-900 border-x border-emerald-100/80">Fisik Masuk Gudang</th>
                  <th class="py-2.5 px-3 text-right min-w-[130px]">Harga Satuan</th>
                  <th class="py-2.5 px-3 text-right min-w-[130px]">Subtotal</th>
                </tr>
              </thead>
              <tbody id="detail-items-tbody" class="divide-y divide-slate-100 font-medium">
                <!-- Item detail baris -->
              </tbody>
              <tfoot class="bg-slate-50/90 border-t border-slate-200 font-black">
                <tr>
                  <td colspan="5" class="py-2.5 px-3 text-right text-xs uppercase tracking-wider text-slate-600">Total Anggaran:</td>
                  <td id="detail-total-amount" class="py-2.5 px-3 text-right text-sm text-blue-700 font-mono">Rp 0</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        <!-- Panel Konfirmasi Terima Barang (Jika status ordered/draft) -->
        <div id="detail-receive-box" class="p-4 rounded-xl bg-emerald-50/80 border border-emerald-200/80 space-y-3 hidden">
          <div class="flex items-center gap-2 text-emerald-800 font-bold text-xs sm:text-sm">
            <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Verifikasi Kedatangan Barang Fisik ke Gudang BPTD</span>
          </div>
          <p class="text-xs text-emerald-700">
            Pastikan seluruh barang fisik telah diperiksa jumlah dan kondisinya. Konfirmasi ini akan otomatis menambah stok barang di gudang dan mencatat kartu stok (Ledger).
          </p>
          <div class="flex flex-col sm:flex-row sm:items-center gap-3 pt-1">
            <input
              type="text"
              id="detail-receive-invoice-input"
              placeholder="Masukkan No. Faktur / Surat Jalan (Opsional)"
              class="flex-1 px-3 py-1.5 rounded-lg text-xs border border-emerald-300 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
            <button
              type="button"
              id="btn-execute-receive"
              class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-2xs flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap"
            >
              <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              <span>Konfirmasi Terima Barang</span>
            </button>
          </div>
        </div>

      </div>

      <!-- Detail Footer -->
      <div class="px-5 py-3 border-t border-slate-200/80 bg-slate-50/80 flex items-center justify-between shrink-0">
        <a
          id="detail-print-btn"
          href="#"
          target="_blank"
          class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition"
        >
          <svg class="w-4 h-4 stroke-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
          </svg>
          <span>Cetak Dokumen</span>
        </a>

        <button
          type="button"
          onclick="closeProcurementDetailModal()"
          class="px-4 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs sm:text-sm border border-slate-200 transition cursor-pointer"
        >
          Tutup
        </button>
      </div>

    </div>
  </div>

  <!-- ============================================================== -->
  <!-- 10. MODAL: TAMBAH REKANAN CEPAT (AJAX)                         -->
  <!-- ============================================================== -->
  <div
    id="modal-supplier-form"
    class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 hidden transition-opacity"
  >
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full flex flex-col overflow-hidden anim-scale-in">
      
      <div class="px-5 py-4 border-b border-slate-200/80 flex items-center justify-between bg-slate-50/80 shrink-0">
        <h2 class="text-base font-black text-slate-900 tracking-tight leading-tight">
          Tambah Rekanan / Vendor Baru
        </h2>
        <button
          type="button"
          onclick="closeSupplierModal()"
          class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer"
        >
          <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form id="form-add-supplier" onsubmit="submitSupplierForm(event)" class="p-5 space-y-3.5">
        @csrf
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
            Nama Perusahaan / Toko <span class="text-rose-500">*</span>
          </label>
          <input
            type="text"
            name="name"
            required
            placeholder="Contoh: CV. Berkah Logistik"
            class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white transition"
          />
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
              Kontak Person
            </label>
            <input
              type="text"
              name="contact_person"
              placeholder="Nama PIC"
              class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white transition"
            />
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
              No. Telepon / WA
            </label>
            <input
              type="text"
              name="phone"
              placeholder="0812..."
              class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white transition"
            />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
              NPWP
            </label>
            <input
              type="text"
              name="tax_number"
              placeholder="00.000.000.0-000.000"
              class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white transition"
            />
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
              Email
            </label>
            <input
              type="email"
              name="email"
              placeholder="vendor@mail.com"
              class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white transition"
            />
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
            Alamat Kantor
          </label>
          <textarea
            name="address"
            rows="2"
            placeholder="Alamat lengkap rekanan..."
            class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white transition"
          ></textarea>
        </div>

        <div class="pt-2 flex items-center justify-end gap-2">
          <button
            type="button"
            onclick="closeSupplierModal()"
            class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer"
          >
            Batal
          </button>
          <button
            type="submit"
            id="btn-submit-supplier"
            class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition shadow-xs cursor-pointer"
          >
            Simpan Rekanan
          </button>
        </div>
      </form>

    </div>
  </div>

</main>

@push('scripts')
<script>
  // Data Master Items untuk Dropdown Repeater
  const masterItems = @json($activeItems);
  let procurementRowIndex = 0;
  let directRowIndex = 0;
  let currentDetailProcurementId = null;

  // Tab Switcher
  function switchProcurementTab(tabName) {
    document.querySelectorAll('.procurement-panel').forEach(p => p.classList.add('hidden'));
    const targetPanel = document.getElementById('panel-' + tabName);
    if (targetPanel) targetPanel.classList.remove('hidden');

    document.querySelectorAll('.procurement-tab-btn').forEach(b => {
      b.classList.remove('bg-white', 'text-blue-700', 'shadow-xs', 'border', 'border-slate-200/60');
      b.classList.add('text-slate-600', 'hover:text-slate-900', 'hover:bg-slate-200/60');
    });

    const activeBtn = document.getElementById('tab-btn-' + tabName);
    if (activeBtn) {
      activeBtn.classList.add('bg-white', 'text-blue-700', 'shadow-xs', 'border', 'border-slate-200/60');
      activeBtn.classList.remove('text-slate-600', 'hover:text-slate-900', 'hover:bg-slate-200/60');
    }

    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.replaceState({}, '', url);
  }

  // ==========================================
  // PENGADAAN (PO) REPEATER LOGIC
  // ==========================================
  function openProcurementModal() {
    document.getElementById('modal-procurement-form').classList.remove('hidden');
    const tbody = document.getElementById('procurement-items-tbody');
    if (tbody.children.length === 0) {
      addProcurementItemRow();
    }
  }

  function closeProcurementModal() {
    document.getElementById('modal-procurement-form').classList.add('hidden');
  }

  function addProcurementItemRow() {
    const tbody = document.getElementById('procurement-items-tbody');
    const idx = procurementRowIndex++;

    let options = '<option value="">-- Pilih Barang ATK --</option>';
    masterItems.forEach(it => {
      const stock = it.current_stock || 0;
      const smallUnit = it.effective_small_unit || 'Pcs';
      const pkgUnit = it.unit || 'Pcs';
      const conv = it.conversion_rate || 1;
      const convLabel = (conv > 1 && pkgUnit.toLowerCase() !== smallUnit.toLowerCase()) 
        ? ` [1 ${pkgUnit} = ${conv} ${smallUnit}]` 
        : '';
      options += `<option value="${it.id}" data-name="${it.name}" data-code="${it.code}" data-unit="${pkgUnit}" data-small-unit="${smallUnit}" data-conv="${conv}" data-stock="${stock}">${it.name} (${it.code})${convLabel} - Stok: ${stock} ${smallUnit}</option>`;
    });

    const tr = document.createElement('tr');
    tr.id = `procurement-row-${idx}`;
    tr.className = 'hover:bg-slate-50/60 transition';
    tr.innerHTML = `
      <td class="py-2.5 px-3">
        <select name="items[${idx}][item_id]" required onchange="onProcurementItemSelect(${idx})" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-blue-500 bg-white font-medium">
          ${options}
        </select>
        <div id="row-stock-hint-${idx}" class="text-[10px] text-slate-500 font-medium mt-1 hidden"></div>
      </td>
      <td class="py-2.5 px-3">
        <select name="items[${idx}][unit_choice]" id="unit-choice-${idx}" onchange="onProcurementUnitChoiceChange(${idx})" class="w-full px-2 py-1.5 rounded-lg border border-slate-200 text-xs bg-white font-semibold">
          <option value="base">Pcs (Satuan)</option>
        </select>
        <input type="hidden" name="items[${idx}][unit]" id="unit-val-${idx}" value="Pcs">
        <input type="hidden" name="items[${idx}][conversion_factor]" id="conv-factor-${idx}" value="1">
        <div id="row-ratio-badge-${idx}" class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200/80 mt-1 hidden"></div>
      </td>
      <td class="py-2.5 px-3 text-center">
        <input type="number" name="items[${idx}][quantity]" id="qty-${idx}" min="1" value="1" required oninput="calculateProcurementSubtotal(${idx})" class="w-full px-2 py-1.5 rounded-lg border border-slate-200 text-xs font-mono font-bold text-center bg-white" />
        <span id="qty-unit-label-${idx}" class="text-[10px] font-bold text-slate-500 block mt-0.5">Pcs</span>
      </td>
      <td class="py-2.5 px-3 text-center bg-emerald-50/40 border-x border-emerald-100/60">
        <div class="flex flex-col items-center justify-center p-1 rounded-lg">
          <span id="row-base-qty-${idx}" class="font-mono font-black text-emerald-800 text-xs sm:text-sm">1 Pcs</span>
          <span id="row-base-calc-${idx}" class="text-[10px] text-emerald-600 font-semibold block mt-0.5">(1 &times; 1)</span>
        </div>
      </td>
      <td class="py-2.5 px-3">
        <input type="number" name="items[${idx}][unit_price]" id="price-${idx}" min="0" step="500" value="0" required oninput="calculateProcurementSubtotal(${idx})" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs font-mono font-bold text-right bg-white" placeholder="0" />
        <div id="row-netto-hint-${idx}" class="text-[10px] text-blue-600 font-semibold text-right mt-0.5 hidden"></div>
      </td>
      <td class="py-2.5 px-3 text-right">
        <span id="subtotal-label-${idx}" class="font-mono font-bold text-slate-900 text-xs sm:text-sm">Rp 0</span>
      </td>
      <td class="py-2.5 px-3 text-center">
        <button type="button" onclick="removeProcurementItemRow(${idx})" class="p-1 rounded text-slate-300 hover:text-rose-500 hover:bg-rose-50 transition cursor-pointer" title="Hapus Baris">
          <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
      </td>
    `;
    tbody.appendChild(tr);
    updateProcurementCountBadge();
  }

  function removeProcurementItemRow(idx) {
    const tbody = document.getElementById('procurement-items-tbody');
    if (tbody.children.length <= 1) {
      if (window.showToast) window.showToast('info', 'Minimal satu item harus ada dalam pengadaan.');
      return;
    }
    const row = document.getElementById(`procurement-row-${idx}`);
    if (row) row.remove();
    updateProcurementCountBadge();
    calculateProcurementGrandTotal();
  }

  function updateProcurementCountBadge() {
    const tbody = document.getElementById('procurement-items-tbody');
    const badge = document.getElementById('items-count-badge');
    if (badge && tbody) {
      badge.textContent = `${tbody.children.length} Item`;
    }
  }

  function onProcurementItemSelect(idx) {
    const select = document.querySelector(`select[name="items[${idx}][item_id]"]`);
    const opt = select?.selectedOptions[0];
    const stockHint = document.getElementById(`row-stock-hint-${idx}`);
    if (!opt || !opt.value) {
      if (stockHint) stockHint.classList.add('hidden');
      return;
    }

    const unitPkg = opt.getAttribute('data-unit') || 'Pcs';
    const smallUnit = opt.getAttribute('data-small-unit') || 'Pcs';
    const conv = parseInt(opt.getAttribute('data-conv') || '1', 10);
    const stock = opt.getAttribute('data-stock') || '0';

    if (stockHint) {
      stockHint.textContent = `Stok Gudang: ${Number(stock).toLocaleString('id-ID')} ${smallUnit}`;
      stockHint.classList.remove('hidden');
    }

    const unitChoiceSelect = document.getElementById(`unit-choice-${idx}`);
    unitChoiceSelect.innerHTML = '';

    if (conv > 1 && unitPkg.toLowerCase() !== smallUnit.toLowerCase()) {
      unitChoiceSelect.innerHTML += `<option value="pack" data-unit="${unitPkg}" data-conv="${conv}">📦 ${unitPkg} (Isi ${conv} ${smallUnit})</option>`;
      unitChoiceSelect.innerHTML += `<option value="base" data-unit="${smallUnit}" data-conv="1">🏷️ ${smallUnit} (Satuan Eceran)</option>`;
    } else {
      unitChoiceSelect.innerHTML += `<option value="base" data-unit="${smallUnit}" data-conv="1">🏷️ ${smallUnit} (Satuan Tunggal)</option>`;
    }

    onProcurementUnitChoiceChange(idx);
    calculateProcurementSubtotal(idx);
  }

  function onProcurementUnitChoiceChange(idx) {
    const choiceSelect = document.getElementById(`unit-choice-${idx}`);
    const opt = choiceSelect?.selectedOptions[0];
    if (!opt) return;

    const unit = opt.getAttribute('data-unit') || 'Pcs';
    const conv = parseInt(opt.getAttribute('data-conv') || '1', 10);

    document.getElementById(`unit-val-${idx}`).value = unit;
    document.getElementById(`conv-factor-${idx}`).value = conv;

    const qtyUnitLabel = document.getElementById(`qty-unit-label-${idx}`);
    if (qtyUnitLabel) qtyUnitLabel.textContent = unit;

    const itemSelect = document.querySelector(`select[name="items[${idx}][item_id]"]`);
    const itemOpt = itemSelect?.selectedOptions[0];
    const unitPkg = itemOpt?.getAttribute('data-unit') || unit;
    const smallUnit = itemOpt?.getAttribute('data-small-unit') || 'Pcs';
    const itemConv = parseInt(itemOpt?.getAttribute('data-conv') || '1', 10);

    const badge = document.getElementById(`row-ratio-badge-${idx}`);
    if (badge) {
      if (itemConv > 1 && unitPkg.toLowerCase() !== smallUnit.toLowerCase()) {
        badge.innerHTML = `💡 1 ${unitPkg} = ${itemConv} ${smallUnit}`;
        badge.classList.remove('hidden');
      } else {
        badge.classList.add('hidden');
      }
    }

    calculateProcurementSubtotal(idx);
  }

  function calculateProcurementSubtotal(idx) {
    const qty = parseFloat(document.getElementById(`qty-${idx}`)?.value || '0');
    const price = parseFloat(document.getElementById(`price-${idx}`)?.value || '0');
    const conv = parseInt(document.getElementById(`conv-factor-${idx}`)?.value || '1', 10);
    const unit = document.getElementById(`unit-val-${idx}`)?.value || 'Pcs';

    const itemSelect = document.querySelector(`select[name="items[${idx}][item_id]"]`);
    const itemOpt = itemSelect?.selectedOptions[0];
    const smallUnit = itemOpt?.getAttribute('data-small-unit') || 'Pcs';

    const subtotal = qty * price;
    const subLabel = document.getElementById(`subtotal-label-${idx}`);
    if (subLabel) {
      subLabel.textContent = 'Rp ' + Math.round(subtotal).toLocaleString('id-ID');
    }

    // Hitung Total Fisik Masuk Gudang
    const baseQty = qty * conv;
    const baseQtyEl = document.getElementById(`row-base-qty-${idx}`);
    const baseCalcEl = document.getElementById(`row-base-calc-${idx}`);
    if (baseQtyEl) {
      baseQtyEl.textContent = `${Math.round(baseQty).toLocaleString('id-ID')} ${smallUnit}`;
    }
    if (baseCalcEl) {
      if (conv > 1) {
        baseCalcEl.textContent = `(${qty} ${unit} × ${conv} ${smallUnit}/${unit})`;
      } else {
        baseCalcEl.textContent = `(${qty} ${smallUnit})`;
      }
    }

    // Hitung Netto Harga Satuan per Eceran Fisik
    const nettoEl = document.getElementById(`row-netto-hint-${idx}`);
    if (nettoEl) {
      if (conv > 1 && price > 0) {
        const netPrice = Math.round(price / conv);
        nettoEl.innerHTML = `@ Rp ${netPrice.toLocaleString('id-ID')} / ${smallUnit}`;
        nettoEl.classList.remove('hidden');
      } else if (price > 0) {
        nettoEl.innerHTML = `@ Rp ${Math.round(price).toLocaleString('id-ID')} / ${smallUnit}`;
        nettoEl.classList.remove('hidden');
      } else {
        nettoEl.classList.add('hidden');
      }
    }

    calculateProcurementGrandTotal();
  }

  function calculateProcurementGrandTotal() {
    let grandTotal = 0;
    let totalPhysicalPieces = 0;
    const tbody = document.getElementById('procurement-items-tbody');
    tbody.querySelectorAll('tr').forEach(tr => {
      const idx = tr.id.replace('procurement-row-', '');
      const qty = parseFloat(document.getElementById(`qty-${idx}`)?.value || '0');
      const price = parseFloat(document.getElementById(`price-${idx}`)?.value || '0');
      const conv = parseInt(document.getElementById(`conv-factor-${idx}`)?.value || '1', 10);
      grandTotal += (qty * price);
      totalPhysicalPieces += (qty * conv);
    });

    const grandLabel = document.getElementById('label-grand-total');
    if (grandLabel) {
      grandLabel.textContent = 'Rp ' + Math.round(grandTotal).toLocaleString('id-ID');
    }

    const piecesLabel = document.getElementById('label-total-physical-pieces');
    if (piecesLabel) {
      piecesLabel.textContent = `${Math.round(totalPhysicalPieces).toLocaleString('id-ID')} Fisik Unit Masuk`;
    }
  }

  function submitProcurementForm() {
    const form = document.getElementById('form-procurement');
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    const supplierSelect = document.getElementById('select-procurement-supplier');
    if (!supplierSelect.value) {
      if (window.showToast) window.showToast('error', 'Silakan pilih rekanan/vendor penyedia terlebih dahulu.');
      return;
    }

    const tbody = document.getElementById('procurement-items-tbody');
    let hasItem = false;
    tbody.querySelectorAll('select[name^="items"]').forEach(s => {
      if (s.name.includes('[item_id]') && s.value) hasItem = true;
    });

    if (!hasItem) {
      if (window.showToast) window.showToast('error', 'Minimal pilih 1 item barang yang diadakan.');
      return;
    }

    form.submit();
  }

  // ==========================================
  // DETAIL PENGADAAN & TERIMA
  // ==========================================
  async function showProcurementDetail(procurementId) {
    currentDetailProcurementId = procurementId;
    const modal = document.getElementById('modal-procurement-detail');
    modal.classList.remove('hidden');

    // Loading State
    document.getElementById('detail-po-number').textContent = 'Memuat data...';
    document.getElementById('detail-items-tbody').innerHTML = '<tr><td colspan="6" class="py-6 text-center text-slate-400">Mengambil data pengadaan...</td></tr>';

    try {
      const res = await fetch(`/inventory/procurement/${procurementId}`, {
        headers: { 'Accept': 'application/json' }
      });
      const json = await res.json();
      if (!json.success || !json.data) throw new Error(json.message || 'Gagal memuat detail');

      const p = json.data;
      document.getElementById('detail-po-number').textContent = p.procurement_number;
      
      const badge = document.getElementById('detail-status-badge');
      badge.textContent = p.status_label;
      badge.className = `px-2.5 py-0.5 rounded-full text-xs font-bold border ${p.status_badge_class}`;

      document.getElementById('detail-date').textContent = new Date(p.date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
      document.getElementById('detail-supplier').textContent = p.supplier_name || '-';
      document.getElementById('detail-invoice').textContent = p.invoice_number || '-';
      document.getElementById('detail-officer').textContent = p.user?.name || 'Petugas Logistik';
      document.getElementById('detail-total-amount').textContent = p.formatted_total_amount;

      document.getElementById('detail-print-btn').href = `/inventory/procurement/${p.id}/print?type=po`;

      // Render Item Table dengan Rasio & Fisik Jelas
      const tbody = document.getElementById('detail-items-tbody');
      tbody.innerHTML = '';
      p.details.forEach(d => {
        const smallUnit = d.item?.effective_small_unit || d.item?.small_unit || 'Pcs';
        const isMulti = d.conversion_factor > 1;
        const netPrice = isMulti ? Math.round(d.unit_price / d.conversion_factor) : d.unit_price;
        const ratioBadge = isMulti 
          ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">1 ${d.unit} = ${d.conversion_factor} ${smallUnit}</span>`
          : `<span class="text-slate-400 text-xs">1 : 1</span>`;

        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td class="py-2.5 px-3">
            <span class="font-bold text-slate-800">${d.item_name}</span>
            <span class="text-[10px] text-slate-400 font-mono block">${d.item_code}</span>
          </td>
          <td class="py-2.5 px-3 text-center">
            <span class="font-bold text-slate-900">${d.quantity} ${d.unit}</span>
          </td>
          <td class="py-2.5 px-3 text-center">
            ${ratioBadge}
          </td>
          <td class="py-2.5 px-3 text-center bg-emerald-50/40 border-x border-emerald-100/60">
            <span class="font-mono font-black text-emerald-700 text-xs sm:text-sm">${Number(d.base_quantity).toLocaleString('id-ID')} ${smallUnit}</span>
            ${isMulti ? `<span class="text-[10px] text-emerald-600 block">(${d.quantity} ${d.unit} &times; ${d.conversion_factor})</span>` : ''}
          </td>
          <td class="py-2.5 px-3 text-right font-mono">
            <div class="font-semibold text-slate-800">${d.formatted_unit_price}</div>
            ${isMulti ? `<div class="text-[10px] text-slate-500">(@ Rp ${netPrice.toLocaleString('id-ID')} / ${smallUnit})</div>` : ''}
          </td>
          <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">${d.formatted_subtotal}</td>
        `;
        tbody.appendChild(tr);
      });

      // Kotak Terima Barang
      const receiveBox = document.getElementById('detail-receive-box');
      if (['ordered', 'draft'].includes(p.status)) {
        receiveBox.classList.remove('hidden');
        const receiveBtn = document.getElementById('btn-execute-receive');
        receiveBtn.onclick = () => executeReceiveAction(p.id, p.procurement_number);
        document.getElementById('detail-receive-invoice-input').value = p.invoice_number || '';
      } else {
        receiveBox.classList.add('hidden');
      }

    } catch (e) {
      if (window.showToast) window.showToast('error', 'Gagal mengambil data pengadaan: ' + e.message);
      closeProcurementDetailModal();
    }
  }

  function closeProcurementDetailModal() {
    document.getElementById('modal-procurement-detail').classList.add('hidden');
    currentDetailProcurementId = null;
  }

  function confirmReceiveProcurement(id, poNumber, existingInvoice) {
    if (window.showConfirmDialog) {
      window.showConfirmDialog({
        title: `Konfirmasi Penerimaan ${poNumber}`,
        message: `Apakah barang fisik untuk pesanan ${poNumber} telah tiba di gudang dan diverifikasi? Stok fisik di master inventaris akan otomatis ditambahkan.`,
        confirmText: 'Ya, Terima Barang',
        confirmColor: 'emerald',
        onConfirm: () => executeReceiveAction(id, poNumber, existingInvoice)
      });
    } else {
      if (confirm(`Konfirmasi barang ${poNumber} telah diterima di gudang?`)) {
        executeReceiveAction(id, poNumber, existingInvoice);
      }
    }
  }

  async function executeReceiveAction(id, poNumber, defaultInvoice = '') {
    const inputInvoice = document.getElementById('detail-receive-invoice-input')?.value || defaultInvoice;
    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
      const res = await fetch(`/inventory/procurement/${id}/receive`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ invoice_number: inputInvoice })
      });

      const json = await res.json();
      if (!json.success) throw new Error(json.message || 'Gagal konfirmasi terima');

      if (window.showToast) window.showToast('success', json.message);
      setTimeout(() => {
        window.location.href = `{{ route('inventory.procurement.index') }}?tab=procurement`;
      }, 700);

    } catch (e) {
      if (window.showToast) window.showToast('error', e.message);
    }
  }

  function cancelProcurement(id, poNumber) {
    if (window.showConfirmDialog) {
      window.showConfirmDialog({
        title: `Batalkan Pengadaan ${poNumber}?`,
        message: `Pesanan yang dibatalkan tidak dapat dikembalikan. Lanjutkan pembatalan pesanan ${poNumber}?`,
        confirmText: 'Batalkan Pesanan',
        confirmColor: 'red',
        onConfirm: async () => {
          try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
            const res = await fetch(`/inventory/procurement/${id}/cancel`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
              },
              body: JSON.stringify({ reason: 'Dibatalkan melalui tabel pengadaan' })
            });
            const json = await res.json();
            if (!json.success) throw new Error(json.message);
            if (window.showToast) window.showToast('success', json.message);
            setTimeout(() => window.location.reload(), 600);
          } catch (e) {
            if (window.showToast) window.showToast('error', e.message);
          }
        }
      });
    }
  }

  // ==========================================
  // DIRECT STOCK IN LOGIC
  // ==========================================
  function openDirectStockInModal() {
    document.getElementById('modal-direct-stockin').classList.remove('hidden');
    const tbody = document.getElementById('direct-items-tbody');
    if (tbody.children.length === 0) {
      addDirectItemRow();
    }
  }

  function closeDirectStockInModal() {
    document.getElementById('modal-direct-stockin').classList.add('hidden');
  }

  function addDirectItemRow() {
    const tbody = document.getElementById('direct-items-tbody');
    const idx = directRowIndex++;

    let options = '<option value="">-- Pilih Barang ATK --</option>';
    masterItems.forEach(it => {
      const stock = it.current_stock || 0;
      const smallUnit = it.effective_small_unit || 'Pcs';
      const pkgUnit = it.unit || 'Pcs';
      const conv = it.conversion_rate || 1;
      const convLabel = (conv > 1 && pkgUnit.toLowerCase() !== smallUnit.toLowerCase()) 
        ? ` [1 ${pkgUnit} = ${conv} ${smallUnit}]` 
        : '';
      options += `<option value="${it.id}" data-name="${it.name}" data-code="${it.code}" data-unit="${pkgUnit}" data-small-unit="${smallUnit}" data-conv="${conv}" data-stock="${stock}">${it.name} (${it.code})${convLabel} - Stok: ${stock} ${smallUnit}</option>`;
    });

    const tr = document.createElement('tr');
    tr.id = `direct-row-${idx}`;
    tr.className = 'hover:bg-slate-50/60 transition';
    tr.innerHTML = `
      <td class="py-2.5 px-3">
        <select name="items[${idx}][item_id]" required onchange="onDirectItemSelect(${idx})" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 bg-white font-medium">
          ${options}
        </select>
        <div id="direct-stock-hint-${idx}" class="text-[10px] text-slate-500 font-medium mt-1 hidden"></div>
      </td>
      <td class="py-2.5 px-3">
        <select name="items[${idx}][unit_choice]" id="direct-unit-choice-${idx}" onchange="onDirectUnitChoiceChange(${idx})" class="w-full px-2 py-1.5 rounded-lg border border-slate-200 text-xs bg-white font-semibold">
          <option value="base">Pcs (Satuan)</option>
        </select>
        <input type="hidden" name="items[${idx}][unit]" id="direct-unit-val-${idx}" value="Pcs">
        <input type="hidden" name="items[${idx}][conversion_factor]" id="direct-conv-factor-${idx}" value="1">
        <div id="direct-ratio-badge-${idx}" class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200/80 mt-1 hidden"></div>
      </td>
      <td class="py-2.5 px-3 text-center">
        <input type="number" name="items[${idx}][quantity]" id="direct-qty-${idx}" min="1" value="1" required oninput="onDirectQtyInput(${idx})" class="w-full px-2 py-1.5 rounded-lg border border-slate-200 text-xs font-mono font-bold text-center bg-white" />
        <span id="direct-qty-unit-label-${idx}" class="text-[10px] font-bold text-slate-500 block mt-0.5">Pcs</span>
      </td>
      <td class="py-2.5 px-3 text-center bg-emerald-50/40 border-x border-emerald-100/60">
        <div class="flex flex-col items-center justify-center p-1 rounded-lg">
          <span id="direct-base-qty-${idx}" class="font-mono font-black text-emerald-800 text-xs sm:text-sm">1 Pcs</span>
          <span id="direct-base-calc-${idx}" class="text-[10px] text-emerald-600 font-semibold block mt-0.5">(1 &times; 1)</span>
        </div>
      </td>
      <td class="py-2.5 px-3 text-center">
        <button type="button" onclick="removeDirectItemRow(${idx})" class="p-1 rounded text-slate-300 hover:text-rose-500 hover:bg-rose-50 transition cursor-pointer" title="Hapus Baris">
          <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
      </td>
    `;
    tbody.appendChild(tr);
    calculateDirectGrandTotal();
  }

  function removeDirectItemRow(idx) {
    const tbody = document.getElementById('direct-items-tbody');
    if (tbody.children.length <= 1) {
      if (window.showToast) window.showToast('info', 'Minimal satu item harus diinput.');
      return;
    }
    const row = document.getElementById(`direct-row-${idx}`);
    if (row) row.remove();
    calculateDirectGrandTotal();
  }

  function onDirectItemSelect(idx) {
    const select = document.querySelector(`select[name="items[${idx}][item_id]"]`);
    const opt = select?.selectedOptions[0];
    const stockHint = document.getElementById(`direct-stock-hint-${idx}`);
    if (!opt || !opt.value) {
      if (stockHint) stockHint.classList.add('hidden');
      return;
    }

    const unitPkg = opt.getAttribute('data-unit') || 'Pcs';
    const smallUnit = opt.getAttribute('data-small-unit') || 'Pcs';
    const conv = parseInt(opt.getAttribute('data-conv') || '1', 10);
    const stock = opt.getAttribute('data-stock') || '0';

    if (stockHint) {
      stockHint.textContent = `Stok Gudang: ${Number(stock).toLocaleString('id-ID')} ${smallUnit}`;
      stockHint.classList.remove('hidden');
    }

    const choiceSelect = document.getElementById(`direct-unit-choice-${idx}`);
    choiceSelect.innerHTML = '';

    if (conv > 1 && unitPkg.toLowerCase() !== smallUnit.toLowerCase()) {
      choiceSelect.innerHTML += `<option value="pack" data-unit="${unitPkg}" data-conv="${conv}">📦 ${unitPkg} (Isi ${conv} ${smallUnit})</option>`;
      choiceSelect.innerHTML += `<option value="base" data-unit="${smallUnit}" data-conv="1">🏷️ ${smallUnit} (Satuan Eceran)</option>`;
    } else {
      choiceSelect.innerHTML += `<option value="base" data-unit="${smallUnit}" data-conv="1">🏷️ ${smallUnit} (Satuan Tunggal)</option>`;
    }

    onDirectUnitChoiceChange(idx);
  }

  function onDirectUnitChoiceChange(idx) {
    const choiceSelect = document.getElementById(`direct-unit-choice-${idx}`);
    const opt = choiceSelect?.selectedOptions[0];
    if (!opt) return;

    const unit = opt.getAttribute('data-unit') || 'Pcs';
    const conv = parseInt(opt.getAttribute('data-conv') || '1', 10);

    document.getElementById(`direct-unit-val-${idx}`).value = unit;
    document.getElementById(`direct-conv-factor-${idx}`).value = conv;

    const qtyUnitLabel = document.getElementById(`direct-qty-unit-label-${idx}`);
    if (qtyUnitLabel) qtyUnitLabel.textContent = unit;

    const itemSelect = document.querySelector(`select[name="items[${idx}][item_id]"]`);
    const itemOpt = itemSelect?.selectedOptions[0];
    const unitPkg = itemOpt?.getAttribute('data-unit') || unit;
    const smallUnit = itemOpt?.getAttribute('data-small-unit') || 'Pcs';
    const itemConv = parseInt(itemOpt?.getAttribute('data-conv') || '1', 10);

    const badge = document.getElementById(`direct-ratio-badge-${idx}`);
    if (badge) {
      if (itemConv > 1 && unitPkg.toLowerCase() !== smallUnit.toLowerCase()) {
        badge.innerHTML = `💡 1 ${unitPkg} = ${itemConv} ${smallUnit}`;
        badge.classList.remove('hidden');
      } else {
        badge.classList.add('hidden');
      }
    }

    onDirectQtyInput(idx);
  }

  function onDirectQtyInput(idx) {
    const conv = parseInt(document.getElementById(`direct-conv-factor-${idx}`)?.value || '1', 10);
    const qty = parseInt(document.getElementById(`direct-qty-${idx}`)?.value || '0', 10);
    const unit = document.getElementById(`direct-unit-val-${idx}`)?.value || 'Pcs';

    const itemSelect = document.querySelector(`select[name="items[${idx}][item_id]"]`);
    const itemOpt = itemSelect?.selectedOptions[0];
    const smallUnit = itemOpt?.getAttribute('data-small-unit') || 'Pcs';

    const baseQty = qty * conv;
    const baseQtyEl = document.getElementById(`direct-base-qty-${idx}`);
    const baseCalcEl = document.getElementById(`direct-base-calc-${idx}`);
    if (baseQtyEl) {
      baseQtyEl.textContent = `${Math.round(baseQty).toLocaleString('id-ID')} ${smallUnit}`;
    }
    if (baseCalcEl) {
      if (conv > 1) {
        baseCalcEl.textContent = `(${qty} ${unit} × ${conv} ${smallUnit}/${unit})`;
      } else {
        baseCalcEl.textContent = `(${qty} ${smallUnit})`;
      }
    }

    calculateDirectGrandTotal();
  }

  function calculateDirectGrandTotal() {
    let totalPhysical = 0;
    const tbody = document.getElementById('direct-items-tbody');
    tbody.querySelectorAll('tr').forEach(tr => {
      const idx = tr.id.replace('direct-row-', '');
      const qty = parseInt(document.getElementById(`direct-qty-${idx}`)?.value || '0', 10);
      const conv = parseInt(document.getElementById(`direct-conv-factor-${idx}`)?.value || '1', 10);
      totalPhysical += (qty * conv);
    });

    const label = document.getElementById('label-direct-total-physical');
    if (label) {
      label.textContent = `${totalPhysical.toLocaleString('id-ID')} Fisik Unit Masuk`;
    }
  }

  function submitDirectStockInForm() {
    const form = document.getElementById('form-direct-stockin');
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }
    form.submit();
  }

  // ==========================================
  // TAMBAH REKANAN CEPAT (AJAX)
  // ==========================================
  function openSupplierModal() {
    document.getElementById('modal-supplier-form').classList.remove('hidden');
  }

  function closeSupplierModal() {
    document.getElementById('modal-supplier-form').classList.add('hidden');
  }

  async function submitSupplierForm(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = document.getElementById('btn-submit-supplier');

    submitBtn.disabled = true;
    submitBtn.textContent = 'Menyimpan...';

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
      const res = await fetch("{{ route('inventory.procurement.supplier.store') }}", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: formData
      });

      const json = await res.json();
      if (!json.success) throw new Error(json.message || 'Gagal menyimpan rekanan');

      if (window.showToast) window.showToast('success', json.message);

      // Tambahkan ke dropdown rekanan di form pengadaan jika terbuka
      const sup = json.data;
      const select = document.getElementById('select-procurement-supplier');
      if (select && sup) {
        const newOpt = new Option(sup.name, sup.id, true, true);
        select.add(newOpt);
      }

      form.reset();
      closeSupplierModal();

      // Jika sedang di tab supplier, reload halaman agar tabel terupdate
      const currentTab = new URL(window.location).searchParams.get('tab');
      if (currentTab === 'supplier') {
        setTimeout(() => window.location.reload(), 600);
      }

    } catch (err) {
      if (window.showToast) window.showToast('error', err.message);
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Simpan Rekanan';
    }
  }
</script>
@endpush
@endsection
