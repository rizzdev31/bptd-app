@extends('layouts.app')

@section('title', 'Dashboard Utama - BPTD Kelas II Jawa Timur')

@push('styles')
<style>
  /* High performance entrance keyframes (GPU accelerated) */
  @keyframes dashCardEntrance {
    0% {
      opacity: 0;
      transform: translateY(16px);
    }
    100% {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .dash-stagger {
    opacity: 0;
    animation: dashCardEntrance 0.65s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    will-change: transform, opacity;
  }

  .dash-delay-1 { animation-delay: 0.05s; }
  .dash-delay-2 { animation-delay: 0.12s; }
  .dash-delay-3 { animation-delay: 0.19s; }
  .dash-delay-4 { animation-delay: 0.26s; }
  .dash-delay-5 { animation-delay: 0.33s; }
  .dash-delay-6 { animation-delay: 0.40s; }

  /* Smooth interactive hover transitions */
  .dash-interactive-card {
    transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.28s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.28s ease;
  }
  .dash-interactive-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px -6px rgba(11, 35, 65, 0.08), 0 4px 8px -2px rgba(11, 35, 65, 0.04);
  }

  /* Circular gauge stroke transition */
  .dash-gauge-circle {
    transition: stroke-dashoffset 1.4s cubic-bezier(0.16, 1, 0.3, 1);
  }

  /* Soft pulse for status badges */
  @keyframes badgePulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.08); opacity: 0.85; }
  }
  .animate-badge-pulse {
    animation: badgePulse 2.4s infinite ease-in-out;
  }

  /* ApexCharts styling adjustments for BPTD Ministry Theme */
  .apexcharts-tooltip {
    border-radius: 12px !important;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
    border: 1px solid #e2e8f0 !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
  }
  .apexcharts-xaxistooltip {
    border-radius: 8px !important;
    border: 1px solid #cbd5e1 !important;
  }

  @media (prefers-reduced-motion: reduce) {
    .dash-stagger {
      opacity: 1 !important;
      transform: none !important;
      animation: none !important;
    }
    .dash-interactive-card {
      transition: none !important;
    }
    .dash-interactive-card:hover {
      transform: none !important;
    }
    .dash-gauge-circle {
      transition: none !important;
    }
  }
</style>
@endpush

@section('content')
@php
  $safeStockCount = max(0, ($totalItems ?? 0) - ($lowStockCount ?? 0) - ($outOfStockCount ?? 0));
  $stockHealthRate = ($totalItems ?? 0) > 0 ? round(($safeStockCount / $totalItems) * 100) : 100;
  $criticalCount = ($lowStockCount ?? 0) + ($outOfStockCount ?? 0);
@endphp

<!-- BEGIN: DashboardContextContent (Main Board Cloning) -->
<main
  class="flex-1 max-w-430 w-full mx-auto px-4 sm:px-6 lg:px-8 py-7 space-y-7"
