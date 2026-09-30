@extends('layouts.app')

@section('title', 'Data Master ATK - BPTD Kelas II Jawa Timur')

@section('content')
<main class="flex-1 max-w-[1720px] w-full mx-auto px-4 sm:px-6 lg:px-8 py-7 space-y-7">
  <!-- Header & Breadcrumb -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="space-y-1">
      <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
        <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition">Dashboard</a>
        <span>/</span>
        <span class="text-slate-600">Master Inventory ATK</span>
        <span>/</span>
        <span class="text-blue-600">Data Master ATK</span>
      </div>
      <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
        Katalog dan Master Data ATK
      </h2>
      <p class="text-xs sm:text-sm text-slate-500">
        Daftar seluruh inventaris Alat Tulis Kantor, batas minimum stok, lokasi penyimpanan, dan status ketersediaan.
      </p>
    </div>

    <!-- Quick Action Button -->
    <div class="flex items-center gap-2">
      <button
        type="button"
        onclick="openModal('modal-add-item')"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold text-xs sm:text-sm shadow-md shadow-blue-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]"
      >
        <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
        <span>Tambah Barang ATK</span>
      </button>
    </div>
  </div>

  <!-- 4 Summary Metric Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
    <!-- Card 1: Total SKU -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Katalog</p>
          <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($totalItems) }}</h3>
          <p class="text-[11px] text-slate-500 mt-0.5">Jenis item terdaftar</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
          </svg>
        </div>
      </div>
    </div>

    <!-- Card 2: Total Stok Fisik -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Fisik Unit</p>
          <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($totalPhysicalStock) }}</h3>
          <p class="text-[11px] text-slate-500 mt-0.5">Akumulasi kuantitas stok</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
      </div>
    </div>

    <!-- Card 3: Stok Menipis -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Stok Menipis</p>
          <h3 class="text-2xl font-extrabold text-amber-700 mt-1">{{ number_format($lowStockCount) }}</h3>
          <p class="text-[11px] text-slate-500 mt-0.5">&le; minimum threshold</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
        </div>
      </div>
    </div>

    <!-- Card 4: Stok Habis -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs font-semibold text-rose-600 uppercase tracking-wider">Stok Habis</p>
          <h3 class="text-2xl font-extrabold text-rose-700 mt-1">{{ number_format($outOfStockCount) }}</h3>
          <p class="text-[11px] text-slate-500 mt-0.5">Kuantitas kosong (0 unit)</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
          </svg>
        </div>
      </div>
    </div>
  </div>

  <!-- Search & Filter Bar -->
  <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs">
    <form method="GET" action="{{ route('inventory.items.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
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
          placeholder="Cari kode ATK, barcode, atau nama barang..."
          class="w-full pl-10 pr-4 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition"
        />
      </div>

      <!-- Category Filter -->
      <div class="lg:col-span-3">
        <select
          name="category_id"
          class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition"
        >
          <option value="">Semua Kategori ATK</option>
          @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
              {{ $cat->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Stock Status Filter -->
      <div class="lg:col-span-2">
        <select
          name="stock_status"
          class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition"
        >
          <option value="">Semua Status Stok</option>
          <option value="available" {{ request('stock_status') === 'available' ? 'selected' : '' }}>Tersedia</option>
          <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Stok Menipis</option>
          <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Stok Habis</option>
        </select>
      </div>

      <!-- Buttons: Filter & Reset -->
      <div class="lg:col-span-2 flex items-center gap-2">
        <button
          type="submit"
          class="flex-1 py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs tracking-wide transition text-center"
        >
          Terapkan
        </button>
        @if (request()->hasAny(['keyword', 'category_id', 'stock_status', 'status']))
          <a
            href="{{ route('inventory.items.index') }}"
            class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs tracking-wide transition text-center"
            title="Reset Filter"
          >
            Reset
          </a>
        @endif
      </div>
    </form>
  </div>

  <!-- Data Table Card -->
  <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-bold text-slate-900 text-sm">
        Daftar Barang Inventaris
        <span class="ml-1 text-xs text-slate-400 font-normal">({{ $items->total() }} barang terdata)</span>
      </h3>
      <span class="text-xs text-slate-400">Hal. {{ $items->currentPage() }} dari {{ $items->lastPage() }}</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs sm:text-sm">
        <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-100">
          <tr>
            <th class="py-3.5 px-4">Item & Kode</th>
            <th class="py-3.5 px-4">Kategori</th>
            <th class="py-3.5 px-4 text-center">Stok Fisik</th>
            <th class="py-3.5 px-4 text-center">Min / Target</th>
            <th class="py-3.5 px-4 text-center">Status Stok</th>
            <th class="py-3.5 px-4">Lokasi Rak</th>
            <th class="py-3.5 px-4">Supplier</th>
            <th class="py-3.5 px-4 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
          @forelse ($items as $item)
            <tr class="hover:bg-slate-50/60 transition">
              <!-- Item & Kode -->
              <td class="py-3.5 px-4">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 font-bold text-xs">
                    {{ strtoupper(substr($item->category?->name ?? 'ATK', 0, 2)) }}
                  </div>
                  <div>
                    <div class="font-bold text-slate-900 hover:text-blue-600 transition">{{ $item->name }}</div>
                    <div class="flex items-center gap-1.5 mt-0.5">
                      <span class="font-mono text-[10px] text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded font-semibold">{{ $item->code }}</span>
                      @if ($item->barcode)
                        <span class="font-mono text-[10px] text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded" title="Barcode: {{ $item->barcode }}">&#9646; {{ $item->barcode }}</span>
                      @endif
                    </div>
                  </div>
                </div>
              </td>

              <!-- Kategori -->
              <td class="py-3.5 px-4">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium bg-slate-100 text-slate-700">
                  {{ $item->category?->name ?? 'Umum' }}
                </span>
              </td>

              <!-- Stok Fisik & Gauge -->
              <td class="py-3.5 px-4 text-center">
                <div class="inline-flex flex-col items-center">
                  <div class="flex items-baseline gap-1">
                    <span class="text-base font-extrabold {{ $item->current_stock <= 0 ? 'text-rose-600' : ($item->current_stock <= $item->minimum_stock ? 'text-amber-600' : 'text-slate-900') }}">
                      {{ $item->current_stock }}
                    </span>
                    <span class="text-[11px] text-slate-500 font-medium">{{ $item->unit }}</span>
                  </div>
                  <!-- Progress bar terhadap target -->
                  <div class="w-20 bg-slate-100 h-1.5 rounded-full mt-1 overflow-hidden">
                    <div
                      class="h-full rounded-full {{ $item->current_stock <= 0 ? 'bg-rose-500' : ($item->current_stock <= $item->minimum_stock ? 'bg-amber-500' : 'bg-blue-600') }}"
                      style="width: {{ $item->stock_percentage }}%"
                    ></div>
                  </div>
                </div>
              </td>

              <!-- Min / Target -->
              <td class="py-3.5 px-4 text-center text-xs text-slate-600">
                <span class="font-semibold text-slate-800">{{ $item->minimum_stock }}</span>
                <span class="text-slate-400">/</span>
                <span class="font-semibold text-slate-800">{{ $item->target_stock }}</span>
                <span class="text-[10px] text-slate-400 block">{{ $item->unit }}</span>
              </td>

              <!-- Status Stok Pill -->
              <td class="py-3.5 px-4 text-center">
                @if ($item->stock_status === 'available')
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Tersedia
                  </span>
                @elseif ($item->stock_status === 'low_stock')
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    Menipis
                  </span>
                @else
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    Habis
                  </span>
                @endif
              </td>

              <!-- Lokasi Rak -->
              <td class="py-3.5 px-4 text-xs text-slate-600">
                <div class="flex items-center gap-1">
                  <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                  <span class="truncate max-w-[140px]">{{ $item->storage_location ?? '-' }}</span>
                </div>
              </td>

              <!-- Supplier -->
              <td class="py-3.5 px-4 text-xs text-slate-600">
                <span class="truncate max-w-[150px] block" title="{{ $item->supplier?->name }}">{{ $item->supplier?->name ?? '-' }}</span>
              </td>

              <!-- Aksi -->
              <td class="py-3.5 px-4 text-center">
                <div class="inline-flex items-center gap-1">
                  <!-- Button Edit -->
                  <button
                    type="button"
                    onclick="openEditModal({{ json_encode($item) }})"
                    class="p-1.5 rounded-lg text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition"
                    title="Edit Data Barang"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>

                  <!-- Toggle Aktif/Nonaktif -->
                  <form
                    action="{{ route('inventory.items.destroy', $item) }}"
                    method="POST"
                    class="inline"
                    onsubmit="window.confirmSubmit(event, {
                      title: '{{ $item->status === 'active' ? 'Nonaktifkan Barang ATK' : 'Aktifkan Barang ATK' }}',
                      message: '{{ $item->status === 'active' ? 'Barang yang dinonaktifkan tidak dapat dipilih untuk transaksi Stock Out baru.' : 'Barang akan kembali aktif dan dapat didistribusikan.' }}',
                      badgeText: '{{ addslashes($item->name) }} ({{ $item->code }})',
                      type: '{{ $item->status === 'active' ? 'warning' : 'primary' }}',
                      confirmText: '{{ $item->status === 'active' ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}'
                    })"
                  >
                    @csrf
                    @method('DELETE')
                    <button
                      type="submit"
                      class="p-1.5 rounded-lg {{ $item->status === 'active' ? 'text-slate-400 hover:text-rose-600 hover:bg-rose-50' : 'text-slate-400 hover:text-emerald-600 hover:bg-emerald-50' }} transition"
                      title="{{ $item->status === 'active' ? 'Nonaktifkan Barang' : 'Aktifkan Barang' }}"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if ($item->status === 'active')
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        @else
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        @endif
                      </svg>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="py-12 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-sm font-semibold text-slate-600">Tidak ada barang ATK ditemukan</p>
                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter yang dipilih.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    @if ($items->hasPages())
      <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
        {{ $items->links() }}
      </div>
    @endif
  </div>
