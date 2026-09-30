@extends('layouts.app')

@section('title', 'Role & Hak Akses - BPTD Kelas II Jawa Timur')

@section('content')
<main class="flex-1 max-w-[1720px] w-full mx-auto px-4 sm:px-6 lg:px-8 py-7 space-y-7">
  <!-- Header & Breadcrumb -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="space-y-1">
      <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
        <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition">Dashboard</a>
        <span>/</span>
        <span class="text-slate-600">Pengaturan</span>
        <span>/</span>
        <span class="text-blue-600">Role &amp; Hak Akses</span>
      </div>
      <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
        Manajemen Peran &amp; Hak Akses
      </h2>
      <p class="text-xs sm:text-sm text-slate-500">
        Kelola batasan wewenang sistem, pengaturan hak akses modul inventaris ATK, dan konfigurasi akun pengguna dinas.
      </p>
    </div>

    <!-- Quick Action Button -->
    <div class="flex items-center gap-2">
      <button
        type="button"
        onclick="openModal('modal-add-role')"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold text-xs sm:text-sm shadow-md shadow-blue-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]"
      >
        <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
        <span>Tambah Peran Baru</span>
      </button>
    </div>
  </div>

  <!-- 4 Summary Metric Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
    <!-- Card 1: Total Peran -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Peran Terdaftar</p>
          <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalRoles }} Peran</h3>
          <p class="text-[11px] text-slate-500 mt-0.5">Klasifikasi tingkat akses</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
          </svg>
        </div>
      </div>
    </div>

    <!-- Card 2: Total Permissions -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Modul Wewenang</p>
          <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalPermissions }} Izin</h3>
          <p class="text-[11px] text-slate-500 mt-0.5">Fitur terproteksi granular</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
          </svg>
        </div>
      </div>
    </div>

    <!-- Card 3: Total Pengguna Terikat -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Akun Pengguna</p>
          <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalSystemUsers }} Akun</h3>
          <p class="text-[11px] text-slate-500 mt-0.5">User login aktif di sistem</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
        </div>
      </div>
    </div>

    <!-- Card 4: Superadmin -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Administrator Utama</p>
          <h3 class="text-2xl font-extrabold text-amber-700 mt-1">{{ $superadminCount }} Superadmin</h3>
          <p class="text-[11px] text-slate-500 mt-0.5">Akses kendali penuh</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
          </svg>
        </div>
      </div>
    </div>
  </div>

  <!-- Two-Column Layout: Roles on Left, Permission Matrix on Right -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    <!-- Left Column: Role Selector & Cards (4 Columns) -->
    <div class="lg:col-span-4 space-y-4">
      <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-3">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
          <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            Daftar Peran Pengguna
          </h3>
          <span class="text-[11px] font-semibold text-slate-400">{{ $roles->count() }} Peran</span>
        </div>

        <div class="space-y-2.5">
          @foreach ($roles as $r)
            @php
              $isSelected = $selectedRole && $selectedRole->id === $r->id;
            @endphp
            <div
              class="relative rounded-xl p-3.5 border transition-all cursor-pointer {{ $isSelected ? 'bg-blue-50/60 border-blue-400 shadow-xs ring-1 ring-blue-500/20' : 'bg-white hover:bg-slate-50/80 border-slate-200' }}"
              onclick="window.location.href='{{ route('settings.roles.index', ['role_id' => $r->id]) }}'"
            >
              <div class="flex items-start justify-between gap-2">
                <div class="flex-1">
                  <div class="flex items-center gap-2">
                    <span class="font-bold text-sm {{ $isSelected ? 'text-blue-900' : 'text-slate-800' }}">
                      {{ $r->label }}
                    </span>
                    @if (in_array($r->name, ['superadmin', 'petugas', 'pimpinan']))
                      <span class="text-[9px] font-bold tracking-wider uppercase px-1.5 py-0.5 rounded-sm bg-slate-100 text-slate-500">
                        Default
                      </span>
                    @endif
                  </div>
                  <div class="text-[11px] font-mono text-slate-400 mt-0.5">
                    ID: {{ $r->name }}
                  </div>
                  <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                    {{ $r->description ?? 'Tidak ada deskripsi peran.' }}
                  </p>
                </div>

                <!-- Active Indicator Pin -->
                @if ($isSelected)
                  <span class="w-2.5 h-2.5 rounded-full bg-blue-600 shrink-0 mt-1 shadow-xs"></span>
                @endif
              </div>

              <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <div class="flex items-center gap-3">
                  <span class="inline-flex items-center gap-1 font-semibold text-slate-700">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    {{ $r->users_count }} Pengguna
                  </span>
                  <span class="inline-flex items-center gap-1 text-[11px] text-blue-600 font-semibold bg-blue-100/60 px-2 py-0.5 rounded-full">
                    {{ $r->permissions->count() }} Hak Akses
                  </span>
                </div>

                <!-- Edit / Delete Actions -->
                <div class="flex items-center gap-1" onclick="event.stopPropagation()">
                  <button
                    type="button"
                    onclick="openEditRoleModal({{ json_encode($r) }})"
                    class="p-1 rounded text-slate-400 hover:text-blue-600 hover:bg-white transition"
                    title="Ubah Label/Deskripsi"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                  </button>

                  @if (!in_array($r->name, ['superadmin', 'petugas', 'pimpinan']))
                    <form
                      action="{{ route('settings.roles.destroy', $r) }}"
                      method="POST"
                      class="inline"
                      onsubmit="window.confirmSubmit(event, {
                        title: 'Hapus Peran Pengguna',
                        message: 'Peran yang dihapus tidak dapat dipulihkan kembali. Seluruh izin modular terkait akan dilepaskan.',
                        badgeText: '{{ addslashes($r->label) }} ({{ $r->name }})',
                        type: 'danger',
                        confirmText: 'Ya, Hapus Peran'
                      })"
                    >
                      @csrf
                      @method('DELETE')
                      <button
                        type="submit"
                        class="p-1 rounded text-slate-400 hover:text-rose-600 hover:bg-white transition"
                        title="Hapus Peran"
                      >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </form>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <!-- Right Column: Permission Matrix for Selected Role (8 Columns) -->
    <div class="lg:col-span-8 space-y-4">
      @if ($selectedRole)
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
          <!-- Card Header with Selected Role Identity -->
          <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
              <div class="flex items-center gap-2">
                <h3 class="font-extrabold text-slate-900 text-base">
                  Matriks Hak Akses: {{ $selectedRole->label }}
                </h3>
                <span class="font-mono text-xs font-semibold px-2 py-0.5 rounded-md bg-blue-100 text-blue-700">
                  {{ $selectedRole->name }}
                </span>
              </div>
              <p class="text-xs text-slate-500 mt-0.5">
                Centang izin hak akses yang diberikan kepada pemegang peran ini.
              </p>
            </div>

            <!-- Quick Select Helpers -->
            <div class="flex items-center gap-2">
              <button
                type="button"
                onclick="toggleAllCheckboxes(true)"
                class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition"
              >
                Pilih Semua
              </button>
              <button
                type="button"
                onclick="toggleAllCheckboxes(false)"
                class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition"
              >
                Hapus Semua
              </button>
            </div>
          </div>

          <!-- Form Permissions Matrix -->
          <form action="{{ route('settings.roles.permissions.update', $selectedRole) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            @php
              $currentPermIds = $selectedRole->permissions->pluck('id')->toArray();
              $moduleTitles = [
                'pegawai' => ['title' => 'Modul Data Pegawai (Master Penerima)', 'icon' => '👥', 'color' => 'blue'],
                'inventory' => ['title' => 'Modul Master ATK & Kategori Inventaris', 'icon' => '📦', 'color' => 'indigo'],
                'transaksi' => ['title' => 'Modul Transaksi (Stock Out, Stock In, Opname)', 'icon' => '🔄', 'color' => 'amber'],
                'laporan' => ['title' => 'Modul Laporan & Rekapitulasi Ekspor', 'icon' => '📊', 'color' => 'emerald'],
                'ai' => ['title' => 'Modul AI Stock Assistant (Read-Only)', 'icon' => '🤖', 'color' => 'purple'],
                'system' => ['title' => 'Modul Pengaturan Sistem & Audit Trail', 'icon' => '⚙️', 'color' => 'slate'],
              ];
            @endphp

            @foreach ($permissionsByModule as $moduleName => $permissions)
              @php
                $meta = $moduleTitles[$moduleName] ?? ['title' => ucfirst($moduleName), 'icon' => '📌', 'color' => 'slate'];
              @endphp
              <div class="rounded-xl border border-slate-200/90 overflow-hidden bg-white shadow-2xs">
                <!-- Module Category Header -->
                <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200/80 flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="text-base">{{ $meta['icon'] }}</span>
                    <span class="font-bold text-xs sm:text-sm text-slate-800 tracking-tight">
                      {{ $meta['title'] }}
                    </span>
                  </div>
                  <span class="text-[11px] font-semibold text-slate-400">
                    {{ $permissions->count() }} Izin
                  </span>
                </div>

                <!-- Checkbox Items Grid -->
                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                  @foreach ($permissions as $perm)
                    @php
                      $isChecked = in_array($perm->id, $currentPermIds);
                    @endphp
                    <label
                      class="flex items-start gap-3 p-2.5 rounded-lg border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition cursor-pointer select-none"
                    >
                      <input
                        type="checkbox"
                        name="permissions[]"
                        value="{{ $perm->id }}"
                        {{ $isChecked ? 'checked' : '' }}
                        class="perm-checkbox mt-0.5 rounded text-blue-600 focus:ring-blue-500 w-4 h-4 border-slate-300 transition"
                      />
                      <div class="flex-1">
                        <div class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                          {{ $perm->label }}
                        </div>
                        <div class="font-mono text-[10px] text-slate-400 mt-0.5">
                          {{ $perm->name }}
                        </div>
                      </div>
                    </label>
                  @endforeach
                </div>
              </div>
            @endforeach

            <!-- Action Buttons Footer -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
              <span class="text-xs text-slate-400">
                Terakhir disesuaikan: {{ $selectedRole->updated_at?->diffForHumans() ?? 'Baru' }}
              </span>
              <div class="flex items-center gap-2">
                <a
                  href="{{ route('settings.roles.index', ['role_id' => $selectedRole->id]) }}"
                  class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition"
                >
                  Reset Pilihan
                </a>
                <button
                  type="submit"
                  class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md shadow-blue-500/20 transition hover:scale-[1.01] active:scale-[0.99]"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  <span>Simpan Perubahan Hak Akses</span>
                </button>
              </div>
            </div>
          </form>
        </div>
      @else
        <div class="bg-white rounded-2xl p-12 text-center text-slate-400 border border-slate-200/80">
          <p class="text-sm font-semibold">Pilih peran di sebelah kiri untuk melihat dan mengonfigurasi hak akses.</p>
        </div>
      @endif
    </div>
  </div>
