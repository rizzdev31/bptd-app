@extends('layouts.app')

@section('title', 'Master Pegawai - BPTD Kelas II Jawa Timur')

@section('content')
<main class="flex-1 max-w-430 w-full mx-auto px-4 sm:px-6 lg:px-8 py-7 space-y-7">
  <!-- Header Section -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="space-y-1">
      <div class="flex items-center gap-2">
        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700 uppercase tracking-wider">
          Master Data
        </span>
        <span class="text-xs text-slate-400">•</span>
        <span class="text-xs text-slate-500 font-medium">BPTD Kelas II Jawa Timur</span>
      </div>
      <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
        Master Pegawai & Penerima ATK
      </h2>
      <p class="text-xs sm:text-sm text-slate-500 max-w-2xl">
        Kelola basis data pegawai seluruh unit kerja BPTD Jatim untuk pencatatan penerima distribusi ATK serta pengelolaan hak akses peran sistem.
      </p>
    </div>

    <!-- Add Button Trigger -->
    <div class="flex items-center gap-3">
      <button
        type="button"
        onclick="openCreateModal()"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#007BFB] hover:bg-[#006CE0] active:scale-95 text-white font-semibold text-sm rounded-xl shadow-btn transition-all cursor-pointer"
      >
        <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        <span>Tambah Pegawai</span>
      </button>
    </div>
  </div>

  <!-- 4 Summary Metric Cards (Matching Dashboard Aesthetics) -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
    <!-- Card 1: Total Pegawai -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4 hover:shadow-md transition">
      <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
        <svg class="w-6 h-6 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
      </div>
      <div>
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Pegawai</p>
        <h3 class="text-2xl font-black text-slate-900 tracking-tight">{{ $totalEmployees }}</h3>
        <p class="text-[11px] text-slate-500 font-medium mt-0.5">Pegawai BPTD Terdaftar</p>
      </div>
    </div>

    <!-- Card 2: Pegawai Aktif -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4 hover:shadow-md transition">
      <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
        <svg class="w-6 h-6 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <div>
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pegawai Aktif</p>
        <h3 class="text-2xl font-black text-emerald-600 tracking-tight">{{ $activeEmployees }}</h3>
        <p class="text-[11px] text-slate-500 font-medium mt-0.5">Siap Menerima Distribusi ATK</p>
      </div>
    </div>

    <!-- Card 3: Akun Pengelola Sistem -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4 hover:shadow-md transition">
      <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
        <svg class="w-6 h-6 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
      </div>
      <div>
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Akun Pengelola</p>
        <h3 class="text-2xl font-black text-slate-900 tracking-tight">{{ $systemUsers }}</h3>
        <p class="text-[11px] text-slate-500 font-medium mt-0.5">Superadmin & Petugas ATK</p>
      </div>
    </div>

    <!-- Card 4: Unit Kerja -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4 hover:shadow-md transition">
      <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
        <svg class="w-6 h-6 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
      </div>
      <div>
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Unit Kerja</p>
        <h3 class="text-2xl font-black text-slate-900 tracking-tight">{{ $totalUnits }}</h3>
        <p class="text-[11px] text-slate-500 font-medium mt-0.5">Seksi / Subbag Kerja</p>
      </div>
    </div>
  </div>

  <!-- Filter & Search Toolbar -->
  <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm">
    <form method="GET" action="{{ route('pegawai.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
      <!-- Search Input -->
      <div class="sm:col-span-5 relative">
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
          <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
        </div>
        <input
          type="text"
          name="keyword"
          value="{{ request('keyword') }}"
          placeholder="Cari Nama Pegawai, NIP, atau Jabatan..."
          class="field-input block w-full pl-10 pr-4 py-2.5 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none"
        />
      </div>

      <!-- Filter Unit Kerja -->
      <div class="sm:col-span-4">
        <select
          name="work_unit_id"
          class="field-input block w-full px-3.5 py-2.5 rounded-xl text-xs sm:text-sm text-slate-700 focus:outline-none cursor-pointer"
        >
          <option value="">Semua Unit Kerja BPTD</option>
          @foreach ($workUnits as $unit)
            <option value="{{ $unit->id }}" {{ request('work_unit_id') == $unit->id ? 'selected' : '' }}>
              {{ $unit->name }} ({{ $unit->code }})
            </option>
          @endforeach
        </select>
      </div>

      <!-- Filter Status -->
      <div class="sm:col-span-2">
        <select
          name="status"
          class="field-input block w-full px-3.5 py-2.5 rounded-xl text-xs sm:text-sm text-slate-700 focus:outline-none cursor-pointer"
        >
          <option value="">Semua Status</option>
          <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
          <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        </select>
      </div>

      <!-- Buttons -->
      <div class="sm:col-span-1 flex items-center gap-1.5">
        <button
          type="submit"
          class="w-full py-2.5 px-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold flex items-center justify-center transition"
          title="Terapkan Filter"
        >
          Filter
        </button>
        @if (request()->hasAny(['keyword', 'work_unit_id', 'status']))
          <a
            href="{{ route('pegawai.index') }}"
            class="py-2.5 px-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold flex items-center justify-center transition"
            title="Reset Pencarian"
          >
            ✕
          </a>
        @endif
      </div>
    </form>
  </div>

  <!-- Data Table Section -->
  <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500 select-none">
            <th class="py-3.5 px-4 sm:px-6 w-14 text-center">No</th>
            <th class="py-3.5 px-4 sm:px-6">Identitas Pegawai</th>
            <th class="py-3.5 px-4 sm:px-6">Unit Kerja</th>
            <th class="py-3.5 px-4 sm:px-6">Jabatan</th>
            <th class="py-3.5 px-4 sm:px-6 text-center">Peran Sistem</th>
            <th class="py-3.5 px-4 sm:px-6 text-center">Status</th>
            <th class="py-3.5 px-4 sm:px-6 text-center w-28">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
          @forelse ($employees as $index => $emp)
            <tr class="hover:bg-slate-50/80 transition-colors">
              <!-- No -->
              <td class="py-4 px-4 sm:px-6 text-center font-mono text-xs text-slate-400">
                {{ $employees->firstItem() + $index }}
              </td>

              <!-- Identitas Pegawai -->
              <td class="py-4 px-4 sm:px-6">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-full bg-blue-100 text-[#007BFB] flex items-center justify-center font-bold text-xs shrink-0 select-none">
                    {{ strtoupper(substr($emp->name, 0, 2)) }}
                  </div>
                  <div>
                    <span class="font-bold text-slate-900 block leading-tight">{{ $emp->name }}</span>
                    <span class="text-xs text-slate-500 font-mono tracking-tight flex items-center gap-1.5 mt-0.5">
                      <span class="text-slate-400">NIP:</span> {{ $emp->nip }}
                    </span>
                  </div>
                </div>
              </td>

              <!-- Unit Kerja -->
              <td class="py-4 px-4 sm:px-6">
                <span class="font-medium text-slate-800 block">
                  {{ $emp->workUnit?->name ?? '-' }}
                </span>
                @if ($emp->workUnit?->code)
                  <span class="inline-block mt-0.5 text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-semibold">
                    {{ $emp->workUnit->code }}
                  </span>
                @endif
              </td>

              <!-- Jabatan -->
              <td class="py-4 px-4 sm:px-6 text-slate-600">
                {{ $emp->position ?? 'Staf Pelaksana' }}
              </td>

              <!-- Peran Sistem -->
              <td class="py-4 px-4 sm:px-6 text-center">
                @if ($emp->user && $emp->user->role)
                  @if ($emp->user->role->name === 'superadmin')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 shadow-pill">
                      <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                      Superadmin
                    </span>
                  @elseif ($emp->user->role->name === 'petugas')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 shadow-pill">
                      <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                      Petugas ATK
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200 shadow-pill">
                      {{ $emp->user->role->label }}
                    </span>
                  @endif
                @else
                  <span class="text-slate-400 text-xs italic font-normal">Penerima ATK</span>
                @endif
              </td>

              <!-- Status -->
              <td class="py-4 px-4 sm:px-6 text-center">
                @if ($emp->status === 'active')
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Aktif
                  </span>
                @else
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                    Nonaktif
                  </span>
                @endif
              </td>

              <!-- Aksi -->
              <td class="py-4 px-4 sm:px-6 text-center">
                <div class="inline-flex items-center gap-1.5">
                  <!-- Edit Button -->
                  <button
                    type="button"
                    onclick='openEditModal(@json($emp))'
                    class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition"
                    title="Ubah Data Pegawai"
                  >
                    <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
                    </svg>
                  </button>

                  <!-- Toggle Inactive/Active Button -->
                  <form
                    action="{{ route('pegawai.destroy', $emp->id) }}"
                    method="POST"
                    class="inline"
                    onsubmit="window.confirmSubmit(event, {
                      title: '{{ $emp->status === 'active' ? 'Nonaktifkan Pegawai' : 'Aktifkan Pegawai' }}',
                      message: '{{ $emp->status === 'active' ? 'Pegawai yang dinonaktifkan tidak dapat dipilih saat pencatatan distribusi ATK baru.' : 'Pegawai akan diaktifkan kembali dan dapat menerima distribusi ATK.' }}',
                      badgeText: '{{ addslashes($emp->name) }} (NIP: {{ $emp->nip }})',
                      type: '{{ $emp->status === 'active' ? 'warning' : 'primary' }}',
                      confirmText: '{{ $emp->status === 'active' ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}'
                    })"
                  >
                    @csrf
                    @method('DELETE')
                    <button
                      type="submit"
                      class="p-1.5 rounded-lg {{ $emp->status === 'active' ? 'text-slate-500 hover:text-red-600 hover:bg-red-50' : 'text-slate-500 hover:text-emerald-600 hover:bg-emerald-50' }} transition"
                      title="{{ $emp->status === 'active' ? 'Nonaktifkan Pegawai' : 'Aktifkan Pegawai' }}"
                    >
                      @if ($emp->status === 'active')
                        <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                      @else
                        <svg class="w-4 h-4 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                      @endif
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-12 px-6 text-center text-slate-400">
                <div class="flex flex-col items-center justify-center gap-2">
                  <svg class="w-10 h-10 text-slate-300 stroke-[1.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                  </svg>
                  <p class="font-semibold text-slate-600 text-sm">Tidak ada data pegawai yang ditemukan</p>
                  <p class="text-xs text-slate-400">Silakan gunakan kata kunci lain atau tambahkan data pegawai baru.</p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    @if ($employees->hasPages())
      <div class="p-4 border-t border-slate-100 flex items-center justify-between">
        {{ $employees->links() }}
      </div>
    @endif
  </div>
