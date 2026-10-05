@extends('layouts.app')

@section('title', 'Audit Trail & Log Riwayat Aktivitas - BPTD Kelas II Jawa Timur')

@section('content')
<main class="main-content flex-1 max-w-430 w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

  <!-- ============================================================== -->
  <!-- 1. HEADER SECTION & EXPORT ACTIONS                             -->
  <!-- ============================================================== -->
  <div class="dash-hero-card bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-2xs">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      
      <!-- Sisi Kiri: Breadcrumb & Title -->
      <div class="space-y-1.5">
        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-semibold tracking-wide">
          <svg class="w-3.5 h-3.5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
          <span>Sistem Manajemen Persediaan ATK</span>
          <span>&bull;</span>
          <span class="text-blue-600 font-bold">Audit &amp; Kepatuhan SOP</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-snug">
          Audit Trail &amp; Log Riwayat Aktivitas
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 font-normal max-w-3xl">
          Pencatatan otomatis setiap mutasi stok, otorisasi transaksi distribusi SBPB, pengadaan, rekonsiliasi opname, hingga perubahan data master demi integritas dan transparansi audit BPTD.
        </p>
      </div>

      <!-- Sisi Kanan: Action Buttons (Excel & CSV) -->
      <div class="flex items-center gap-2 shrink-0 self-start sm:self-center flex-wrap">
        
        <!-- Tombol Unduh CSV -->
        <a
          href="{{ route('inventory.audit.export-csv', request()->query()) }}"
          class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-800 font-bold text-xs sm:text-sm transition border border-slate-200/80 cursor-pointer shadow-2xs"
          title="Unduh Data Mentah Log (CSV)"
        >
          <svg class="w-4 h-4 text-slate-600 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          <span>Unduh CSV</span>
        </a>

        <!-- Tombol Ekspor Excel (.xlsx A4) -->
        <a
          href="{{ route('inventory.audit.export-excel', request()->query()) }}"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs sm:text-sm transition shadow-xs cursor-pointer"
          title="Ekspor ke Format Microsoft Excel Asli (.xlsx) - Standar Cetak A4 Siap Edit"
        >
          <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span>Ekspor Excel (.xlsx)</span>
        </a>

      </div>

    </div>
  </div>

  <!-- ============================================================== -->
  <!-- 2. QUICK METRIC CARDS                                          -->
  <!-- ============================================================== -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    
    <!-- Card 1: Total Log Tercatat -->
    <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up">
      <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block truncate">Total Log Audit</span>
        <span class="text-lg font-black text-slate-900 tracking-tight block">{{ number_format($totalLogs) }} Rekaman</span>
        <span class="text-[11px] font-semibold text-blue-600 block truncate">Seluruh Aktivitas Sistem</span>
      </div>
    </div>

    <!-- Card 2: Mutasi Persediaan Fisik -->
    <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up anim-delay-100">
      <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block truncate">Mutasi Stok Fisik</span>
        <span class="text-lg font-black text-amber-700 tracking-tight block">{{ number_format($stockMutationLogs) }} Log</span>
        <span class="text-[11px] font-semibold text-amber-600 block truncate">In, Out, Adj &amp; Opname</span>
      </div>
    </div>

    <!-- Card 3: Aktivitas Hari Ini -->
    <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up anim-delay-150">
      <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block truncate">Aktivitas Hari Ini</span>
        <span class="text-lg font-black text-emerald-700 tracking-tight block">{{ number_format($todayLogs) }} Kejadian</span>
        <span class="text-[11px] font-semibold text-emerald-600 block truncate">Realtime Hari Berjalan</span>
      </div>
    </div>

    <!-- Card 4: Pengguna Beraktivitas -->
    <div class="dash-interactive-card bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 anim-fade-in-up anim-delay-200">
      <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block truncate">Operator Terdaftar</span>
        <span class="text-lg font-black text-purple-700 tracking-tight block">{{ number_format($uniqueUsersCount) }} Pengguna</span>
        <span class="text-[11px] font-semibold text-purple-600 block truncate">Pencatat Aktivitas</span>
      </div>
    </div>

  </div>

  <!-- ============================================================== -->
  <!-- 3. FILTER BAR & VIEW MODE SWITCHER                             -->
  <!-- ============================================================== -->
  <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs space-y-4">
    <form method="GET" action="{{ route('inventory.audit.index') }}" class="space-y-4">
      <input type="hidden" name="view" value="{{ $viewMode }}" />

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        
        <!-- Search Keyword -->
        <div class="lg:col-span-2">
          <label class="block text-xs font-bold text-slate-700 mb-1">Cari Keterangan / Pengguna / SKU</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              type="text"
              name="q"
              value="{{ $search }}"
              placeholder="Cari deskripsi, operator, NIP, no referensi..."
              class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-slate-50/50 hover:bg-white transition"
            />
          </div>
        </div>

        <!-- Filter Modul -->
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Modul Sistem</label>
          <select
            name="module"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-slate-50/50 hover:bg-white transition"
          >
            <option value="">Semua Modul</option>
            @foreach ($availableModules as $mod)
              <option value="{{ $mod }}" {{ $module === $mod ? 'selected' : '' }}>{{ $mod }}</option>
            @endforeach
          </select>
        </div>

        <!-- Filter Jenis Aksi -->
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Aksi</label>
          <select
            name="action"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-slate-50/50 hover:bg-white transition"
          >
            <option value="">Semua Aksi</option>
            @foreach ($availableActions as $key => $lbl)
              <option value="{{ $key }}" {{ $action === $key ? 'selected' : '' }}>{{ $lbl }}</option>
            @endforeach
          </select>
        </div>

        <!-- Filter Operator / User -->
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Pengguna / Operator</label>
          <select
            name="user_id"
            class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-slate-50/50 hover:bg-white transition"
          >
            <option value="">Semua Operator</option>
            @foreach ($users as $u)
              <option value="{{ $u->id }}" {{ (string)$userId === (string)$u->id ? 'selected' : '' }}>
                {{ $u->name }} ({{ $u->username }})
              </option>
            @endforeach
          </select>
        </div>

      </div>

      <!-- Baris Tanggal & Tombol Submit Filter -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2 border-t border-slate-100">
        
        <!-- Rentang Tanggal -->
        <div class="flex items-center gap-2 w-full sm:w-auto flex-wrap">
          <div class="flex items-center gap-1.5 text-xs text-slate-600 font-medium">
            <svg class="w-4 h-4 text-slate-400 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Periode:</span>
          </div>
          <input
            type="date"
            name="date_from"
            value="{{ $dateFrom }}"
            class="px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-blue-500 bg-slate-50/50"
          />
          <span class="text-xs text-slate-400">s/d</span>
          <input
            type="date"
            name="date_to"
            value="{{ $dateTo }}"
            class="px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-blue-500 bg-slate-50/50"
          />
        </div>

        <!-- Tombol Terapkan & Reset & View Mode -->
        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
          <button
            type="submit"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm transition shadow-2xs cursor-pointer"
          >
            <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <span>Terapkan Filter</span>
          </button>

          @if ($search || $module || $action || $userId || $dateFrom || $dateTo)
            <a
              href="{{ route('inventory.audit.index', ['view' => $viewMode]) }}"
              class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition border border-slate-200/80 cursor-pointer"
              title="Reset Semua Filter"
            >
              <svg class="w-3.5 h-3.5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              <span>Reset</span>
            </a>
          @endif

          <!-- Segmented Control View Switcher -->
          <div class="inline-flex items-center rounded-xl bg-slate-100 p-0.5 border border-slate-200/80 ml-2">
            <a
              href="{{ route('inventory.audit.index', array_merge(request()->query(), ['view' => 'table'])) }}"
              class="px-2.5 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition {{ $viewMode === 'table' ? 'bg-white text-blue-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"
              title="Tampilan Tabel Data Terstruktur"
            >
              <svg class="w-3.5 h-3.5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span>Tabel</span>
            </a>
            <a
              href="{{ route('inventory.audit.index', array_merge(request()->query(), ['view' => 'timeline'])) }}"
              class="px-2.5 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition {{ $viewMode === 'timeline' ? 'bg-white text-blue-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"
              title="Tampilan Timeline Kronologis Alur Waktu"
            >
              <svg class="w-3.5 h-3.5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>Timeline</span>
            </a>
          </div>

        </div>

      </div>
    </form>
  </div>

  <!-- ============================================================== -->
  <!-- 4. DATA LOGS PRESENTATION (TABEL ATAU TIMELINE)                -->
  <!-- ============================================================== -->
  @if ($logs->isEmpty())
    <!-- Empty State -->
    <div class="bg-white rounded-2xl p-12 text-center border border-slate-200/80 shadow-2xs space-y-3">
      <div class="w-14 h-14 mx-auto rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center">
        <svg class="w-7 h-7 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
      </div>
      <h3 class="text-base font-bold text-slate-800">Tidak Ada Rekaman Log Ditemukan</h3>
      <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
        Tidak ada data riwayat aktivitas yang sesuai dengan parameter filter pencarian Anda. Coba atur ulang tanggal atau reset kata kunci.
      </p>
      <div class="pt-2">
        <a
          href="{{ route('inventory.audit.index') }}"
          class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition"
        >
          <span>Tampilkan Semua Log</span>
        </a>
      </div>
    </div>
  @else

    @if ($viewMode === 'timeline')
      <!-- ============================================================ -->
      <!-- VIEW MODE A: VISUAL TIMELINE FEED                           -->
      <!-- ============================================================ -->
      <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-2xs space-y-6">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
          <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Feed Kronologis Aktivitas</h2>
          </div>
          <span class="text-xs font-semibold text-slate-400">Menampilkan {{ $logs->firstItem() }} - {{ $logs->lastItem() }} dari {{ $logs->total() }} Kejadian</span>
        </div>

        <!-- Timeline Stream Container -->
        <div class="relative pl-6 sm:pl-8 space-y-6 before:absolute before:left-3 sm:before:left-4 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
          
          @foreach ($logs as $log)
            <div class="relative group">
              <!-- Bullet Node Indicator -->
              <div class="absolute -left-6 sm:-left-8 top-1.5 w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-white border-2 border-slate-300 group-hover:border-blue-500 flex items-center justify-center transition shadow-2xs">
                <span class="w-2 h-2 rounded-full {{ str_contains($log->action_badge_class, 'emerald') ? 'bg-emerald-500' : (str_contains($log->action_badge_class, 'rose') ? 'bg-rose-500' : (str_contains($log->action_badge_class, 'amber') ? 'bg-amber-500' : 'bg-blue-500')) }}"></span>
              </div>

              <!-- Card Content -->
              <div class="bg-slate-50/70 hover:bg-white p-4 rounded-xl border border-slate-200/80 transition-all hover:shadow-2xs space-y-2.5">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                  <div class="flex items-center gap-2 flex-wrap">
                    <!-- Badge Aksi -->
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold border {{ $log->action_badge_class }}">
                      {{ $log->action_label }}
                    </span>
                    <!-- Badge Modul -->
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold border {{ $log->module_badge_class }}">
                      {{ $log->module }}
                    </span>
                    @if ($log->record_type)
                      <span class="text-[11px] font-mono font-medium text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">
                        {{ $log->record_type }} #{{ $log->record_id }}
                      </span>
                    @endif
                  </div>

                  <!-- Timestamp -->
                  <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium shrink-0">
                    <svg class="w-3.5 h-3.5 text-slate-400 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span title="{{ $log->created_at->format('d/m/Y H:i:s') }} WIB">{{ $log->created_at->diffForHumans() }}</span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="text-slate-400">{{ $log->created_at->format('H:i') }} WIB</span>
                  </div>
                </div>

                <!-- Description Text -->
                <p class="text-xs sm:text-sm text-slate-800 font-medium leading-relaxed">
                  {{ $log->description }}
                </p>

                <!-- Footer Metadata: User & IP & Inspect Button -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 border-t border-slate-200/60 text-xs text-slate-500">
                  <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 font-bold text-[10px] flex items-center justify-center shrink-0">
                      {{ substr($log->user_name ?: ($log->user?->name ?? 'U'), 0, 1) }}
                    </div>
                    <span class="font-bold text-slate-700">{{ $log->user_name ?: ($log->user?->name ?? 'Sistem') }}</span>
                    @if ($log->user_nip)
                      <span class="text-slate-400 text-[11px]">({{ $log->user_nip }})</span>
                    @endif
                    <span class="text-slate-300">&bull;</span>
                    <span class="font-mono text-[11px] text-slate-400">IP: {{ $log->ip_address ?: '127.0.0.1' }}</span>
                  </div>

                  <button
                    type="button"
                    onclick="inspectAuditLog({{ $log->id }})"
                    class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 font-bold text-xs hover:underline cursor-pointer self-start sm:self-auto"
                  >
                    <svg class="w-3.5 h-3.5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>Inspeksi Rincian &amp; Diff</span>
                  </button>
                </div>

              </div>
            </div>
          @endforeach

        </div>

        <!-- Pagination -->
        <div class="pt-4 border-t border-slate-100">
          {{ $logs->links() }}
        </div>

      </div>

    @else
      <!-- ============================================================ -->
      <!-- VIEW MODE B: STRUCTURED DATA TABLE (DEFAULT)                -->
      <!-- ============================================================ -->
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
        
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-600 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Tabel Log Riwayat Aktivitas</h2>
          </div>
          <span class="text-xs font-semibold text-slate-500">Menampilkan {{ $logs->firstItem() }} - {{ $logs->lastItem() }} dari {{ $logs->total() }} Log</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-240">
            <thead>
              <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-black text-slate-600 uppercase tracking-wider">
                <th class="py-3 px-3.5 text-center w-12">No</th>
                <th class="py-3 px-3.5 w-40">Waktu (WIB)</th>
                <th class="py-3 px-3.5 w-48">Pengguna / Operator</th>
                <th class="py-3 px-3.5 w-32">Modul</th>
                <th class="py-3 px-3.5 w-32">Jenis Aksi</th>
                <th class="py-3 px-3.5">Rincian Aktivitas</th>
                <th class="py-3 px-3.5 w-36">Objek Rekaman</th>
                <th class="py-3 px-3.5 w-28 text-center">IP Address</th>
                <th class="py-3 px-3.5 text-center w-24">Inspeksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
              @foreach ($logs as $index => $log)
                <tr class="hover:bg-blue-50/40 transition">
                  <!-- No -->
                  <td class="py-3 px-3.5 text-center font-mono text-slate-400 font-bold">
                    {{ $logs->firstItem() + $index }}
                  </td>

                  <!-- Waktu Kejadian -->
                  <td class="py-3 px-3.5 whitespace-nowrap">
                    <span class="font-bold text-slate-800 block">{{ $log->created_at->format('d/m/Y') }}</span>
                    <span class="font-mono text-[11px] text-slate-400 block">{{ $log->created_at->format('H:i:s') }} WIB</span>
                  </td>

                  <!-- Pengguna Pelaksana -->
                  <td class="py-3 px-3.5">
                    <div class="flex items-center gap-2">
                      <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center shrink-0">
                        {{ substr($log->user_name ?: ($log->user?->name ?? 'U'), 0, 1) }}
                      </div>
                      <div class="min-w-0">
                        <span class="font-bold text-slate-900 block truncate">{{ $log->user_name ?: ($log->user?->name ?? 'Sistem') }}</span>
                        <span class="text-[11px] font-mono text-slate-500 block truncate">{{ $log->user_nip ?: ($log->user?->nip ?? '-') }}</span>
                      </div>
                    </div>
                  </td>

                  <!-- Modul -->
                  <td class="py-3 px-3.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold border {{ $log->module_badge_class }}">
                      {{ $log->module }}
                    </span>
                  </td>

                  <!-- Jenis Aksi -->
                  <td class="py-3 px-3.5">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold border {{ $log->action_badge_class }}">
                      {{ $log->action_label }}
                    </span>
                  </td>

                  <!-- Deskripsi Aktivitas -->
                  <td class="py-3 px-3.5">
                    <span class="text-slate-800 font-medium line-clamp-2 leading-relaxed" title="{{ $log->description }}">
                      {{ $log->description }}
                    </span>
                  </td>

                  <!-- Objek Rekaman -->
                  <td class="py-3 px-3.5 whitespace-nowrap">
                    @if ($log->record_type)
                      <span class="font-mono text-[11px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 block truncate">
                        {{ $log->record_type }} #{{ $log->record_id }}
                      </span>
                    @else
                      <span class="text-slate-400 text-xs italic">-</span>
                    @endif
                  </td>

                  <!-- IP Address -->
                  <td class="py-3 px-3.5 text-center font-mono text-[11px] text-slate-500 whitespace-nowrap">
                    {{ $log->ip_address ?: '127.0.0.1' }}
                  </td>

                  <!-- Tombol Inspeksi -->
                  <td class="py-3 px-3.5 text-center">
                    <button
                      type="button"
                      onclick="inspectAuditLog({{ $log->id }})"
                      class="inline-flex items-center justify-center p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition cursor-pointer"
                      title="Lihat Detail Log & Perubahan Data (Diff)"
                    >
                      <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-slate-100">
          {{ $logs->links() }}
        </div>

      </div>
    @endif

  @endif

  <!-- ============================================================== -->
  <!-- 5. MODAL INSPEKSI LOG & DIFF VIEWER (AUDIT INSPECTOR)          -->
  <!-- ============================================================== -->
  <div
    id="audit-inspect-modal"
    class="fixed inset-0 z-99999 items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 overflow-y-auto hidden"
    aria-hidden="true"
  >
    <div class="bg-white rounded-2xl max-w-2xl w-full border border-slate-200 shadow-2xl overflow-hidden anim-scale-in my-8">
      
      <!-- Modal Header -->
      <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm sm:text-base font-bold text-white tracking-tight">Inspeksi Log Audit &amp; Jejak Nilai</h3>
            <span id="modal-log-id" class="text-xs text-slate-400 font-mono">Memuat data...</span>
          </div>
        </div>
        <button
          type="button"
          onclick="closeInspectModal()"
          class="w-8 h-8 rounded-lg bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 flex items-center justify-center transition cursor-pointer"
        >
          <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="p-6 space-y-5 max-h-155 overflow-y-auto">
        
        <!-- Metadata Ringkas Header -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 bg-slate-50 p-4 rounded-xl border border-slate-200/80">
          <div>
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Waktu Kejadian</span>
            <span id="modal-time" class="text-xs sm:text-sm font-black text-slate-900 block">-</span>
            <span id="modal-time-relative" class="text-[11px] text-blue-600 font-semibold block">-</span>
          </div>
          <div>
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Operator Pelaksana</span>
            <span id="modal-user-name" class="text-xs sm:text-sm font-black text-slate-900 block">-</span>
            <span id="modal-user-nip" class="text-[11px] text-slate-500 font-mono block">-</span>
          </div>
          <div>
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Modul &amp; Jenis Aksi</span>
            <div class="flex items-center gap-1.5 mt-0.5">
              <span id="modal-module" class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold border">-</span>
              <span id="modal-action" class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold border">-</span>
            </div>
          </div>
          <div>
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Koneksi &amp; Perangkat</span>
            <span id="modal-ip" class="text-xs font-mono text-slate-700 block">-</span>
            <span id="modal-agent" class="text-[11px] text-slate-500 truncate block">-</span>
          </div>
        </div>

        <!-- Deskripsi Transaksi -->
        <div class="space-y-1">
          <label class="text-xs font-bold text-slate-700">Rincian Narasi Log</label>
          <div id="modal-description" class="p-3 bg-white rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 leading-relaxed font-medium">
            -
          </div>
        </div>

        <!-- Diff Viewer: Perbandingan Nilai Sebelum vs Sesudah -->
        <div id="modal-diff-container" class="space-y-2">
          <div class="flex items-center justify-between">
            <label class="text-xs font-bold text-slate-700">Snapshot Nilai Data (Before / After)</label>
            <span class="text-[11px] text-slate-400 font-mono" id="modal-diff-badge">Terverifikasi</span>
          </div>

          <div id="modal-diff-body" class="rounded-xl border border-slate-200 overflow-hidden text-xs">
            <!-- Diisi secara dinamis oleh JavaScript -->
          </div>
        </div>

      </div>

      <!-- Modal Footer -->
      <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-end">
        <button
          type="button"
          onclick="closeInspectModal()"
          class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs transition cursor-pointer"
        >
          Tutup Rincian
        </button>
      </div>

    </div>
  </div>