</main>

<!-- Modal: Tambah Barang ATK Baru -->
<div id="modal-add-item" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl max-w-2xl w-full border border-slate-200 shadow-xl overflow-hidden animate-scale-in">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
          <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
          </svg>
        </div>
        <h3 class="font-bold text-slate-900 text-base">Tambah Barang ATK Baru</h3>
      </div>
      <button type="button" onclick="closeModal('modal-add-item')" class="text-slate-400 hover:text-slate-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <form action="{{ route('inventory.items.store') }}" method="POST" class="p-6 space-y-4">
      @csrf
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Kode ATK -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode ATK <span class="text-rose-500">*</span></label>
          <input
            type="text"
            name="code"
            required
            value="{{ old('code', 'ATK-' . date('Y') . '-' . str_pad(rand(100, 999), 4, '0', STR_PAD_LEFT)) }}"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 font-mono bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
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
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 font-mono focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          />
        </div>

        <!-- Nama Barang -->
        <div class="sm:col-span-2">
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Barang ATK <span class="text-rose-500">*</span></label>
          <input
            type="text"
            name="name"
            required
            value="{{ old('name') }}"
            placeholder="Contoh: Kertas HVS A4 80gr Sinar Dunia"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          />
        </div>

        <!-- Kategori -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kategori <span class="text-rose-500">*</span></label>
          <select
            name="category_id"
            required
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          >
            <option value="">Pilih Kategori</option>
            @foreach ($categories as $cat)
              <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Satuan -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Satuan <span class="text-rose-500">*</span></label>
          <select
            name="unit_id"
            required
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          >
            <option value="">Pilih Satuan</option>
            @foreach ($units as $u)
              <option value="{{ $u->id }}" {{ old('unit_id') == $u->id ? 'selected' : '' }}>
                {{ $u->name }} ({{ $u->code }})
              </option>
            @endforeach
          </select>
        </div>

        <!-- Stok Awal -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Stok Awal</label>
          <input
            type="number"
            name="current_stock"
            min="0"
            value="{{ old('current_stock', 0) }}"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          />
        </div>

        <!-- Minimum Stok -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Minimum Stok (Peringatan) <span class="text-rose-500">*</span></label>
          <input
            type="number"
            name="minimum_stock"
            required
            min="0"
            value="{{ old('minimum_stock', 10) }}"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          />
        </div>

        <!-- Target Stok -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Target Stok Ideal <span class="text-rose-500">*</span></label>
          <input
            type="number"
            name="target_stock"
            required
            min="0"
            value="{{ old('target_stock', 50) }}"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          />
        </div>

        <!-- Lokasi Penyimpanan -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Lokasi Penyimpanan</label>
          <input
            type="text"
            name="storage_location"
            value="{{ old('storage_location') }}"
            placeholder="Contoh: Gudang ATK - Rak A1"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          />
        </div>

        <!-- Supplier -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Supplier Rekanan</label>
          <select
            name="supplier_id"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          >
            <option value="">Pilih Supplier (Opsional)</option>
            @foreach ($suppliers as $sup)
              <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                {{ $sup->name }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Status -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status Operasional <span class="text-rose-500">*</span></label>
          <select
            name="status"
            required
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          >
            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif Digunakan</option>
            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif / Tidak Beredar</option>
          </select>
        </div>

        <!-- Deskripsi -->
        <div class="sm:col-span-2">
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan / Spesifikasi Barang</label>
          <textarea
            name="description"
            rows="2"
            placeholder="Catatan tambahan spesifikasi barang..."
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          >{{ old('description') }}</textarea>
        </div>
      </div>

      <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
        <button
          type="button"
          onclick="closeModal('modal-add-item')"
          class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition"
        >
          Batal
        </button>
        <button
          type="submit"
          class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md shadow-blue-500/20 transition"
        >
          Simpan Barang ATK
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Edit Barang ATK -->
<div id="modal-edit-item" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl max-w-2xl w-full border border-slate-200 shadow-xl overflow-hidden animate-scale-in">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
          <svg class="w-4 h-4 stroke-[2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
          </svg>
        </div>
        <h3 class="font-bold text-slate-900 text-base">Edit Data Barang ATK</h3>
      </div>
      <button type="button" onclick="closeModal('modal-edit-item')" class="text-slate-400 hover:text-slate-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <form id="form-edit-item" method="POST" class="p-6 space-y-4">
      @csrf
      @method('PUT')
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Kode ATK -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode ATK <span class="text-rose-500">*</span></label>
          <input
            type="text"
            id="edit-code"
            name="code"
            required
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 font-mono bg-slate-50 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          />
        </div>

        <!-- Barcode -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Barcode / SKU Scanner</label>
          <input
            type="text"
            id="edit-barcode"
            name="barcode"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 font-mono focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          />
        </div>

        <!-- Nama Barang -->
        <div class="sm:col-span-2">
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Barang ATK <span class="text-rose-500">*</span></label>
          <input
            type="text"
            id="edit-name"
            name="name"
            required
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          />
        </div>

        <!-- Kategori -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kategori <span class="text-rose-500">*</span></label>
          <select
            id="edit-category-id"
            name="category_id"
            required
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          >
            <option value="">Pilih Kategori</option>
            @foreach ($categories as $cat)
              <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>

        <!-- Satuan -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Satuan <span class="text-rose-500">*</span></label>
          <select
            id="edit-unit-id"
            name="unit_id"
            required
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          >
            <option value="">Pilih Satuan</option>
            @foreach ($units as $u)
              <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->code }})</option>
            @endforeach
          </select>
        </div>

        <!-- Minimum Stok -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Minimum Stok <span class="text-rose-500">*</span></label>
          <input
            type="number"
            id="edit-minimum-stock"
            name="minimum_stock"
            required
            min="0"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          />
        </div>

        <!-- Target Stok -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Target Stok Ideal <span class="text-rose-500">*</span></label>
          <input
            type="number"
            id="edit-target-stock"
            name="target_stock"
            required
            min="0"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          />
        </div>

        <!-- Lokasi Penyimpanan -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Lokasi Penyimpanan</label>
          <input
            type="text"
            id="edit-storage-location"
            name="storage_location"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          />
        </div>

        <!-- Supplier -->
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Supplier Rekanan</label>
          <select
            id="edit-supplier-id"
            name="supplier_id"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          >
            <option value="">Pilih Supplier (Opsional)</option>
            @foreach ($suppliers as $sup)
              <option value="{{ $sup->id }}">{{ $sup->name }}</option>
            @endforeach
          </select>
        </div>

        <!-- Status -->
        <div class="sm:col-span-2">
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status Operasional <span class="text-rose-500">*</span></label>
          <select
            id="edit-status"
            name="status"
            required
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          >
            <option value="active">Aktif Digunakan</option>
            <option value="inactive">Nonaktif / Tidak Beredar</option>
          </select>
        </div>

        <!-- Deskripsi -->
        <div class="sm:col-span-2">
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan / Spesifikasi Barang</label>
          <textarea
            id="edit-description"
            name="description"
            rows="2"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
          ></textarea>
        </div>
      </div>

      <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
        <button
          type="button"
          onclick="closeModal('modal-edit-item')"
          class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition"
        >
          Batal
        </button>
        <button
          type="submit"
          class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md shadow-blue-500/20 transition"
        >
          Perbarui Data ATK
        </button>
      </div>
    </form>
  </div>
</div>

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

  // Close modals on Escape key
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
      closeModal('modal-add-item');
      closeModal('modal-edit-item');
    }
  });
</script>
@endsection