</main>

<!-- MODAL: Tambah Pegawai -->
<div id="create-modal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs hidden items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-100 p-6 sm:p-8 animate-in fade-in zoom-in-95 duration-200">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <div>
        <h3 class="text-lg font-bold text-slate-900">Tambah Data Pegawai Baru</h3>
        <p class="text-xs text-slate-500">Daftarkan pegawai BPTD Kelas II Jawa Timur</p>
      </div>
      <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1">&times;</button>
    </div>

    <form action="{{ route('pegawai.store') }}" method="POST" class="space-y-4 pt-4">
      @csrf

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- NIP -->
        <div class="space-y-1">
          <label class="block text-xs font-semibold text-slate-700">NIP Pegawai : <span class="text-red-500">*</span></label>
          <input type="text" name="nip" required placeholder="Contoh: 198501012010121001" class="field-input block w-full px-3.5 py-2.5 rounded-xl text-xs sm:text-sm text-slate-900 focus:outline-none"/>
        </div>

        <!-- Nama Lengkap -->
        <div class="space-y-1">
          <label class="block text-xs font-semibold text-slate-700">Nama Lengkap & Gelar : <span class="text-red-500">*</span></label>
          <input type="text" name="name" required placeholder="Contoh: Ahmad Fauzi, S.T." class="field-input block w-full px-3.5 py-2.5 rounded-xl text-xs sm:text-sm text-slate-900 focus:outline-none"/>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Unit Kerja -->
        <div class="space-y-1">
          <label class="block text-xs font-semibold text-slate-700">Unit Kerja : <span class="text-red-500">*</span></label>
          <select name="work_unit_id" required class="field-input block w-full px-3.5 py-2.5 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none cursor-pointer">
            <option value="">-- Pilih Unit Kerja --</option>
            @foreach ($workUnits as $unit)
              <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->code }})</option>
            @endforeach
          </select>
        </div>

        <!-- Jabatan -->
        <div class="space-y-1">
          <label class="block text-xs font-semibold text-slate-700">Jabatan :</label>
          <input type="text" name="position" placeholder="Contoh: Pengatur Lalu Lintas" class="field-input block w-full px-3.5 py-2.5 rounded-xl text-xs sm:text-sm text-slate-900 focus:outline-none"/>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Telepon / WhatsApp -->
        <div class="space-y-1">
          <label class="block text-xs font-semibold text-slate-700">No. WhatsApp/Telepon :</label>
          <input type="text" name="phone" placeholder="08xxxxxxxxxx" class="field-input block w-full px-3.5 py-2.5 rounded-xl text-xs sm:text-sm text-slate-900 focus:outline-none"/>
        </div>

        <!-- Status -->
        <div class="space-y-1">
          <label class="block text-xs font-semibold text-slate-700">Status Pegawai : <span class="text-red-500">*</span></label>
          <select name="status" required class="field-input block w-full px-3.5 py-2.5 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none cursor-pointer">
            <option value="active">Aktif</option>
            <option value="inactive">Nonaktif</option>
          </select>
        </div>
      </div>

      <!-- Keterangan -->
      <div class="space-y-1">
        <label class="block text-xs font-semibold text-slate-700">Catatan / Keterangan :</label>
        <input type="text" name="description" placeholder="Catatan tambahan..." class="field-input block w-full px-3.5 py-2.5 rounded-xl text-xs sm:text-sm text-slate-900 focus:outline-none"/>
      </div>

      <!-- Section: Akun Login Sistem (Opsional) -->
      <div class="pt-3 border-t border-slate-100">
        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
          <input type="checkbox" id="toggle-user-account" name="create_user" value="1" onchange="toggleAccountFields(this)" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
          <span class="text-xs sm:text-sm font-semibold text-slate-800">Berikan Hak Akses Akun Aplikasi (Petugas / Superadmin)</span>
        </label>

        <div id="account-fields-container" class="hidden mt-3 p-4 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-3">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="space-y-1">
              <label class="block text-xs font-semibold text-slate-700">Username Login :</label>
              <input type="text" name="username" placeholder="Username (misal: ahmad.fauzi)" class="field-input block w-full px-3 py-2 rounded-xl text-xs text-slate-900 focus:outline-none bg-white"/>
            </div>
            <div class="space-y-1">
              <label class="block text-xs font-semibold text-slate-700">Peran Sistem (Role) :</label>
              <select name="role_id" class="field-input block w-full px-3 py-2 rounded-xl text-xs text-slate-800 focus:outline-none bg-white cursor-pointer">
                @foreach ($roles as $role)
                  <option value="{{ $role->id }}">{{ $role->label }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-semibold text-slate-700">Password Akun (Minimal 6 karakter) :</label>
            <input type="password" name="password" placeholder="••••••••" class="field-input block w-full px-3 py-2 rounded-xl text-xs text-slate-900 focus:outline-none bg-white"/>
          </div>
        </div>
      </div>

      <!-- Buttons -->
      <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
        <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">Batal</button>
        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-btn transition">Simpan Pegawai</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: Ubah Data Pegawai -->
<div id="edit-modal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs hidden items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-100 p-6 sm:p-8 animate-in fade-in zoom-in-95 duration-200">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <div>
        <h3 class="text-lg font-bold text-slate-900">Ubah Data Pegawai</h3>
        <p class="text-xs text-slate-500">Perbarui informasi data pegawai dan peran sistem</p>
      </div>
      <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1">&times;</button>
    </div>

    <form id="edit-form" method="POST" class="space-y-4 pt-4">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- NIP -->
        <div class="space-y-1">
          <label class="block text-xs font-semibold text-slate-700">NIP Pegawai : <span class="text-red-500">*</span></label>
          <input type="text" id="edit-nip" name="nip" required class="field-input block w-full px-3.5 py-2.5 rounded-xl text-xs sm:text-sm text-slate-900 focus:outline-none"/>
        </div>

        <!-- Nama Lengkap -->
        <div class="space-y-1">
          <label class="block text-xs font-semibold text-slate-700">Nama Lengkap & Gelar : <span class="text-red-500">*</span></label>
          <input type="text" id="edit-name" name="name" required class="field-input block w-full px-3.5 py-2.5 rounded-xl text-xs sm:text-sm text-slate-900 focus:outline-none"/>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Unit Kerja -->
        <div class="space-y-1">
          <label class="block text-xs font-semibold text-slate-700">Unit Kerja : <span class="text-red-500">*</span></label>
          <select id="edit-work-unit-id" name="work_unit_id" required class="field-input block w-full px-3.5 py-2.5 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none cursor-pointer">
            @foreach ($workUnits as $unit)
              <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->code }})</option>
            @endforeach
          </select>
        </div>

        <!-- Jabatan -->
        <div class="space-y-1">
          <label class="block text-xs font-semibold text-slate-700">Jabatan :</label>
          <input type="text" id="edit-position" name="position" class="field-input block w-full px-3.5 py-2.5 rounded-xl text-xs sm:text-sm text-slate-900 focus:outline-none"/>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Telepon -->
        <div class="space-y-1">
          <label class="block text-xs font-semibold text-slate-700">No. WhatsApp/Telepon :</label>
          <input type="text" id="edit-phone" name="phone" class="field-input block w-full px-3.5 py-2.5 rounded-xl text-xs sm:text-sm text-slate-900 focus:outline-none"/>
        </div>

        <!-- Status -->
        <div class="space-y-1">
          <label class="block text-xs font-semibold text-slate-700">Status Pegawai : <span class="text-red-500">*</span></label>
          <select id="edit-status" name="status" required class="field-input block w-full px-3.5 py-2.5 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none cursor-pointer">
            <option value="active">Aktif</option>
            <option value="inactive">Nonaktif</option>
          </select>
        </div>
      </div>

      <!-- Keterangan -->
      <div class="space-y-1">
        <label class="block text-xs font-semibold text-slate-700">Catatan / Keterangan :</label>
        <input type="text" id="edit-description" name="description" class="field-input block w-full px-3.5 py-2.5 rounded-xl text-xs sm:text-sm text-slate-900 focus:outline-none"/>
      </div>

      <!-- Akun Role Section (Jika memiliki akun user) -->
      <div id="edit-account-section" class="pt-3 border-t border-slate-100 hidden">
        <p class="text-xs font-bold text-blue-600 mb-2">Pengaturan Hak Akses Akun Aplikasi</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div class="space-y-1">
            <label class="block text-xs font-semibold text-slate-700">Peran Sistem (Role) :</label>
            <select id="edit-role-id" name="role_id" class="field-input block w-full px-3 py-2 rounded-xl text-xs text-slate-800 focus:outline-none cursor-pointer">
              @foreach ($roles as $role)
                <option value="{{ $role->id }}">{{ $role->label }}</option>
              @endforeach
            </select>
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-semibold text-slate-700">Ganti Password (Kosongkan jika tidak diganti) :</label>
            <input type="password" name="new_password" placeholder="••••••••" class="field-input block w-full px-3 py-2 rounded-xl text-xs text-slate-900 focus:outline-none"/>
          </div>
        </div>
      </div>

      <!-- Buttons -->
      <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
        <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">Batal</button>
        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-btn transition">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openCreateModal() {
    const modal = document.getElementById('create-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function closeCreateModal() {
    const modal = document.getElementById('create-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  function toggleAccountFields(checkbox) {
    const container = document.getElementById('account-fields-container');
    if (checkbox.checked) {
      container.classList.remove('hidden');
    } else {
      container.classList.add('hidden');
    }
  }

  function openEditModal(emp) {
    const form = document.getElementById('edit-form');
    form.action = `/pegawai/${emp.id}`;

    document.getElementById('edit-nip').value = emp.nip || '';
    document.getElementById('edit-name').value = emp.name || '';
    document.getElementById('edit-work-unit-id').value = emp.work_unit_id || '';
    document.getElementById('edit-position').value = emp.position || '';
    document.getElementById('edit-phone').value = emp.phone || '';
    document.getElementById('edit-status').value = emp.status || 'active';
    document.getElementById('edit-description').value = emp.description || '';

    const accountSection = document.getElementById('edit-account-section');
    if (emp.user) {
      accountSection.classList.remove('hidden');
      if (emp.user.role_id) {
        document.getElementById('edit-role-id').value = emp.user.role_id;
      }
    } else {
      accountSection.classList.add('hidden');
    }

    const modal = document.getElementById('edit-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function closeEditModal() {
    const modal = document.getElementById('edit-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }
</script>
@endsection