>
  <!-- Header Main Board -->
  <div class="dash-stagger dash-delay-1 space-y-0.5">
    <p class="text-sm font-semibold text-slate-800">
      Selamat Datang
      <span class="text-blue-600 font-bold">{{ Auth::user()?->name ?? 'Pegawai' }}</span>
      <span class="text-xs bg-blue-100 text-blue-700 px-2.5 py-0.5 rounded-full ml-1 font-semibold">{{ Auth::user()?->role?->label ?? 'Staf' }}</span>
    </p>
    <h2
      class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900"
    >
      Dashboard Utama Aplikasi
    </h2>
  </div>

  @if (($lowStockCount ?? 0) > 0 || ($outOfStockCount ?? 0) > 0)
    <div class="dash-stagger dash-delay-2 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
        </div>
        <div>
          <h4 class="font-bold text-sm">Peringatan Ketersediaan Persediaan ATK</h4>
          <p class="text-xs text-amber-700 mt-0.5">
            Terdapat <span class="font-bold underline">{{ $lowStockCount ?? 0 }} item menipis</span> dan <span class="font-bold underline">{{ $outOfStockCount ?? 0 }} item habis</span> yang membutuhkan perhatian atau pengadaan baru.
          </p>
        </div>
      </div>
      <a
        href="{{ route('inventory.items.index', ['stock_status' => 'low_stock']) }}"
        class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs transition shrink-0 shadow-xs hover:scale-102"
      >
        Lihat Stok Kritis &rarr;
      </a>
    </div>
  @endif

  <!-- 1. Row of 3 Metric Cards with circular gauges -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Card 1: Pegawai Aktif -->
    <div
      class="dash-stagger dash-delay-2 dash-interactive-card bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between"
    >
      <div class="flex items-center gap-4">
        <!-- Gauge Circle -->
        <div
          class="relative w-20 h-20 shrink-0 flex items-center justify-center"
        >
          <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
            <circle
              cx="18"
              cy="18"
              fill="none"
              r="14.5"
              stroke="#e0e7ff"
              stroke-width="3"
            ></circle>
            <circle
              cx="18"
              cy="18"
              fill="none"
              r="14.5"
              stroke="#2563eb"
              stroke-dasharray="91"
              stroke-dashoffset="91"
              data-target-offset="20"
              class="dash-gauge-circle"
              stroke-linecap="round"
              stroke-width="3.2"
            ></circle>
          </svg>
          <span class="dash-counter absolute text-xs font-bold text-slate-900" data-target="{{ $activeEmployees ?? 0 }}">0</span>
        </div>
        <!-- Details -->
        <div class="flex-1">
          <div class="flex items-center gap-1.5">
            <svg class="w-4 h-4 text-blue-600 fill-current" viewBox="0 0 24 24">
              <path
                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"
              ></path>
            </svg>
            <h3 class="font-bold text-slate-900 text-sm">Pegawai Aktif</h3>
          </div>
          <p class="text-xs text-slate-400 mt-1 leading-snug">
            <span class="dash-counter font-semibold text-slate-700" data-target="{{ $activeEmployees ?? 0 }}">0</span> Pegawai Terdaftar di Sistem
          </p>
        </div>
      </div>
      <div class="mt-4">
        <a
          href="{{ route('pegawai.index') }}"
          class="block w-full py-2 px-4 rounded-xl bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold text-xs tracking-wide transition text-center hover:shadow-xs"
        >
          Monitoring Pegawai
        </a>
      </div>
    </div>

    <!-- Card 2: Report Inventory -->
    <div
      class="dash-stagger dash-delay-2 dash-interactive-card bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between"
    >
      <div class="flex items-center gap-4">
        <!-- Gauge Circle with Red & Blue accents -->
        <div
          class="relative w-20 h-20 shrink-0 flex items-center justify-center"
        >
          <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
            <circle
              cx="18"
              cy="18"
              fill="none"
              r="14.5"
              stroke="#f1f5f9"
              stroke-width="3"
            ></circle>
            <!-- Blue arc -->
            <circle
              cx="18"
              cy="18"
              fill="none"
              r="14.5"
              stroke="#2563eb"
              stroke-dasharray="91"
              stroke-dashoffset="91"
              data-target-offset="40"
              class="dash-gauge-circle"
              stroke-linecap="round"
              stroke-width="3.2"
            ></circle>
          </svg>
          <span class="dash-counter absolute text-xs font-bold text-slate-900" data-target="{{ $totalItems ?? 0 }}">0</span>
        </div>
        <!-- Details -->
        <div class="flex-1">
          <h3 class="font-bold text-slate-900 text-sm">Katalog ATK</h3>
          <p class="text-xs text-slate-400 mt-1 leading-snug">
            <span class="dash-counter font-semibold text-slate-700" data-target="{{ $totalItems ?? 0 }}">0</span> Jenis Item Terdaftar
          </p>
        </div>
      </div>
      <div class="mt-4">
        <a
          href="{{ route('inventory.items.index') }}"
          class="block w-full py-2 px-4 rounded-xl bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold text-xs tracking-wide transition text-center hover:shadow-xs"
        >
          Monitoring Inventory ATK
        </a>
      </div>
    </div>

    <!-- Card 3: Stock Barang -->
    <div
      class="dash-stagger dash-delay-2 dash-interactive-card bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between"
    >
      <div class="flex items-center gap-4">
        <!-- Gauge Circle with Box Icon inside -->
        <div
          class="relative w-20 h-20 shrink-0 flex items-center justify-center"
        >
          <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
            <circle
              cx="18"
              cy="18"
              fill="none"
              r="14.5"
              stroke="#f1f5f9"
              stroke-width="3"
            ></circle>
            <circle
              cx="18"
              cy="18"
              fill="none"
              r="14.5"
              stroke="#2563eb"
              stroke-dasharray="91"
              stroke-dashoffset="91"
              data-target-offset="26"
              class="dash-gauge-circle"
              stroke-linecap="round"
              stroke-width="3.2"
            ></circle>
          </svg>
          <div
            class="absolute w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600"
          >
            <svg
              class="w-4 h-4 stroke-[1.8]"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                stroke-linecap="round"
                stroke-linejoin="round"
              ></path>
            </svg>
          </div>
        </div>
        <!-- Details -->
        <div class="flex-1">
          <h3 class="font-bold text-slate-900 text-sm">Stock Barang</h3>
          <p class="text-xs text-slate-400 mt-1 leading-snug">
            <span class="dash-counter font-semibold text-slate-700" data-target="{{ $totalStock ?? 0 }}" data-format-number="true">0</span> Unit Total Fisik
          </p>
        </div>
      </div>
      <div class="mt-4">
        <a
          href="{{ route('inventory.items.index') }}"
          class="block w-full py-2 px-4 rounded-xl bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold text-xs tracking-wide transition text-center hover:shadow-xs"
        >
          Cek Stock Barang Sekarang
        </a>
      </div>
    </div>
  </div>

  <!-- 2. Mid Section: Analytics & Visual Charts Grid (3 Columns) -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
    <!-- Left Column: Rekapitulasi Inventory Bulanan (5 cols) -->
    <div
      class="dash-stagger dash-delay-3 dash-interactive-card lg:col-span-5 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between"
    >
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-base font-bold text-slate-900">
            Rekapitulasi
            <span class="font-normal text-slate-600">Inventory Bulanan</span>
          </h3>
          <p class="text-xs text-slate-400 mt-0.5">Distribusi pergerakan barang per bulan</p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">
          <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
          Tahun {{ date('Y') }}
        </span>
      </div>
      
      <!-- Interactive ApexCharts Column Chart -->
      <div class="mt-4 pt-1">
        <div id="monthlyRecapChart" class="w-full h-56 -ml-2"></div>
      </div>
    </div>

    <!-- Center Column: Traffic Inventory (4 cols) -->
    <div
      class="dash-stagger dash-delay-3 dash-interactive-card lg:col-span-4 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between"
    >
      <div class="flex items-center justify-between">
        <h3 class="text-base font-bold text-slate-900">Traffic Inventory</h3>
        <span class="text-xs font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md">Tren 30 Hari</span>
      </div>
      <div class="my-auto space-y-6 pt-4 pb-2">
        <!-- Metric 1: User Peminjaman (1,235 UP) -->
        <div class="flex items-center justify-between gap-4">
          <!-- Animated Area Sparkline -->
          <div id="sparklineBorrow" class="w-28 sm:w-32 h-12 flex items-center"></div>
          
          <!-- Numbers -->
          <div class="text-right">
            <div
              class="flex items-center justify-end gap-1 text-2xl font-extrabold text-slate-900"
            >
              <span class="dash-counter" data-target="1235" data-format-number="true">0</span>
              <span class="text-emerald-500 text-xl font-bold">↑</span>
            </div>
            <p class="text-xs text-slate-500 font-medium mt-0.5">
              User Peminjaman
            </p>
          </div>
        </div>

        <!-- Metric 2: Barang Berkurang (456 DOWN) -->
        <div
          class="flex items-center justify-between gap-4 border-t border-slate-100 pt-5"
        >
          <!-- Animated Area Sparkline -->
          <div id="sparklineReduced" class="w-28 sm:w-32 h-12 flex items-center"></div>
          
          <!-- Numbers -->
          <div class="text-right">
            <div
              class="flex items-center justify-end gap-1 text-2xl font-extrabold text-slate-900"
            >
              <span class="dash-counter" data-target="456" data-format-number="true">0</span>
              <span class="text-red-400 text-xl font-bold">↓</span>
            </div>
            <p class="text-xs text-slate-500 font-medium mt-0.5">
              Barang Berkurang
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column: Royal Blue Solid Accent Card with Interactive Stock Health Radial (3 cols) -->
    <div
      class="dash-stagger dash-delay-4 dash-interactive-card lg:col-span-3 rounded-2xl bg-linear-to-br from-[#1d3bbd] via-[#264bf6] to-[#1e40af] p-5 text-white relative overflow-hidden flex flex-col justify-between shadow-sm min-h-55"
    >
      <!-- Subtle glow and rounded geometry -->
      <div
        class="absolute -right-10 -bottom-10 w-44 h-44 rounded-full bg-white/10 blur-xl pointer-events-none"
      ></div>
      <div
        class="absolute -left-10 -top-10 w-40 h-40 rounded-full bg-blue-400/20 blur-lg pointer-events-none"
      ></div>

      <!-- Top Header -->
      <div class="relative z-10 flex items-center justify-between">
        <div>
          <span class="text-[10px] font-bold uppercase tracking-wider text-blue-200">Indeks Logistik</span>
          <h4 class="text-sm font-bold text-white mt-0.5">Kesehatan Stok ATK</h4>
        </div>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-white/15 text-white backdrop-blur-xs border border-white/20">
          Real-Time
        </span>
      </div>

      <!-- Center Animated Radial Bar Chart -->
      <div class="relative z-10 my-auto flex items-center justify-center py-1">
        <div id="stockHealthChart" data-health-rate="{{ $stockHealthRate }}" class="w-full flex items-center justify-center"></div>
      </div>

      <!-- Bottom Indicators -->
      <div class="relative z-10 grid grid-cols-2 gap-2 pt-2 border-t border-white/15 text-center">
        <div class="bg-white/10 rounded-xl p-2 backdrop-blur-xs">
          <p class="text-[10px] text-blue-100 font-medium">Stok Aman</p>
          <p class="text-sm font-extrabold text-white mt-0.5">{{ $safeStockCount }} Item</p>
        </div>
        <div class="bg-white/10 rounded-xl p-2 backdrop-blur-xs">
          <p class="text-[10px] text-amber-200 font-medium">Stok Kritis</p>
          <p class="text-sm font-extrabold text-amber-300 mt-0.5">{{ $criticalCount }} Item</p>
        </div>
      </div>
    </div>
  </div>

  <!-- 3. Bottom Section: Data Real Time Inventory Apps Table -->
  <div class="dash-stagger dash-delay-5 space-y-3 pt-2">
    <div class="flex items-center justify-between">
      <h3 class="text-xl font-bold text-slate-900">
        Data Real Time
        <span class="font-normal text-slate-700">Inventory Apps</span>
      </h3>
      <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
        <span class="relative flex h-2 w-2">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
        </span>
        Sinkronisasi Otomatis
      </div>
    </div>
    
    <div
      class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden"
    >
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs sm:text-sm text-slate-600">
          <thead
            class="bg-slate-50/75 text-slate-800 font-bold border-b border-slate-100 text-xs"
          >
            <tr>
              <th class="py-4 px-5 w-12 text-center" scope="col">
                <input
                  class="rounded border-slate-300 text-blue-600 focus:ring-0 focus:ring-offset-0"
                  type="checkbox"
                />
              </th>
              <th class="py-4 px-4 font-bold" scope="col">ID</th>
              <th class="py-4 px-4 font-bold" scope="col">NIP</th>
              <th class="py-4 px-4 font-bold" scope="col">Nama Pegawai</th>
              <th class="py-4 px-4 font-bold" scope="col">Jenis Pengajuan</th>
              <th class="py-4 px-4 font-bold" scope="col">Nama Barang</th>
              <th class="py-4 px-4 font-bold" scope="col">Status</th>
              <th class="py-4 px-4 font-bold text-center" scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-normal">
            <!-- Row 1: PGJ-001 Sukses -->
            <tr class="hover:bg-blue-50/40 transition-colors duration-150">
              <td class="py-4 px-5 text-center">
                <input
                  class="rounded border-slate-300 text-blue-600 focus:ring-0 focus:ring-offset-0"
                  type="checkbox"
                />
              </td>
              <td class="py-4 px-4 font-medium text-slate-800">PGJ-001</td>
              <td class="py-4 px-4 text-slate-600 font-mono text-xs">0192648989</td>
              <td class="py-4 px-4 text-slate-800 font-medium">
                Muhammad Ulil
              </td>
              <td class="py-4 px-4">
                <span
                  class="inline-block px-3 py-1 rounded-md border border-blue-400 text-blue-600 text-[11px] font-semibold bg-blue-50/50"
                >
                  Permintaan
                </span>
              </td>
              <td class="py-4 px-4 text-slate-700">Bulpoin</td>
              <td class="py-4 px-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-semibold">
                  <span class="relative flex h-1.5 w-1.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                  </span>
                  Sukses
                </span>
              </td>
              <td class="py-4 px-4">
                <div class="flex items-center justify-center gap-2.5">
                  <!-- Checkmark Green -->
                  <button
                    class="text-emerald-500 hover:text-emerald-600 p-1 hover:scale-115 active:scale-95 transition-transform"
                    title="Setujui"
                    type="button"
                  >
                    <svg
                      class="w-4 h-4"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2.5"
                      viewBox="0 0 24 24"
                    >
                      <circle
                        cx="12"
                        cy="12"
                        r="9"
                        stroke="currentColor"
                        stroke-width="2"
                      ></circle>
                      <path
                        d="M8 12l2.5 2.5L16 9"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      ></path>
                    </svg>
                  </button>
                  <!-- External Link Amber -->
                  <button
                    class="text-amber-500 hover:text-amber-600 p-1 hover:scale-115 active:scale-95 transition-transform"
                    title="Detail"
                    type="button"
                  >
                    <svg
                      class="w-4 h-4 stroke-2"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      ></path>
                    </svg>
                  </button>
                  <!-- Lock Black -->
                  <button
                    class="text-slate-700 hover:text-slate-900 p-1 hover:scale-115 active:scale-95 transition-transform"
                    title="Kunci"
                    type="button"
                  >
                    <svg
                      class="w-4 h-4 stroke-2"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <rect height="10" rx="2" width="14" x="5" y="11"></rect>
                      <path d="M8 11V7a4 4 0 018 0v4"></path>
                    </svg>
                  </button>
                  <!-- Three Dots Vertical -->
                  <button
                    class="text-slate-400 hover:text-slate-700 p-1 hover:scale-115 active:scale-95 transition-transform"
                    title="Menu Lain"
                    type="button"
                  >
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                      <path
                        d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"
                      ></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            <!-- Row 2: PGJ-002 Sukses -->
            <tr class="hover:bg-blue-50/40 transition-colors duration-150">
              <td class="py-4 px-5 text-center">
                <input
                  class="rounded border-slate-300 text-blue-600 focus:ring-0 focus:ring-offset-0"
                  type="checkbox"
                />
              </td>
              <td class="py-4 px-4 font-medium text-slate-800">PGJ-002</td>
              <td class="py-4 px-4 text-slate-600 font-mono text-xs">0192648989</td>
              <td class="py-4 px-4 text-slate-800 font-medium">
                Muhammad Ulil
              </td>
              <td class="py-4 px-4">
                <span
                  class="inline-block px-3 py-1 rounded-md border border-blue-400 text-blue-600 text-[11px] font-semibold bg-blue-50/50"
                >
                  Permintaan
                </span>
              </td>
              <td class="py-4 px-4 text-slate-700">Bulpoin</td>
              <td class="py-4 px-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-semibold">
                  <span class="relative flex h-1.5 w-1.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                  </span>
                  Sukses
                </span>
              </td>
              <td class="py-4 px-4">
                <div class="flex items-center justify-center gap-2.5">
                  <button
                    class="text-emerald-500 hover:text-emerald-600 p-1 hover:scale-115 active:scale-95 transition-transform"
                    title="Setujui"
                    type="button"
                  >
                    <svg
                      class="w-4 h-4"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2.5"
                      viewBox="0 0 24 24"
                    >
                      <circle
                        cx="12"
                        cy="12"
                        r="9"
                        stroke="currentColor"
                        stroke-width="2"
                      ></circle>
                      <path
                        d="M8 12l2.5 2.5L16 9"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      ></path>
                    </svg>
                  </button>
                  <button
                    class="text-amber-500 hover:text-amber-600 p-1 hover:scale-115 active:scale-95 transition-transform"
                    title="Detail"
                    type="button"
                  >
                    <svg
                      class="w-4 h-4 stroke-2"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      ></path>
                    </svg>
                  </button>
                  <button
                    class="text-slate-700 hover:text-slate-900 p-1 hover:scale-115 active:scale-95 transition-transform"
                    title="Kunci"
                    type="button"
                  >
                    <svg
                      class="w-4 h-4 stroke-2"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <rect height="10" rx="2" width="14" x="5" y="11"></rect>
                      <path d="M8 11V7a4 4 0 018 0v4"></path>
                    </svg>
                  </button>
                  <button
                    class="text-slate-400 hover:text-slate-700 p-1 hover:scale-115 active:scale-95 transition-transform"
                    title="Menu Lain"
                    type="button"
                  >
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                      <path
                        d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"
                      ></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            <!-- Row 3: PGJ-003 Di Tolak -->
            <tr class="hover:bg-blue-50/40 transition-colors duration-150">
              <td class="py-4 px-5 text-center">
                <input
                  class="rounded border-slate-300 text-blue-600 focus:ring-0 focus:ring-offset-0"
                  type="checkbox"
                />
              </td>
              <td class="py-4 px-4 font-medium text-slate-800">PGJ-003</td>
              <td class="py-4 px-4 text-slate-600 font-mono text-xs">0192648989</td>
              <td class="py-4 px-4 text-slate-800 font-medium">
                Muhammad Ulil
              </td>
              <td class="py-4 px-4">
                <span
                  class="inline-block px-3 py-1 rounded-md border border-blue-400 text-blue-600 text-[11px] font-semibold bg-blue-50/50"
                >
                  Permintaan
                </span>
              </td>
              <td class="py-4 px-4 text-slate-700">Bulpoin</td>
              <td class="py-4 px-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-rose-50 text-rose-700 border border-rose-200 text-[11px] font-semibold">
                  <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                  Di Tolak
                </span>
              </td>
              <td class="py-4 px-4">
                <div class="flex items-center justify-center gap-2.5">
                  <button
                    class="text-emerald-500 hover:text-emerald-600 p-1 hover:scale-115 active:scale-95 transition-transform"
                    title="Setujui"
                    type="button"
                  >
                    <svg
                      class="w-4 h-4"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2.5"
                      viewBox="0 0 24 24"
                    >
                      <circle
                        cx="12"
                        cy="12"
                        r="9"
                        stroke="currentColor"
                        stroke-width="2"
                      ></circle>
                      <path
                        d="M8 12l2.5 2.5L16 9"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      ></path>
                    </svg>
                  </button>
                  <button
                    class="text-amber-500 hover:text-amber-600 p-1 hover:scale-115 active:scale-95 transition-transform"
                    title="Detail"
                    type="button"
                  >
                    <svg
                      class="w-4 h-4 stroke-2"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      ></path>
                    </svg>
                  </button>
                  <button
                    class="text-slate-700 hover:text-slate-900 p-1 hover:scale-115 active:scale-95 transition-transform"
                    title="Kunci"
                    type="button"
                  >
                    <svg
                      class="w-4 h-4 stroke-2"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <rect height="10" rx="2" width="14" x="5" y="11"></rect>
                      <path d="M8 11V7a4 4 0 018 0v4"></path>
                    </svg>
                  </button>
                  <button
                    class="text-slate-400 hover:text-slate-700 p-1 hover:scale-115 active:scale-95 transition-transform"
                    title="Menu Lain"
                    type="button"
                  >
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                      <path
                        d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"
                      ></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            <!-- Row 4: PHJ-004 Di Tolak -->
            <tr class="hover:bg-blue-50/40 transition-colors duration-150">
              <td class="py-4 px-5 text-center">
                <input
                  class="rounded border-slate-300 text-blue-600 focus:ring-0 focus:ring-offset-0"
                  type="checkbox"
                />
              </td>
              <td class="py-4 px-4 font-medium text-slate-800">PHJ-004</td>
              <td class="py-4 px-4 text-slate-600 font-mono text-xs">0192648989</td>
              <td class="py-4 px-4 text-slate-800 font-medium">
                Muhammad Ulil
              </td>
              <td class="py-4 px-4">
                <span
                  class="inline-block px-3 py-1 rounded-md border border-blue-400 text-blue-600 text-[11px] font-semibold bg-blue-50/50"
                >
                  Permintaan
                </span>
              </td>
              <td class="py-4 px-4 text-slate-700">Bulpoin</td>
              <td class="py-4 px-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-rose-50 text-rose-700 border border-rose-200 text-[11px] font-semibold">
                  <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                  Di Tolak
                </span>
              </td>
              <td class="py-4 px-4">
                <div class="flex items-center justify-center gap-2.5">
                  <button
                    class="text-emerald-500 hover:text-emerald-600 p-1 hover:scale-115 active:scale-95 transition-transform"
                    title="Setujui"
                    type="button"
                  >
                    <svg
                      class="w-4 h-4"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2.5"
                      viewBox="0 0 24 24"
                    >
                      <circle
                        cx="12"
                        cy="12"
                        r="9"
                        stroke="currentColor"
                        stroke-width="2"
                      ></circle>
                      <path
                        d="M8 12l2.5 2.5L16 9"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      ></path>
                    </svg>
                  </button>
                  <button
                    class="text-amber-500 hover:text-amber-600 p-1 hover:scale-115 active:scale-95 transition-transform"
                    title="Detail"
                    type="button"
                  >
                    <svg
                      class="w-4 h-4 stroke-2"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      ></path>
                    </svg>
                  </button>
                  <button
                    class="text-slate-700 hover:text-slate-900 p-1 hover:scale-115 active:scale-95 transition-transform"
                    title="Kunci"
                    type="button"
                  >
                    <svg
                      class="w-4 h-4 stroke-2"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <rect height="10" rx="2" width="14" x="5" y="11"></rect>
                      <path d="M8 11V7a4 4 0 018 0v4"></path>
                    </svg>
                  </button>
                  <button
                    class="text-slate-400 hover:text-slate-700 p-1 hover:scale-115 active:scale-95 transition-transform"
                    title="Menu Lain"
                    type="button"
                  >
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                      <path
                        d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"
                      ></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>