</main>
@endsection

@push('scripts')
<script>
  function inspectAuditLog(logId) {
    const modal = document.getElementById('audit-inspect-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    document.getElementById('modal-log-id').textContent = 'Memuat rincian ID #' + logId + '...';
    document.getElementById('modal-diff-body').innerHTML = '<div class="p-4 text-center text-slate-400 text-xs">Mengambil snapshot audit dari server...</div>';

    fetch('/inventory/audit/' + logId)
      .then(res => res.json())
      .then(res => {
        if (!res.success) {
          alert('Gagal memuat log audit: ' + (res.message || 'Terjadi kesalahan'));
          closeInspectModal();
          return;
        }

        const data = res.data;
        document.getElementById('modal-log-id').textContent = 'Log Audit ID #' + data.id + ' • ' + (data.record_type ? data.record_type + ' #' + data.record_id : 'Sistem Event');
        document.getElementById('modal-time').textContent = data.created_at_formatted;
        document.getElementById('modal-time-relative').textContent = data.created_at_relative;
        document.getElementById('modal-user-name').textContent = data.user.name;
        document.getElementById('modal-user-nip').textContent = 'NIP: ' + data.user.nip + ' (' + data.user.role + ')';
        
        // Modul & Aksi Badges
        const modEl = document.getElementById('modal-module');
        modEl.textContent = data.module;
        modEl.className = 'inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold border ' + data.module_badge_class;

        const actEl = document.getElementById('modal-action');
        actEl.textContent = data.action_label;
        actEl.className = 'inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold border ' + data.action_badge_class;

        document.getElementById('modal-ip').textContent = 'IP: ' + data.ip_address;
        document.getElementById('modal-agent').textContent = data.user_agent;
        document.getElementById('modal-description').textContent = data.description;

        // Render Diff or Values
        renderDiff(data);
      })
      .catch(err => {
        console.error(err);
        document.getElementById('modal-diff-body').innerHTML = '<div class="p-4 text-center text-rose-500 text-xs">Gagal mengambil rincian log audit. Silakan coba kembali.</div>';
      });
  }

  function renderDiff(data) {
    const diffBody = document.getElementById('modal-diff-body');
    const oldVals = data.old_values;
    const newVals = data.new_values;

    if (!oldVals && !newVals) {
      diffBody.innerHTML = '<div class="p-4 text-center text-slate-400 text-xs italic">Tidak ada payload perubahan data tersimpan pada transaksi ini.</div>';
      return;
    }

    // Jika memiliki perbandingan Old vs New (misal update status atau edit master)
    if (oldVals && newVals) {
      let rowsHtml = '';
      const allKeys = Array.from(new Set([...Object.keys(oldVals), ...Object.keys(newVals)]));

      rowsHtml += `
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-100 text-[11px] font-bold text-slate-600 border-b border-slate-200">
              <th class="py-2 px-3 w-1/3">Field / Atribut</th>
              <th class="py-2 px-3 w-1/3 text-rose-700 bg-rose-50/60">Sebelum (Old Value)</th>
              <th class="py-2 px-3 w-1/3 text-emerald-700 bg-emerald-50/60">Sesudah (New Value)</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
      `;

      allKeys.forEach(k => {
        const vOld = oldVals[k] !== undefined ? JSON.stringify(oldVals[k]) : '-';
        const vNew = newVals[k] !== undefined ? JSON.stringify(newVals[k]) : '-';
        const isChanged = vOld !== vNew;

        rowsHtml += `
          <tr class="${isChanged ? 'bg-amber-50/20' : ''}">
            <td class="py-2 px-3 font-mono text-[11px] font-bold text-slate-700">${k}</td>
            <td class="py-2 px-3 font-mono text-[11px] text-rose-700 bg-rose-50/30 break-all">${vOld}</td>
            <td class="py-2 px-3 font-mono text-[11px] text-emerald-700 bg-emerald-50/30 font-bold break-all">${vNew}</td>
          </tr>
        `;
      });

      rowsHtml += `</tbody></table>`;
      diffBody.innerHTML = rowsHtml;
      return;
    }

    // Jika hanya memiliki new_values (misal saat Create atau ringkasan item transaksi)
    const vals = newVals || oldVals;
    let listHtml = `
      <div class="p-3 bg-slate-50 space-y-1.5 font-mono text-[11px]">
        <div class="font-bold text-slate-700 border-b border-slate-200 pb-1">Payload Atribut Terlampir:</div>
    `;

    for (const [k, v] of Object.entries(vals)) {
      const valStr = typeof v === 'object' ? JSON.stringify(v, null, 2) : String(v);
      listHtml += `
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-1 py-1 border-b border-slate-200/50">
          <span class="text-blue-700 font-semibold shrink-0">${k}:</span>
          <span class="text-slate-800 break-all font-medium">${valStr}</span>
        </div>
      `;
    }

    listHtml += `</div>`;
    diffBody.innerHTML = listHtml;
  }

  function closeInspectModal() {
    const modal = document.getElementById('audit-inspect-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  // Close modal on escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeInspectModal();
    }
  });

  // Close on outside click
  document.getElementById('audit-inspect-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
      closeInspectModal();
    }
  });
</script>
@endpush