</main>

<!-- Modal: Tambah Peran Baru -->
<div id="modal-add-role" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-xl overflow-hidden animate-scale-in">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
          <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
          </svg>
        </div>
        <h3 class="font-bold text-slate-900 text-base">Tambah Peran Baru</h3>
      </div>
      <button type="button" onclick="closeModal('modal-add-role')" class="text-slate-400 hover:text-slate-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <form action="{{ route('settings.roles.store') }}" method="POST" class="p-6 space-y-4">
      @csrf
      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Identitas / Slug <span class="text-rose-500">*</span></label>
        <input
          type="text"
          name="name"
          required
          placeholder="Contoh: staf_gudang"
          value="{{ old('name') }}"
          class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 font-mono focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
        />
        <p class="text-[11px] text-slate-400 mt-1">Hanya huruf kecil, angka, dan underscore (_).</p>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Label Nama Peran <span class="text-rose-500">*</span></label>
        <input
          type="text"
          name="label"
          required
          placeholder="Contoh: Staf Gudang Operasional"
          value="{{ old('label') }}"
          class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
        />
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Deskripsi Wewenang</label>
        <textarea
          name="description"
          rows="3"
          placeholder="Jelaskan ruang lingkup wewenang peran ini..."
          class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
        >{{ old('description') }}</textarea>
      </div>

      <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
        <button
          type="button"
          onclick="closeModal('modal-add-role')"
          class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition"
        >
          Batal
        </button>
        <button
          type="submit"
          class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md shadow-blue-500/20 transition"
        >
          Simpan Peran
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Edit Peran -->
<div id="modal-edit-role" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-xl overflow-hidden animate-scale-in">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
          <svg class="w-4 h-4 stroke-[2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
          </svg>
        </div>
        <h3 class="font-bold text-slate-900 text-base">Edit Informasi Peran</h3>
      </div>
      <button type="button" onclick="closeModal('modal-edit-role')" class="text-slate-400 hover:text-slate-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <form id="form-edit-role" method="POST" class="p-6 space-y-4">
      @csrf
      @method('PUT')
      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Identitas Peran (Slug)</label>
        <input
          type="text"
          id="edit-role-name"
          disabled
          class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 font-mono bg-slate-100 text-slate-500 cursor-not-allowed"
        />
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Label Nama Peran <span class="text-rose-500">*</span></label>
        <input
          type="text"
          id="edit-role-label"
          name="label"
          required
          class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
        />
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Deskripsi Wewenang</label>
        <textarea
          id="edit-role-description"
          name="description"
          rows="3"
          class="w-full px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-200 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
        ></textarea>
      </div>

      <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
        <button
          type="button"
          onclick="closeModal('modal-edit-role')"
          class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition"
        >
          Batal
        </button>
        <button
          type="submit"
          class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md shadow-blue-500/20 transition"
        >
          Perbarui Peran
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

  function openEditRoleModal(role) {
    const form = document.getElementById('form-edit-role');
    form.action = `/settings/roles/${role.id}`;

    document.getElementById('edit-role-name').value = role.name || '';
    document.getElementById('edit-role-label').value = role.label || '';
    document.getElementById('edit-role-description').value = role.description || '';

    openModal('modal-edit-role');
  }

  function toggleAllCheckboxes(checked) {
    document.querySelectorAll('.perm-checkbox').forEach(cb => {
      cb.checked = checked;
    });
  }

  // Close modals on Escape key
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
      closeModal('modal-add-role');
      closeModal('modal-edit-role');
    }
  });
</script>
@endsection
