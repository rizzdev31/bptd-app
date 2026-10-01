@extends('layouts.app')

@section('title', 'Kasir POS Permintaan & Pengeluaran ATK - BPTD Kelas II Jawa Timur')

@push('styles')
<style>
  .dash-interactive-card {
    transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.28s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.28s ease;
  }
  .dash-interactive-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -5px rgba(11, 35, 65, 0.08), 0 4px 6px -2px rgba(11, 35, 65, 0.03);
  }
  .pos-item-card {
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .pos-item-card:hover {
    transform: translateY(-2px);
    border-color: #93c5fd;
    box-shadow: 0 8px 16px -4px rgba(37, 99, 235, 0.1);
  }
  /* Custom scrollbar for POS cart list */
  .pos-cart-scroll::-webkit-scrollbar {
    width: 5px;
  }
  .pos-cart-scroll::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
  }
  .pos-cart-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }
  .pos-cart-scroll::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }
</style>
@endpush

@section('content')
<main class="main-content flex-1 max-w-430 w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

  <!-- ============================================================== -->
  <!-- HEADER BAR: PERMINTAAN & PENGELUARAN ATK                       -->
  <!-- ============================================================== -->
  <div class="no-print bg-white rounded-2xl border border-slate-200/80 shadow-2xs px-5 py-4 transition anim-fade-in-up">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      
      <!-- Sisi Kiri: Logo Emblem & Identitas -->
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
            Permintaan &amp; Pengeluaran ATK
          </h1>
        </div>
      </div>

      <!-- Sisi Kanan: Tab Switcher (Segmented Control) -->
      <div class="flex items-center bg-slate-100/90 p-1 rounded-xl border border-slate-200/70 shrink-0 self-start sm:self-center">
        <button
          type="button"
          id="btn-tab-pos"
          onclick="switchMainTab('pos')"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all shadow-xs bg-white text-blue-700 border border-slate-200/60"
        >
          <svg class="w-4 h-4 text-blue-600 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span>Kasir POS Distribusi</span>
        </button>

        <button
          type="button"
          id="btn-tab-history"
          onclick="switchMainTab('history')"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all text-slate-600 hover:text-slate-900"
        >
          <svg class="w-4 h-4 text-slate-500 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span>Riwayat &amp; SBPB ({{ $stockOutHistory->total() }})</span>
        </button>
      </div>

    </div>
  </div>

  <!-- ============================================================== -->
  <!-- TAB 1: KASIR POS DISTRIBUSI (INPUT CEPAT TRANSAKSI BARU)        -->
  <!-- ============================================================== -->
  <div id="section-pos" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start anim-fade-in-up anim-delay-50">
    
    <!-- KOLOM KIRI: KATALOG CEPAT BARANG ATK (7 Kolom) -->
    <div class="lg:col-span-7 space-y-4 anim-fade-in-up anim-delay-100">
      
      <!-- Box Search Barcode & Nama -->
      <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs space-y-3">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
            <h3 class="font-bold text-slate-900 text-sm uppercase tracking-tight">Katalog Pilihan Cepat ATK</h3>
          </div>
          <span class="text-xs text-slate-400 font-medium">Klik kartu barang untuk tambah ke keranjang</span>
        </div>

        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
          <input
            type="text"
            id="pos-search-input"
            onkeyup="filterPosItems()"
            placeholder="Scan Barcode atau ketik Nama Barang / Kode ATK..."
            class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white text-xs sm:text-sm font-medium focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition"
            autofocus
          />
          <button
            type="button"
            onclick="clearPosSearch()"
            id="pos-clear-search-btn"
            class="absolute inset-y-0 right-0 pr-3.5 items-center text-slate-400 hover:text-slate-600 hidden"
          >
            &times;
          </button>
        </div>

        <!-- Filter Pill Kategori -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar">
          <button
            type="button"
            onclick="selectCategoryFilter('')"
            class="cat-pill-btn px-3 py-1.5 rounded-lg font-semibold whitespace-nowrap transition bg-blue-600 text-white shadow-2xs"
            data-cat-id=""
          >
            Semua ({{ $posItems->count() }})
          </button>
          @foreach ($categories as $cat)
            <button
              type="button"
              onclick="selectCategoryFilter('{{ $cat->id }}')"
              class="cat-pill-btn px-3 py-1.5 rounded-lg font-semibold whitespace-nowrap transition bg-slate-100 text-slate-600 hover:bg-slate-200"
              data-cat-id="{{ $cat->id }}"
            >
              {{ $cat->name }}
            </button>
          @endforeach
        </div>
      </div>

      <!-- Grid Kartu Barang ATK -->
      <div id="pos-items-grid" class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 max-h-155 overflow-y-auto pr-1">
        @forelse ($posItems as $item)
          <div
            class="pos-item-card bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between cursor-pointer select-none {{ $item->current_stock <= 0 ? 'opacity-55 cursor-not-allowed' : '' }}"
            data-item-id="{{ $item->id }}"
            data-item-code="{{ $item->code }}"
            data-item-barcode="{{ $item->barcode ?? '' }}"
            data-item-name="{{ $item->name }}"
            data-item-stock="{{ $item->current_stock }}"
            data-item-unit="{{ $item->unit ?? 'Pcs' }}"
            data-item-small-unit="{{ $item->effective_small_unit }}"
            data-item-conversion="{{ $item->conversion_rate ?? 1 }}"
            data-item-has-multi="{{ $item->has_multi_unit ? '1' : '0' }}"
            data-item-cat="{{ $item->category_id }}"
            onclick="handleItemCardClick({{ $item->id }})"
          >
            <div>
              <div class="flex items-start justify-between gap-2">
                <span class="inline-block px-2 py-0.5 rounded font-mono text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                  {{ $item->code }}
                </span>
                
                @if ($item->current_stock > $item->minimum_stock)
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200" title="Total fisik: {{ number_format($item->current_stock) }} {{ $item->effective_small_unit }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    {{ $item->formatted_stock }}
                  </span>
                @elseif ($item->current_stock > 0)
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200" title="Total fisik: {{ number_format($item->current_stock) }} {{ $item->effective_small_unit }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                    {{ $item->formatted_stock }} (Menipis)
                  </span>
                @else
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                    Stok Habis
                  </span>
                @endif
              </div>

              <h4 class="font-bold text-slate-900 text-sm mt-2 line-clamp-2 leading-snug">
                {{ $item->name }}
              </h4>

              <div class="flex flex-wrap items-center gap-1.5 mt-1.5 text-xs text-slate-500">
                <span class="truncate">{{ $item->category?->name ?? 'Kategori Umum' }}</span>
                @if ($item->has_multi_unit)
                  <span>&bull;</span>
                  <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/60 font-mono">
                    1 {{ $item->unit }} = {{ $item->conversion_rate }} {{ $item->effective_small_unit }}
                  </span>
                @endif
                @if ($item->storage_location)
                  <span>&bull;</span>
                  <span class="truncate font-mono">{{ $item->storage_location }}</span>
                @endif
              </div>
            </div>

            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2">
              <span class="text-xs text-slate-400 font-mono truncate" title="{{ $item->barcode ?: '-' }}">
                SKU: {{ $item->barcode ?: '-' }}
              </span>

              @if ($item->current_stock > 0)
                <button
                  type="button"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs transition shadow-xs shadow-blue-500/20 shrink-0"
                >
                  <svg class="w-3.5 h-3.5 stroke-[2.2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                  </svg>
                  <span>Ambil</span>
                </button>
              @else
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold text-rose-600 bg-rose-50 border border-rose-200 shrink-0">
                  Stok Habis
                </span>
              @endif
            </div>
          </div>
        @empty
          <div class="col-span-2 py-12 text-center text-slate-400 bg-white rounded-2xl border border-dashed border-slate-200">
            Belum ada data barang ATK terdaftar.
          </div>
        @endforelse
      </div>
    </div>

    <!-- KOLOM KANAN: KASIR CHECKOUT REGISTER (5 Kolom) -->
    <div class="lg:col-span-5 sticky top-6 space-y-4 anim-fade-in-up anim-delay-150">
      
      <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden flex flex-col justify-between">
        
        <!-- Header Register Kasir -->
        <div class="bg-linear-to-r from-[#0f2e54] to-[#0b2341] p-4 text-white border-b border-blue-900/60">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center border border-white/20 text-amber-400 shrink-0 shadow-2xs">
                <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
              <div>
                <h3 class="font-black text-sm uppercase tracking-wide text-white leading-tight">Panel Kasir Permintaan</h3>
                <div class="flex items-center gap-1.5 mt-0.5">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                  <p class="text-[11px] text-amber-300 font-mono font-bold tracking-tight">{{ $nextTransactionNumber }}</p>
                </div>
              </div>
            </div>

            <div class="text-right">
              <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-white/15 text-slate-100 border border-white/20">
                {{ date('d M Y') }}
              </span>
              <p class="text-[11px] text-slate-300 mt-1 font-semibold truncate max-w-36">
                {{ Auth::user()?->name ?? 'Petugas' }}
              </p>
            </div>
          </div>
        </div>

        <!-- Form Transaksi POS -->
        <form id="form-pos-checkout" onsubmit="handlePosCheckoutSubmit(event)" class="p-4 sm:p-5 space-y-4">
          @csrf
          <input type="hidden" name="transaction_date" value="{{ date('Y-m-d') }}" />

          <!-- Bagian I: Data Penerima ATK -->
          <div class="space-y-2.5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-1.5">
              <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                <span class="w-1.5 h-3.5 rounded-full bg-blue-600"></span>
                Identitas Penerima ATK
              </label>

              <button
                type="button"
                id="btn-toggle-recipient"
                onclick="toggleManualRecipient()"
                class="text-xs font-semibold text-blue-600 hover:text-blue-800 underline cursor-pointer"
              >
                Penerima Manual / Tamu
              </button>
            </div>

            <!-- Mode 1: Pilih Pegawai Terdaftar -->
            <div id="container-select-recipient" class="space-y-1.5">
              <select
                id="pos-recipient-select"
                name="recipient_id"
                onchange="handleRecipientChange(this)"
                class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition font-medium"
              >
                <option value="">-- Pilih Pegawai BPTD Pemohon ATK --</option>
                @foreach ($recipients as $rec)
                  <option
                    value="{{ $rec->id }}"
                    data-name="{{ $rec->name }}"
                    data-nip="{{ $rec->nip }}"
                    data-unit="{{ $rec->workUnit?->name ?? 'Unit Belum Diatur' }}"
                    data-position="{{ $rec->position ?? '-' }}"
                  >
                    {{ $rec->name }} ({{ $rec->workUnit?->name ?? 'BPTD' }})
                  </option>
                @endforeach
              </select>

              <!-- Preview Card Pegawai Terpilih -->
              <div id="recipient-preview-card" class="hidden p-2.5 rounded-xl bg-blue-50/70 border border-blue-200/80 text-xs text-slate-700 space-y-1">
                <div class="flex items-center justify-between">
                  <span class="font-bold text-blue-900" id="prev-rec-name">-</span>
                  <span class="font-mono text-slate-500" id="prev-rec-nip">-</span>
                </div>
                <div class="text-slate-600 flex items-center gap-1">
                  <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                  <span id="prev-rec-unit" class="truncate">-</span>
                </div>
              </div>
            </div>

            <!-- Mode 2: Input Manual Penerima Luar / Tamu -->
            <div id="container-manual-recipient" class="hidden space-y-2">
              <div>
                <input
                  type="text"
                  id="manual-recipient-name"
                  name="recipient_name"
                  placeholder="Nama Lengkap Penerima ATK..."
                  class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-300 bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 font-medium"
                />
              </div>
              <div class="grid grid-cols-2 gap-2">
                <div>
                  <input
                    type="text"
                    name="recipient_nip"
                    placeholder="NIP / No. Identitas..."
                    class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-300 bg-white font-mono"
                  />
                </div>
                <div>
                  <input
                    type="text"
                    name="recipient_unit"
                    placeholder="Unit / Asal Instansi..."
                    class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-300 bg-white font-medium"
                  />
                </div>
              </div>
            </div>

            <!-- Keperluan / Catatan Permintaan -->
            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Keperluan / Catatan Dinas</label>
              <input
                type="text"
                name="notes"
                placeholder="Contoh: Operasional Harian Loket Pelayanan / Rapat Subbag..."
                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 font-medium"
              />
            </div>
          </div>

          <!-- Bagian II: Keranjang Barang Diminta (POS Cart) -->
          <div class="space-y-2 pt-2 border-t border-slate-100">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                <span class="w-1.5 h-3.5 rounded-full bg-blue-600"></span>
                Daftar Permintaan ATK
              </span>
              <button
                type="button"
                onclick="clearCart()"
                class="text-xs font-semibold text-rose-600 hover:text-rose-700 underline cursor-pointer"
              >
                Kosongkan
              </button>
            </div>

            <!-- Empty Cart Notice -->
            <div id="cart-empty-state" class="py-8 text-center text-slate-400 border border-dashed border-slate-200 rounded-xl space-y-1">
              <svg class="w-8 h-8 mx-auto text-slate-300 stroke-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
              <p class="text-xs font-semibold text-slate-500">Keranjang Masih Kosong</p>
              <p class="text-xs text-slate-400">Pilih barang dari katalog di samping</p>
            </div>

            <!-- Container Item Keranjang -->
            <div id="cart-items-container" class="space-y-2 max-h-72 overflow-y-auto pos-cart-scroll pr-1 hidden">
              <!-- Rendered via JavaScript -->
            </div>
          </div>

          <!-- Bagian III: Ringkasan Total & Tombol Proses -->
          <div class="pt-3 border-t border-slate-200 space-y-3">
            <div class="bg-linear-to-br from-slate-50 via-blue-50/40 to-slate-50 p-3.5 rounded-2xl border border-blue-100 flex items-center justify-between text-xs shadow-2xs">
              <div>
                <span class="text-slate-500 font-semibold uppercase text-[10px] tracking-wider block">Total Jenis ATK</span>
                <span class="font-black text-slate-900 text-sm mt-0.5 block" id="cart-distinct-count">0 Jenis</span>
              </div>
              <div class="text-right">
                <span class="text-slate-500 font-semibold uppercase text-[10px] tracking-wider block">Total Fisik Keluar</span>
                <span class="font-black text-blue-700 text-base sm:text-lg tracking-tight block" id="cart-total-qty">0 Unit</span>
              </div>
            </div>

            <!-- Tombol Submit Checkout -->
            <button
              type="submit"
              id="btn-process-checkout"
              disabled
              class="w-full h-12 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:pointer-events-none text-white font-bold text-xs sm:text-sm shadow-md shadow-blue-500/25 flex items-center justify-center gap-2 transition cursor-pointer"
            >
              <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              <span>Simpan Pengeluaran Barang</span>
            </button>
          </div>

        </form>

      </div>

    </div>

  </div>

  <!-- ============================================================== -->
  <!-- TAB 2: BUKU INDUK RIWAYAT PENGELUARAN & SBPB                   -->
  <!-- ============================================================== -->
  <div id="section-history" class="hidden space-y-5">
    
    <!-- Metric Quick Cards (Ringkasan Pengeluaran Hari Ini & Stok) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Card 1: Pengeluaran Hari Ini -->
      <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up anim-delay-50">
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div>
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Transaksi Hari Ini</span>
          <span class="text-lg font-black text-slate-900 tracking-tight">{{ number_format($todayTransactionsCount) }} Transaksi</span>
        </div>
      </div>

      <!-- Card 2: Total Fisik Unit Hari Ini -->
      <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up anim-delay-100">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <div>
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Fisik Didistribusikan</span>
          <span class="text-lg font-black text-emerald-700 tracking-tight">{{ number_format($todayQuantityTotal) }} Unit Fisik</span>
        </div>
      </div>

      <!-- Card 3: Katalog Aktif Siap Distribusi -->
      <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up anim-delay-150">
        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
          </svg>
        </div>
        <div>
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Item Stok Siap Kirim</span>
          <span class="text-lg font-black text-indigo-900 tracking-tight">{{ number_format($readyItemsCount) }} Varian ATK</span>
        </div>
      </div>

      <!-- Card 4: No. Bukti Antrean Transaksi -->
      <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up anim-delay-200">
        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
          </svg>
        </div>
        <div>
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">No. Bukti Antrean</span>
          <span class="text-base font-extrabold text-amber-800 tracking-tight">{{ $nextTransactionNumber }}</span>
        </div>
      </div>
    </div>
    
    <!-- Filter Riwayat Console -->
    <div class="no-print bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-3 anim-fade-in-up anim-delay-250">
      <form method="GET" action="{{ route('inventory.stock-out.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
        <input type="hidden" name="tab" value="history" />

        <!-- Search Keyword -->
        <div class="lg:col-span-4 relative">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
          <input
            type="text"
            name="history_keyword"
            value="{{ request('history_keyword') }}"
            placeholder="Cari No. Bukti, Nama Penerima, atau NIP..."
            class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition font-medium"
          />
        </div>

        <!-- Tanggal Mulai -->
        <div class="lg:col-span-2">
          <input
            type="date"
            name="date_from"
            value="{{ request('date_from') }}"
            class="w-full px-3 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden font-medium text-slate-700"
            title="Dari Tanggal"
          />
        </div>

        <!-- Tanggal Selesai -->
        <div class="lg:col-span-2">
          <input
            type="date"
            name="date_to"
            value="{{ request('date_to') }}"
            class="w-full px-3 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden font-medium text-slate-700"
            title="Sampai Tanggal"
          />
        </div>

        <!-- Filter Pegawai -->
        <div class="lg:col-span-2">
          <select
            name="filter_recipient_id"
            class="w-full px-3 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-300 bg-slate-50/50 focus:bg-white focus:outline-hidden font-medium text-slate-700"
          >
            <option value="">Semua Pegawai</option>
            @foreach ($recipients as $rec)
              <option value="{{ $rec->id }}" {{ request('filter_recipient_id') == $rec->id ? 'selected' : '' }}>
                {{ $rec->name }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Tombol Filter -->
        <div class="lg:col-span-2 flex items-center gap-2">
          <button
            type="submit"
            class="flex-1 py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs tracking-wider uppercase transition text-center shadow-xs"
          >
            Terapkan
          </button>
          @if (request()->hasAny(['history_keyword', 'date_from', 'date_to', 'filter_recipient_id']))
            <a
              href="{{ route('inventory.stock-out.index', ['tab' => 'history']) }}"
              class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition text-center"
              title="Reset Filter"
            >
              Reset
            </a>
          @endif
        </div>
      </form>
    </div>

    <!-- Tabel Riwayat SBPB -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden anim-fade-in-up anim-delay-300">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs sm:text-sm text-slate-600">
          <thead class="bg-slate-50/80 text-slate-800 font-bold border-b border-slate-200 text-xs uppercase tracking-wider">
            <tr>
              <th class="py-4 px-4 sm:px-5 w-14 text-center">No</th>
              <th class="py-4 px-4">No. Bukti / SBPB</th>
              <th class="py-4 px-4">Tanggal</th>
              <th class="py-4 px-4">Pegawai Penerima</th>
              <th class="py-4 px-4">Unit Kerja</th>
              <th class="py-4 px-4 text-center">Jumlah Item</th>
              <th class="py-4 px-4 text-center">Total Fisik</th>
              <th class="py-4 px-4">Petugas</th>
              <th class="py-4 px-4 text-center w-28">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse ($stockOutHistory as $index => $tx)
              <tr class="hover:bg-blue-50/40 transition-colors">
                <td class="py-3.5 px-4 text-center font-semibold text-slate-400">
                  {{ $stockOutHistory->firstItem() + $index }}
                </td>
                <td class="py-3.5 px-4">
                  <span class="font-bold text-blue-700 tracking-tight block">
                    {{ $tx->transaction_number }}
                  </span>
                  @if ($tx->notes)
                    <span class="text-xs text-slate-400 truncate max-w-xs block" title="{{ $tx->notes }}">
                      {{ $tx->notes }}
                    </span>
                  @endif
                </td>
                <td class="py-3.5 px-4 whitespace-nowrap text-slate-700 font-medium">
                  {{ $tx->transaction_date->format('d/m/Y') }}
                </td>
                <td class="py-3.5 px-4">
                  <span class="font-bold text-slate-900 block leading-tight">
                    {{ $tx->recipient_name }}
                  </span>
                  @if ($tx->recipient_nip)
                    <span class="text-xs text-slate-500 font-medium">NIP: {{ $tx->recipient_nip }}</span>
                  @endif
                </td>
                <td class="py-3.5 px-4">
                  <span class="inline-block px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-xs">
                    {{ $tx->recipient_unit ?: '-' }}
                  </span>
                </td>
                <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                  {{ $tx->total_items }} Varian
                </td>
                <td class="py-3.5 px-4 text-center font-bold text-emerald-700">
                  {{ number_format($tx->total_quantity) }} Unit Fisik
                </td>
                <td class="py-3.5 px-4 text-slate-600 text-xs">
                  {{ $tx->user?->name ?? 'Petugas' }}
                </td>
                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                  <div class="inline-flex items-center gap-1.5">
                    <!-- Tombol Detail Modal -->
                    <button
                      type="button"
                      onclick="openTxDetailModal({{ $tx->id }})"
                      class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 border border-transparent hover:scale-110 active:scale-95 transition"
                      title="Lihat Rincian Barang"
                    >
                      <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>

                    <!-- Tombol Cetak SBPB -->
                    <a
                      href="{{ route('inventory.stock-out.print', $tx) }}"
                      target="_blank"
                      class="p-1.5 rounded-lg text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 border border-transparent hover:scale-110 active:scale-95 transition"
                      title="Cetak Surat Bukti Pengeluaran Barang (SBPB)"
                    >
                      <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                      </svg>
                    </a>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="py-12 text-center text-slate-400">
                  Belum ada riwayat transaksi pengeluaran ATK.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      @if ($stockOutHistory->hasPages())
        <div class="p-4 border-t border-slate-200">
          {{ $stockOutHistory->links() }}
        </div>
      @endif
    </div>

  </div>

</main>

<!-- ============================================================== -->
<!-- MODAL DETAIL TRANSAKSI RIWAYAT SBPB                            -->
<!-- ============================================================== -->
<div
  id="modal-tx-detail"
  class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs items-center justify-center p-4 transition-all duration-300 hidden"
  onclick="if(event.target === this) closeTxDetailModal()"
>
  <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-2xl w-full overflow-hidden animate-scale-in flex flex-col max-h-[90vh]">
    <!-- Header Modal -->
    <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
      <div>
        <span class="text-xs font-bold text-blue-700 uppercase tracking-wider block">DETAIL SURAT BUKTI PENGELUARAN BARANG</span>
        <h3 class="text-base sm:text-lg font-black text-slate-900" id="detail-modal-tx-number">-</h3>
      </div>
      <button
        type="button"
        onclick="closeTxDetailModal()"
        class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 flex items-center justify-center text-xl transition"
      >
        &times;
      </button>
    </div>

    <!-- Body Modal -->
    <div class="p-5 overflow-y-auto space-y-4 text-xs sm:text-sm">
      <!-- Info Ringkas Penerima -->
      <div class="grid grid-cols-2 gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
        <div>
          <span class="text-slate-400 block text-[11px]">Penerima Barang:</span>
          <span class="font-bold text-slate-900" id="detail-modal-recipient-name">-</span>
          <span class="text-slate-500 block font-medium" id="detail-modal-recipient-nip">-</span>
        </div>
        <div>
          <span class="text-slate-400 block text-[11px]">Unit Kerja / Seksi:</span>
          <span class="font-semibold text-slate-800" id="detail-modal-recipient-unit">-</span>
          <span class="text-slate-500 block" id="detail-modal-date">-</span>
        </div>
      </div>

      <!-- Tabel Rincian Item Barang -->
      <div class="border border-slate-200 rounded-xl overflow-hidden">
        <table class="w-full text-left border-collapse text-xs">
          <thead class="bg-slate-100/80 font-bold text-slate-700 border-b border-slate-200">
            <tr>
              <th class="p-2.5 text-center w-10">No</th>
              <th class="p-2.5">Nama &amp; Kode ATK</th>
              <th class="p-2.5 text-center w-20">Jumlah</th>
              <th class="p-2.5 text-center w-24">Satuan</th>
              <th class="p-2.5 text-center w-28">Sisa Stok Fisik</th>
            </tr>
          </thead>
          <tbody id="detail-modal-items-tbody" class="divide-y divide-slate-100">
            <!-- Rendered via JS -->
          </tbody>
        </table>
      </div>
    </div>

    <!-- Footer Modal -->
    <div class="p-4 border-t border-slate-100 flex items-center justify-between bg-slate-50">
      <button
        type="button"
        onclick="closeTxDetailModal()"
        class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 font-bold text-slate-700 text-xs transition"
      >
        Tutup
      </button>

      <a
        id="detail-modal-print-btn"
        href="#"
        target="_blank"
        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition shadow-xs"
      >
        <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        <span>Cetak SBPB</span>
      </a>
    </div>
  </div>
</div>

<!-- ============================================================== -->
<!-- MODAL DIALOG: SUKSES PENGELUARAN (CETAK SBPB OPSIONAL)         -->
<!-- ============================================================== -->
<div
  id="modal-tx-success"
  class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4 transition-all duration-200"
>
  <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 p-6 text-center space-y-5 animate-scale-in">
    <!-- Icon Success -->
    <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200/80 flex items-center justify-center">
      <svg class="w-7 h-7 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
      </svg>
    </div>

    <!-- Teks Header -->
    <div class="space-y-1">
      <h3 class="text-lg font-black text-slate-900">Pengeluaran Barang Berhasil!</h3>
      <p class="text-xs text-slate-500 font-medium">Transaksi telah tercatat dan stok fisik barang telah dipotong otomatis.</p>
    </div>

    <!-- Ringkasan Info Transaksi -->
    <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 text-left text-xs space-y-2">
      <div class="flex items-center justify-between border-b border-slate-200/60 pb-1.5">
        <span class="text-slate-500 font-medium">No. Bukti Transaksi:</span>
        <span class="font-bold text-blue-700 tracking-tight" id="success-modal-tx-number">-</span>
      </div>
      <div class="flex items-center justify-between">
        <span class="text-slate-500 font-medium">Penerima Barang:</span>
        <span class="font-bold text-slate-800" id="success-modal-recipient">-</span>
      </div>
      <div class="flex items-center justify-between">
        <span class="text-slate-500 font-medium">Total Fisik Dikeluarkan:</span>
        <span class="font-bold text-emerald-700" id="success-modal-total-qty">-</span>
      </div>
    </div>

    <!-- Pilihan Aksi: Cetak SBPB (Opsional) vs Transaksi Baru (POS) -->
    <div class="space-y-2.5 pt-1">
      <!-- Tombol Transaksi Baru (Tetap di Kasir POS) -->
      <button
        type="button"
        onclick="closeSuccessModalAndResetPos()"
        class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-bold text-xs sm:text-sm shadow-md shadow-blue-500/20 flex items-center justify-center gap-2 transition cursor-pointer"
      >
        <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        <span>Selesai &amp; Transaksi Baru</span>
      </button>

      <!-- Tombol Cetak SBPB: Hanya jika staf/petugas memang butuh bukti fisik cetak -->
      <a
        id="success-modal-print-btn"
        href="#"
        target="_blank"
        class="w-full py-2.5 px-4 rounded-xl border border-slate-200 bg-slate-50/80 hover:bg-slate-100 hover:border-slate-300 text-slate-700 font-bold text-xs sm:text-sm flex items-center justify-center gap-2 transition shadow-2xs"
      >
        <svg class="w-4 h-4 text-slate-500 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
        <span>Cetak Lembar SBPB (Opsional)</span>
      </a>
    </div>

    <!-- Link ke Riwayat Transaksi -->
    <div class="pt-1">
      <button
        type="button"
        onclick="closeSuccessModalAndGoHistory()"
        class="text-xs font-semibold text-slate-400 hover:text-slate-600 transition underline cursor-pointer"
      >
        Buka Buku Induk Riwayat &amp; SBPB
      </button>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // State Keranjang Kasir POS Multi-Satuan
  let cart = {};
  let currentCategoryFilter = '';

  // Tab Switching dengan Transisi Halus
  function switchMainTab(tab) {
    const secPos = document.getElementById('section-pos');
    const secHistory = document.getElementById('section-history');
    const btnPos = document.getElementById('btn-tab-pos');
    const btnHistory = document.getElementById('btn-tab-history');

    if (tab === 'history') {
      secPos.classList.add('hidden');
      secHistory.classList.remove('hidden');

      // Animasi re-trigger saat membuka tab riwayat
      secHistory.classList.remove('anim-fade-in-up');
      void secHistory.offsetWidth;
      secHistory.classList.add('anim-fade-in-up');

      btnHistory.className = 'inline-flex items-center gap-2 px-4 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all shadow-xs bg-white text-blue-700 border border-slate-200/60';
      btnPos.className = 'inline-flex items-center gap-2 px-4 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all text-slate-600 hover:text-slate-900';
    } else {
      secHistory.classList.add('hidden');
      secPos.classList.remove('hidden');

      // Animasi re-trigger saat membuka tab POS
      secPos.classList.remove('anim-fade-in-up');
      void secPos.offsetWidth;
      secPos.classList.add('anim-fade-in-up');

      btnPos.className = 'inline-flex items-center gap-2 px-4 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all shadow-xs bg-white text-blue-700 border border-slate-200/60';
      btnHistory.className = 'inline-flex items-center gap-2 px-4 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all text-slate-600 hover:text-slate-900';
    }
  }

  @if(request('tab') === 'history')
    document.addEventListener('DOMContentLoaded', () => {
      switchMainTab('history');
    });
  @endif

  // Toggle Penerima Pegawai vs Manual
  function toggleManualRecipient() {
    const contSelect = document.getElementById('container-select-recipient');
    const contManual = document.getElementById('container-manual-recipient');
    const btnToggle = document.getElementById('btn-toggle-recipient');
    const recSelect = document.getElementById('pos-recipient-select');

    if (contManual.classList.contains('hidden')) {
      contManual.classList.remove('hidden');
      contSelect.classList.add('hidden');
      recSelect.value = '';
      document.getElementById('recipient-preview-card').classList.add('hidden');
      btnToggle.textContent = 'Pilih Pegawai Terdaftar';
      if (window.showToast) {
        window.showToast('Mode input manual penerima aktif.', 'info');
      }
    } else {
      contManual.classList.add('hidden');
      contSelect.classList.remove('hidden');
      document.getElementById('manual-recipient-name').value = '';
      btnToggle.textContent = 'Penerima Manual / Tamu';
      if (window.showToast) {
        window.showToast('Mode pilih pegawai terdaftar aktif.', 'info');
      }
    }
    updateCheckoutButtonState();
  }

  // Handle Event Saat Pegawai Dipilih
  function handleRecipientChange(selectEl) {
    const opt = selectEl.options[selectEl.selectedIndex];
    const preview = document.getElementById('recipient-preview-card');

    if (selectEl.value && opt) {
      document.getElementById('prev-rec-name').textContent = opt.dataset.name || '-';
      document.getElementById('prev-rec-nip').textContent = opt.dataset.nip ? `NIP: ${opt.dataset.nip}` : 'Non-PNS';
      document.getElementById('prev-rec-unit').textContent = opt.dataset.unit || '-';
      preview.classList.remove('hidden');
      if (window.showToast) {
        window.showToast(`Penerima dipilih: ${opt.dataset.name}`, 'info');
      }
    } else {
      preview.classList.add('hidden');
    }
    updateCheckoutButtonState();
  }

  // Filter Kategori Pills
  function selectCategoryFilter(catId) {
    currentCategoryFilter = catId;
    document.querySelectorAll('.cat-pill-btn').forEach(btn => {
      if (btn.dataset.catId === catId) {
        btn.className = 'cat-pill-btn px-3 py-1.5 rounded-lg font-semibold whitespace-nowrap transition bg-blue-600 text-white shadow-2xs';
      } else {
        btn.className = 'cat-pill-btn px-3 py-1.5 rounded-lg font-semibold whitespace-nowrap transition bg-slate-100 text-slate-600 hover:bg-slate-200';
      }
    });
    filterPosItems();
  }

  // Filter Pencarian Katalog Cepat
  function filterPosItems() {
    const input = document.getElementById('pos-search-input');
    const clearBtn = document.getElementById('pos-clear-search-btn');
    const query = input.value.toLowerCase().trim();

    if (query.length > 0) {
      clearBtn.classList.remove('hidden');
      clearBtn.classList.add('flex');
    } else {
      clearBtn.classList.add('hidden');
      clearBtn.classList.remove('flex');
    }

    const cards = document.querySelectorAll('.pos-item-card');
    cards.forEach(card => {
      const name = (card.dataset.itemName || '').toLowerCase();
      const code = (card.dataset.itemCode || '').toLowerCase();
      const barcode = (card.dataset.itemBarcode || '').toLowerCase();
      const catId = card.dataset.itemCat || '';

      const matchesSearch = query === '' || name.includes(query) || code.includes(query) || barcode.includes(query);
      const matchesCat = currentCategoryFilter === '' || catId === currentCategoryFilter;

      if (matchesSearch && matchesCat) {
        card.classList.remove('hidden');
      } else {
        card.classList.add('hidden');
      }
    });
  }

  function clearPosSearch() {
    const input = document.getElementById('pos-search-input');
    input.value = '';
    filterPosItems();
    input.focus();
  }

  // Handle Klik Kartu Barang untuk Menambahkan ke Keranjang
  function handleItemCardClick(itemId) {
    const card = document.querySelector(`.pos-item-card[data-item-id="${itemId}"]`);
    if (!card) return;

    const stock = parseInt(card.dataset.itemStock, 10);
    if (stock <= 0) {
      if (window.showToast) {
        window.showToast('Stok barang ini telah habis dan tidak dapat dikeluarkan.', 'warning');
      } else {
        alert('Stok barang ini telah habis.');
      }
      return;
    }

    const primaryUnit = card.dataset.itemUnit || 'Pcs';
    const smallUnit = card.dataset.itemSmallUnit || 'Pcs';
    const conversionRate = parseInt(card.dataset.itemConversion, 10) || 1;
    const hasMultiUnit = card.dataset.itemHasMulti === '1';

    if (!cart[itemId]) {
      // Default: Jika barang bertingkat (misal pulpen lusinan), default ambil eceran (Pcs) sesuai kebutuhan staf
      const defaultUnit = hasMultiUnit ? smallUnit : primaryUnit;

      cart[itemId] = {
        id: itemId,
        code: card.dataset.itemCode,
        name: card.dataset.itemName,
        stock: stock, // Stok fisik dasar (dalam smallUnit / Pcs)
        primaryUnit: primaryUnit,
        smallUnit: smallUnit,
        conversionRate: conversionRate,
        hasMultiUnit: hasMultiUnit,
        selectedUnit: defaultUnit,
        qty: 1
      };

      if (window.showToast) {
        window.showToast(`${card.dataset.itemName} ditambahkan ke keranjang.`, 'success');
      }
    } else {
      // Hitung batas maksimal stok berdasarkan satuan yang sedang aktif
      const item = cart[itemId];
      const maxAllowed = getMaxAllowedQty(item);

      if (item.qty < maxAllowed) {
        item.qty += 1;
        if (window.showToast) {
          window.showToast(`Kuantitas ${item.name} bertambah jadi ${item.qty} ${item.selectedUnit}.`, 'info');
        }
      } else {
        if (window.showToast) {
          window.showToast(`Maksimal stok tersedia hanya ${maxAllowed} ${item.selectedUnit}.`, 'warning');
        }
        return;
      }
    }

    renderCart();
  }

  // Hitung jumlah kuantitas maksimum yang diizinkan untuk satuan tertentu
  function getMaxAllowedQty(item) {
    if (item.selectedUnit === item.primaryUnit && item.hasMultiUnit) {
      return Math.floor(item.stock / item.conversionRate);
    }
    return item.stock; // Dalam satuan terkecil / eceran
  }

  // Ubah Satuan Distribusi Barang (Pcs vs Lusin / Box / Pack)
  function setCartItemUnit(itemId, unit) {
    if (!cart[itemId]) return;
    const item = cart[itemId];
    if (item.selectedUnit === unit) return;

    item.selectedUnit = unit;
    const maxAllowed = getMaxAllowedQty(item);

    if (maxAllowed <= 0) {
      if (window.showToast) {
        window.showToast(`Stok fisik barang tidak mencukupi untuk 1 ${unit} penuh (tersedia ${item.stock} ${item.smallUnit}). Beralih ke satuan eceran.`, 'warning');
      }
      item.selectedUnit = item.smallUnit;
      renderCart();
      return;
    }

    if (item.qty > maxAllowed) {
      item.qty = Math.max(1, maxAllowed);
    }

    if (window.showToast) {
      window.showToast(`Satuan ${item.name} diubah ke ${unit}.`, 'info');
    }

    renderCart();
  }

  // Ubah Jumlah Barang di Keranjang (Stepper)
  function updateCartQty(itemId, delta) {
    if (!cart[itemId]) return;
    const item = cart[itemId];
    const maxAllowed = getMaxAllowedQty(item);
    const newQty = item.qty + delta;

    if (newQty <= 0) {
      const removedName = item.name;
      delete cart[itemId];
      if (window.showToast) {
        window.showToast(`${removedName} dihapus dari keranjang.`, 'info');
      }
    } else if (newQty > maxAllowed) {
      if (window.showToast) {
        window.showToast(`Jumlah melebihi stok yang tersedia (${maxAllowed} ${item.selectedUnit}).`, 'warning');
      }
    } else {
      item.qty = newQty;
    }

    renderCart();
  }

  function setCartQtyDirect(itemId, value) {
    if (!cart[itemId]) return;
    const item = cart[itemId];
    const maxAllowed = getMaxAllowedQty(item);
    let qty = parseInt(value, 10);

    if (isNaN(qty) || qty <= 0) {
      const removedName = item.name;
      delete cart[itemId];
      if (window.showToast) {
        window.showToast(`${removedName} dihapus dari keranjang.`, 'info');
      }
    } else if (qty > maxAllowed) {
      item.qty = Math.max(1, maxAllowed);
      if (window.showToast) {
        window.showToast(`Maksimal stok tersedia adalah ${maxAllowed} ${item.selectedUnit}.`, 'warning');
      }
    } else {
      item.qty = qty;
    }
    renderCart();
  }

  function removeFromCart(itemId) {
    const item = cart[itemId];
    delete cart[itemId];
    renderCart();
    if (window.showToast && item) {
      window.showToast(`${item.name} dihapus dari keranjang.`, 'info');
    }
  }

  function clearCart() {
    const count = Object.keys(cart).length;
    cart = {};
    renderCart();
    if (window.showToast && count > 0) {
      window.showToast('Keranjang permintaan dikosongkan.', 'info');
    }
  }

  // Render Tampilan Keranjang POS
  function renderCart() {
    const emptyState = document.getElementById('cart-empty-state');
    const container = document.getElementById('cart-items-container');
    const distinctEl = document.getElementById('cart-distinct-count');
    const totalQtyEl = document.getElementById('cart-total-qty');

    const itemIds = Object.keys(cart);

    if (itemIds.length === 0) {
      emptyState.classList.remove('hidden');
      container.classList.add('hidden');
      container.innerHTML = '';
      distinctEl.textContent = '0 Jenis';
      totalQtyEl.textContent = '0 Unit';
      updateCheckoutButtonState();
      return;
    }

    emptyState.classList.add('hidden');
    container.classList.remove('hidden');

    let totalDistinct = 0;
    let totalPhysicalUnits = 0;
    let html = '';

    itemIds.forEach((id, idx) => {
      const item = cart[id];
      const maxAllowed = getMaxAllowedQty(item);
      const isPackaging = item.selectedUnit === item.primaryUnit && item.hasMultiUnit;
      const physicalPieces = isPackaging ? (item.qty * item.conversionRate) : item.qty;

      totalDistinct++;
      totalPhysicalUnits += physicalPieces;

      // Unit Switcher Tabs (jika item memiliki satuan bertingkat, misal Lusin -> Pcs)
      let unitSwitcherHtml = '';
      if (item.hasMultiUnit) {
        const canTakeFullPack = Math.floor(item.stock / item.conversionRate) > 0;

        unitSwitcherHtml = `
          <div class="inline-flex items-center p-0.5 rounded-lg bg-slate-100 border border-slate-200 text-[10.5px] font-bold mt-1">
            <button
              type="button"
              onclick="setCartItemUnit(${item.id}, '${item.smallUnit}')"
              class="px-2 py-0.5 rounded-md transition ${item.selectedUnit === item.smallUnit ? 'bg-blue-600 text-white shadow-2xs font-black' : 'text-slate-600 hover:text-slate-900 cursor-pointer'}"
            >
              ${item.smallUnit} (Eceran)
            </button>
            <button
              type="button"
              onclick="setCartItemUnit(${item.id}, '${item.primaryUnit}')"
              ${!canTakeFullPack ? 'disabled title="Sisa stok fisik kurang dari 1 kemasan penuh"' : ''}
              class="px-2 py-0.5 rounded-md transition ${!canTakeFullPack ? 'opacity-40 cursor-not-allowed text-slate-400' : (item.selectedUnit === item.primaryUnit ? 'bg-blue-600 text-white shadow-2xs font-black' : 'text-slate-600 hover:text-slate-900 cursor-pointer')}"
            >
              ${item.primaryUnit} (Kemasan)
            </button>
          </div>
        `;
      }

      // Keterangan konversi eceran
      let conversionHintHtml = '';
      if (isPackaging) {
        conversionHintHtml = `<span class="text-[10px] text-blue-700 font-mono font-bold block">Setara ${physicalPieces} ${item.smallUnit} (${item.conversionRate} ${item.smallUnit}/${item.primaryUnit})</span>`;
      } else if (item.hasMultiUnit) {
        conversionHintHtml = `<span class="text-[10px] text-slate-400 font-mono block">Tersedia: ${item.stock} ${item.smallUnit} eceran</span>`;
      } else {
        conversionHintHtml = `<span class="text-[10px] text-slate-400 font-mono block">Tersedia: ${item.stock} ${item.primaryUnit}</span>`;
      }

      html += `
        <div class="p-3 rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:border-blue-200 transition flex flex-col gap-2.5 text-xs anim-fade-in-up">
          <input type="hidden" name="items[${idx}][item_id]" value="${item.id}" />
          <input type="hidden" name="items[${idx}][quantity]" value="${item.qty}" />
          <input type="hidden" name="items[${idx}][unit]" value="${item.selectedUnit}" />
          
          <div class="flex items-start justify-between gap-2.5">
            <div class="min-w-0 flex-1 space-y-1">
              <div class="flex items-center gap-1.5 flex-wrap">
                <span class="inline-block px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 font-mono text-[10px] font-bold">${item.code}</span>
                ${item.hasMultiUnit ? `<span class="inline-block px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 text-[9.5px] font-bold">Multi-Satuan</span>` : ''}
              </div>
              <h5 class="font-bold text-slate-900 leading-snug truncate" title="${item.name}">${item.name}</h5>
              ${conversionHintHtml}
              ${unitSwitcherHtml}
            </div>

            <!-- Stepper Quantity & Hapus -->
            <div class="flex flex-col items-end gap-1.5 shrink-0 pt-0.5">
              <button
                type="button"
                onclick="removeFromCart(${item.id})"
                class="w-6 h-6 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition cursor-pointer"
                title="Hapus dari keranjang"
              >
                <svg class="w-3.5 h-3.5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
              
              <div class="flex items-center gap-1">
                <button
                  type="button"
                  onclick="updateCartQty(${item.id}, -1)"
                  class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 flex items-center justify-center font-black text-sm transition cursor-pointer shadow-2xs"
                >-</button>
                <input
                  type="number"
                  min="1"
                  max="${maxAllowed}"
                  value="${item.qty}"
                  onchange="setCartQtyDirect(${item.id}, this.value)"
                  class="w-12 h-7 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white text-center font-mono font-bold text-xs p-0 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
                />
                <button
                  type="button"
                  onclick="updateCartQty(${item.id}, 1)"
                  class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 flex items-center justify-center font-black text-sm transition cursor-pointer shadow-2xs"
                >+</button>
              </div>
              <span class="text-[10.5px] font-bold text-slate-600">${item.selectedUnit}</span>
            </div>
          </div>
        </div>
      `;
    });

    container.innerHTML = html;
    distinctEl.textContent = `${totalDistinct} Jenis`;
    totalQtyEl.textContent = `${totalPhysicalUnits} Unit Fisik`;

    updateCheckoutButtonState();
  }

  // Update Status Tombol Checkout
  function updateCheckoutButtonState() {
    const btn = document.getElementById('btn-process-checkout');
    const hasItems = Object.keys(cart).length > 0;
    
    const recSelectVal = document.getElementById('pos-recipient-select')?.value;
    const recManualVal = document.getElementById('manual-recipient-name')?.value?.trim();
    const hasRecipient = recSelectVal || recManualVal;

    btn.disabled = !(hasItems && hasRecipient);
  }

  // Submit Handler Transaksi Checkout POS
  async function handlePosCheckoutSubmit(event) {
    event.preventDefault();
    const form = document.getElementById('form-pos-checkout');
    const btn = document.getElementById('btn-process-checkout');

    if (Object.keys(cart).length === 0) {
      if (window.showToast) {
        window.showToast('Pilih minimal 1 barang persediaan ATK.', 'warning');
      } else {
        alert('Pilih minimal 1 barang persediaan ATK.');
      }
      return;
    }

    const recSelectVal = document.getElementById('pos-recipient-select')?.value;
    const recManualVal = document.getElementById('manual-recipient-name')?.value?.trim();
    if (!recSelectVal && !recManualVal) {
      if (window.showToast) {
        window.showToast('Tentukan pegawai atau ketik nama penerima barang.', 'warning');
      } else {
        alert('Tentukan pegawai atau ketik nama penerima barang.');
      }
      return;
    }

    btn.disabled = true;
    btn.innerHTML = `
      <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <span>Memproses Distribusi &amp; Mutasi Stok...</span>
    `;

    try {
      const formData = new FormData(form);
      const res = await fetch("{{ route('inventory.stock-out.store') }}", {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "Accept": "application/json",
        },
        body: formData
      });

      const data = await res.json();

      if (!res.ok) {
        let errMsg = data.message || 'Terjadi kesalahan saat memproses transaksi.';
        if (data.errors && data.errors.items) {
          errMsg = data.errors.items[0];
        }
        if (window.showToast) {
          window.showToast(errMsg, 'danger');
        } else {
          alert(errMsg);
        }
        btn.disabled = false;
        btn.innerHTML = '<span>Simpan Pengeluaran Barang</span>';
        return;
      }

      if (window.showToast) {
        window.showToast(`Transaksi ${data.transaction_number} berhasil disimpan.`, 'success');
      }

      // Ambil snapshot nama penerima dan jumlah fisik untuk modal dialog
      const recipientName = (document.getElementById('prev-rec-name')?.textContent && document.getElementById('prev-rec-name')?.textContent !== '-')
        ? document.getElementById('prev-rec-name')?.textContent 
        : (document.getElementById('manual-recipient-name')?.value || 'Pegawai BPTD');
      const totalUnits = document.getElementById('cart-total-qty')?.textContent || '0 Unit Fisik';

      // Tampilkan Modal Dialog Sukses (Memberikan opsi Cetak SBPB secara opsional tanpa paksaan auto-print)
      showTxSuccessModal(data.transaction_number, recipientName, totalUnits, data.redirect_print);

      // Reset cart dan form di background
      clearCart();
      form.reset();
      document.getElementById('recipient-preview-card').classList.add('hidden');

      btn.disabled = false;
      btn.innerHTML = `
        <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        <span>Simpan Pengeluaran Barang</span>
      `;

    } catch (err) {
      if (window.showToast) {
        window.showToast('Terjadi kesalahan jaringan atau server.', 'danger');
      } else {
        alert('Terjadi kesalahan jaringan.');
      }
      btn.disabled = false;
      btn.innerHTML = '<span>Simpan Pengeluaran Barang</span>';
    }
  }

  // Modal Dialog Sukses Handler (Cetak SBPB Opsional)
  function showTxSuccessModal(txNumber, recipient, totalUnits, printUrl) {
    const modal = document.getElementById('modal-tx-success');
    document.getElementById('success-modal-tx-number').textContent = txNumber;
    document.getElementById('success-modal-recipient').textContent = recipient;
    document.getElementById('success-modal-total-qty').textContent = totalUnits;
    document.getElementById('success-modal-print-btn').href = printUrl;

    modal.classList.remove('hidden');
    modal.classList.add('flex', 'anim-fade-in');
    document.body.classList.add('overflow-hidden');
  }

  function closeSuccessModalAndResetPos() {
    const modal = document.getElementById('modal-tx-success');
    modal.classList.add('hidden');
    modal.classList.remove('flex', 'anim-fade-in');
    document.body.classList.remove('overflow-hidden');
    // Refresh ke halaman POS agar sisa stok katalog dan no antrean berikutnya sinkron
    window.location.href = "{{ route('inventory.stock-out.index') }}";
  }

  function closeSuccessModalAndGoHistory() {
    const modal = document.getElementById('modal-tx-success');
    modal.classList.add('hidden');
    modal.classList.remove('flex', 'anim-fade-in');
    document.body.classList.remove('overflow-hidden');
    window.location.href = "{{ route('inventory.stock-out.index', ['tab' => 'history']) }}";
  }

  // Modal Detail Transaksi Riwayat
  async function openTxDetailModal(txId) {
    const modal = document.getElementById('modal-tx-detail');
    const tbody = document.getElementById('detail-modal-items-tbody');
    tbody.innerHTML = '<tr><td colspan="5" class="p-4 text-center text-slate-400">Memuat rincian transaksi...</td></tr>';

    modal.classList.remove('hidden');
    modal.classList.add('flex', 'anim-fade-in');
    document.body.classList.add('overflow-hidden');

    try {
      const res = await fetch(`/inventory/stock-out/${txId}`);
      const result = await res.json();
      if (result.success && result.data) {
        const tx = result.data;
        document.getElementById('detail-modal-tx-number').textContent = tx.transaction_number;
        document.getElementById('detail-modal-recipient-name').textContent = tx.recipient_name;
        document.getElementById('detail-modal-recipient-nip').textContent = `NIP: ${tx.recipient_nip || '-'}`;
        document.getElementById('detail-modal-recipient-unit').textContent = tx.recipient_unit || '-';
        document.getElementById('detail-modal-date').textContent = `Tanggal: ${tx.transaction_date}`;
        document.getElementById('detail-modal-print-btn').href = result.print_url;

        let rowsHtml = '';
        tx.details.forEach((d, i) => {
          const isMulti = d.conversion_factor > 1;
          rowsHtml += `
            <tr class="hover:bg-slate-50">
              <td class="p-2.5 text-center font-semibold text-slate-400">${i + 1}</td>
              <td class="p-2.5">
                <span class="font-bold text-slate-800 block">${d.item_name}</span>
                <span class="text-[10px] text-slate-400 font-medium">${d.item_code}</span>
              </td>
              <td class="p-2.5 text-center font-bold text-blue-700">${d.quantity}</td>
              <td class="p-2.5 text-center text-slate-600">
                <span class="font-semibold">${d.unit}</span>
                ${isMulti ? `<span class="block text-[10px] text-slate-400">(${d.base_quantity} ${d.item?.small_unit || 'Pcs'})</span>` : ''}
              </td>
              <td class="p-2.5 font-semibold text-slate-600 text-center">${d.current_stock_after}</td>
            </tr>
          `;
        });
        tbody.innerHTML = rowsHtml;
      }
    } catch (e) {
      tbody.innerHTML = '<tr><td colspan="5" class="p-4 text-center text-rose-500">Gagal mengambil data transaksi.</td></tr>';
    }
  }

  function closeTxDetailModal() {
    const modal = document.getElementById('modal-tx-detail');
    modal.classList.add('hidden');
    modal.classList.remove('flex', 'anim-fade-in');
    document.body.classList.remove('overflow-hidden');
  }

  // Listener input manual jika ada ketikan
  document.getElementById('manual-recipient-name')?.addEventListener('input', updateCheckoutButtonState);
</script>
@endpush
@endsection