<!-- END: DashboardContextContent -->
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  (function() {
    // 1. Numerical Counter Animation (Pure JS CountUp - 60fps)
    function initCounters() {
      const counters = document.querySelectorAll('.dash-counter');
      counters.forEach(counter => {
        const target = parseFloat(counter.getAttribute('data-target') || '0');
        const isFormatted = counter.hasAttribute('data-format-number');
        const duration = 1200;
        const startTime = performance.now();

        function step(now) {
          const elapsed = now - startTime;
          const progress = Math.min(elapsed / duration, 1);
          // Cubic ease-out: smooth decelerating feel
          const easeProgress = 1 - Math.pow(1 - progress, 3);
          const current = Math.floor(easeProgress * target);

          if (isFormatted) {
            counter.textContent = new Intl.NumberFormat('id-ID').format(current);
          } else {
            counter.textContent = current;
          }

          if (progress < 1) {
            requestAnimationFrame(step);
          } else {
            counter.textContent = isFormatted
              ? new Intl.NumberFormat('id-ID').format(target)
              : target;
          }
        }

        requestAnimationFrame(step);
      });
    }

    // 2. Circular Gauge SVG Stroke Animation
    function initGauges() {
      setTimeout(() => {
        document.querySelectorAll('.dash-gauge-circle').forEach(circle => {
          const targetOffset = circle.getAttribute('data-target-offset');
          if (targetOffset !== null) {
            circle.style.strokeDashoffset = targetOffset;
          }
        });
      }, 150);
    }

    // 3. ApexCharts Initialization
    function initCharts() {
      if (typeof ApexCharts === 'undefined') {
        console.warn('ApexCharts library is still loading or offline. Skipping chart initialization.');
        return;
      }

      // Chart 1: Monthly Recap Column Chart
      const monthlyEl = document.querySelector("#monthlyRecapChart");
      if (monthlyEl && !monthlyEl.hasAttribute('data-rendered')) {
        monthlyEl.setAttribute('data-rendered', 'true');
        const monthlyOptions = {
          series: [{
            name: 'Distribusi / Pengajuan',
            data: [42, 68, 55, 78, 90, 82, 64, 75, 88, 70, 85, 94]
          }],
          chart: {
            type: 'bar',
            height: 220,
            fontFamily: 'Plus Jakarta Sans, sans-serif',
            toolbar: { show: false },
            animations: {
              enabled: true,
              easing: 'easeinout',
              speed: 750,
              animateGradually: {
                enabled: true,
                delay: 100
              }
            }
          },
          plotOptions: {
            bar: {
              borderRadius: 6,
              columnWidth: '38%',
              distributed: false
            }
          },
          colors: ['#2563eb'],
          fill: {
            type: 'gradient',
            gradient: {
              shade: 'light',
              type: 'vertical',
              shadeIntensity: 0.25,
              gradientToColors: ['#60a5fa'],
              inverseColors: false,
              opacityFrom: 1,
              opacityTo: 0.85,
              stops: [0, 100]
            }
          },
          dataLabels: { enabled: false },
          xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
              style: {
                colors: '#64748b',
                fontSize: '11px',
                fontWeight: 600
              }
            }
          },
          yaxis: {
            labels: {
              formatter: val => Math.round(val),
              style: { colors: '#94a3b8', fontSize: '10px' }
            }
          },
          grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 4,
            padding: { left: 0, right: 0, top: -10, bottom: 0 }
          },
          tooltip: {
            theme: 'light',
            y: {
              formatter: val => `${val} Pengajuan ATK`
            }
          }
        };
        const monthlyChart = new ApexCharts(monthlyEl, monthlyOptions);
        monthlyChart.render();
      }

      // Chart 2: Sparkline Borrow (Emerald)
      const borrowEl = document.querySelector("#sparklineBorrow");
      if (borrowEl && !borrowEl.hasAttribute('data-rendered')) {
        borrowEl.setAttribute('data-rendered', 'true');
        const borrowOptions = {
          series: [{
            name: 'Peminjaman',
            data: [18, 25, 22, 38, 30, 48, 42, 60, 55, 75]
          }],
          chart: {
            type: 'area',
            height: 48,
            sparkline: { enabled: true },
            animations: {
              enabled: true,
              easing: 'easeinout',
              speed: 800
            }
          },
          stroke: {
            curve: 'smooth',
            width: 2.2,
            colors: ['#10b981']
          },
          fill: {
            type: 'gradient',
            gradient: {
              shadeIntensity: 1,
              opacityFrom: 0.45,
              opacityTo: 0.05,
              stops: [0, 100],
              colorStops: [
                { offset: 0, color: '#10b981', opacity: 0.45 },
                { offset: 100, color: '#10b981', opacity: 0.02 }
              ]
            }
          },
          colors: ['#10b981'],
          tooltip: {
            theme: 'light',
            fixed: { enabled: false },
            x: { show: false },
            y: {
              title: { formatter: () => 'Aktivitas: ' }
            },
            marker: { show: false }
          }
        };
        const borrowChart = new ApexCharts(borrowEl, borrowOptions);
        borrowChart.render();
      }

      // Chart 3: Sparkline Reduced (Rose)
      const reducedEl = document.querySelector("#sparklineReduced");
      if (reducedEl && !reducedEl.hasAttribute('data-rendered')) {
        reducedEl.setAttribute('data-rendered', 'true');
        const reducedOptions = {
          series: [{
            name: 'Pengurangan',
            data: [45, 40, 48, 35, 32, 28, 25, 20, 24, 16]
          }],
          chart: {
            type: 'area',
            height: 48,
            sparkline: { enabled: true },
            animations: {
              enabled: true,
              easing: 'easeinout',
              speed: 800
            }
          },
          stroke: {
            curve: 'smooth',
            width: 2.2,
            colors: ['#f43f5e']
          },
          fill: {
            type: 'gradient',
            gradient: {
              shadeIntensity: 1,
              opacityFrom: 0.45,
              opacityTo: 0.05,
              stops: [0, 100],
              colorStops: [
                { offset: 0, color: '#f43f5e', opacity: 0.45 },
                { offset: 100, color: '#f43f5e', opacity: 0.02 }
              ]
            }
          },
          colors: ['#f43f5e'],
          tooltip: {
            theme: 'light',
            fixed: { enabled: false },
            x: { show: false },
            y: {
              title: { formatter: () => 'Aktivitas: ' }
            },
            marker: { show: false }
          }
        };
        const reducedChart = new ApexCharts(reducedEl, reducedOptions);
        reducedChart.render();
      }

      // Chart 4: Right Accent Card - RadialBar Stock Health
      const healthEl = document.querySelector("#stockHealthChart");
      if (healthEl && !healthEl.hasAttribute('data-rendered')) {
        healthEl.setAttribute('data-rendered', 'true');
        const healthPercent = parseInt(healthEl.getAttribute('data-health-rate') || '100');
        const healthOptions = {
          series: [healthPercent],
          chart: {
            height: 165,
            type: 'radialBar',
            sparkline: { enabled: true },
            animations: {
              enabled: true,
              easing: 'easeinout',
              speed: 950
            }
          },
          plotOptions: {
            radialBar: {
              startAngle: -125,
              endAngle: 125,
              hollow: {
                margin: 0,
                size: '64%',
                background: 'transparent'
              },
              track: {
                background: 'rgba(255, 255, 255, 0.15)',
                strokeWidth: '100%',
                margin: 0
              },
              dataLabels: {
                name: {
                  show: true,
                  offsetY: 18,
                  color: '#93c5fd',
                  fontSize: '11px',
                  fontWeight: 600
                },
                value: {
                  offsetY: -10,
                  color: '#ffffff',
                  fontSize: '24px',
                  fontWeight: 800,
                  formatter: val => `${val}%`
                }
              }
            }
          },
          fill: {
            type: 'gradient',
            gradient: {
              shade: 'dark',
              type: 'horizontal',
              shadeIntensity: 0.5,
              gradientToColors: ['#38bdf8'],
              inverseColors: true,
              opacityFrom: 1,
              opacityTo: 1,
              stops: [0, 100]
            }
          },
          colors: ['#60a5fa'],
          labels: ['Kondisi Stok']
        };
        const healthChart = new ApexCharts(healthEl, healthOptions);
        healthChart.render();
      }
    }

    function initAll() {
      initCounters();
      initGauges();
      // Ensure ApexCharts script is loaded before initialization
      if (typeof ApexCharts !== 'undefined') {
        initCharts();
      } else {
        const checkApex = setInterval(() => {
          if (typeof ApexCharts !== 'undefined') {
            clearInterval(checkApex);
            initCharts();
          }
        }, 80);
        // Timeout safeguard
        setTimeout(() => clearInterval(checkApex), 3000);
      }
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initAll);
    } else {
      initAll();
    }
  })();
</script>
@endpush
