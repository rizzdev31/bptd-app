<!-- BEGIN: LeftSidebar -->
<aside
  class="w-full lg:w-72 shrink-0 min-h-0 lg:min-h-screen bg-white border-b lg:border-b-0 lg:border-r border-slate-200/80 p-4 flex flex-col justify-between lg:sticky lg:top-0 lg:h-screen overflow-visible lg:overflow-y-auto z-40 select-none"
>
  <div class="flex flex-col flex-1 justify-between gap-5 h-auto lg:h-full">
    <!-- Top: Logo Capsule & Nav Links -->
    <div class="flex flex-col gap-5">
      <!-- Brand Capsule (Top) with 4-Agency Logos -->
      <div
        class="bg-white rounded-2xl p-2.5 flex items-center justify-center border border-slate-200 shadow-sm transition hover:shadow-md"
        data-purpose="sidebar-brand-capsule"
      >
        <img
          src="{{ asset('assets/logo-sidebar.png') }}"
          alt="Logo BPTD Kelas II Jawa Timur"
          class="h-11 w-auto max-w-full object-contain mx-auto transition-transform hover:scale-105 duration-200"
        />
      </div>

      <button
        id="sidebar-menu-toggle"
        class="sidebar-menu-btn lg:hidden flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-white text-slate-700 font-medium text-sm"
        type="button"
        aria-expanded="false"
        aria-controls="sidebar-navigation"
      >
        <span class="flex items-center gap-3">
          <svg
            class="w-5 h-5 text-blue-600"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
            aria-hidden="true"
          >
            <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"></path>
          </svg>
          Menu Navigasi
        </span>
        <svg
          class="w-4 h-4 text-slate-500 transition-transform"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
          aria-hidden="true"
        >
          <path
            d="m6 9 6 6 6-6"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
          ></path>
        </svg>
      </button>

      <!-- Navigation List with Active Indicator and Hierarchical Tree -->
      <nav
        id="sidebar-navigation"
        class="hidden lg:block space-y-2 relative"
        aria-label="Sidebar Navigation"
      >
        <!-- Menu 1: Dashboard -->
        <a
          href="{{ route('dashboard') }}"
          class="relative group flex w-full items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('dashboard') ? 'bg-linear-to-r from-amber-500 to-yellow-500 text-white font-semibold shadow-sm hover:brightness-105' : 'sidebar-menu-btn bg-white hover:bg-slate-50 text-slate-700 hover:text-blue-600 font-medium border border-slate-200' }}"
        >
          @if (request()->routeIs('dashboard'))
            <span
              class="absolute -left-3 top-1.5 bottom-1.5 w-1.5 bg-blue-600 rounded-r-full shadow-sm"
            ></span>
          @endif
          <div
            class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-600 group-hover:bg-blue-100 group-hover:text-blue-700 transition' }}"
          >
            <svg
              class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-white stroke-[2.2]' : 'stroke-2' }}"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                stroke-linecap="round"
                stroke-linejoin="round"
              ></path>
            </svg>
          </div>
          <span class="tracking-wide flex-1">Dashboard</span>
          @if (request()->routeIs('dashboard'))
            <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80"></span>
          @endif
        </a>

        <!-- Menu 2: Master Pegawai -->
        <a
          href="{{ route('pegawai.index') }}"
          class="relative group flex w-full items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('pegawai.*') ? 'bg-linear-to-r from-amber-500 to-yellow-500 text-white font-semibold shadow-sm hover:brightness-105' : 'sidebar-menu-btn bg-white hover:bg-slate-50 text-slate-700 hover:text-blue-600 font-medium border border-slate-200' }}"
        >
          @if (request()->routeIs('pegawai.*'))
            <span
              class="absolute -left-3 top-1.5 bottom-1.5 w-1.5 bg-blue-600 rounded-r-full shadow-sm"
            ></span>
          @endif
          <div
            class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->routeIs('pegawai.*') ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-600 group-hover:bg-blue-100 group-hover:text-blue-700 transition' }}"
          >
            <svg
              class="w-4 h-4 {{ request()->routeIs('pegawai.*') ? 'text-white stroke-[2.2]' : 'stroke-2' }}"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                stroke-linecap="round"
                stroke-linejoin="round"
              ></path>
              <circle cx="12" cy="11" r="2.5"></circle>
            </svg>
          </div>
          <span class="flex-1">Master Pegawai</span>
          @if (request()->routeIs('pegawai.*'))
            <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80"></span>
          @endif
        </a>

        <!-- Menu 3: Master Inventory ATK Apps with Polished Accordion Submenu -->
        <div class="w-full space-y-1.5">
          <button
            id="inventory-menu-toggle"
            type="button"
            aria-expanded="true"
            aria-controls="inventory-submenu"
            class="sidebar-menu-btn group flex w-full items-center justify-between px-3.5 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-800 font-semibold text-[13px] border border-slate-200 transition-all"
          >
            <div class="flex items-center gap-3">
              <div
                class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:bg-blue-100 transition"
              >
                <svg
                  class="w-4 h-4 stroke-2"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    d="M4 6h16M4 10h16M4 14h16M4 18h16"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  ></path>
                  <circle cx="2" cy="6" fill="currentColor" r="1"></circle>
                  <circle cx="2" cy="10" fill="currentColor" r="1"></circle>
                  <circle cx="2" cy="14" fill="currentColor" r="1"></circle>
                  <circle cx="2" cy="18" fill="currentColor" r="1"></circle>
                </svg>
              </div>
              <span class="leading-tight">Master Inventory ATK</span>
            </div>
            <svg
              id="inventory-chevron"
              class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-600 transition-transform duration-300 rotate-180"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              aria-hidden="true"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2.5"
                d="M19 9l-7 7-7-7"
              ></path>
            </svg>
          </button>

          <!-- Sub-menu list with connecting guide line and badges -->
          <div id="inventory-submenu" class="inventory-submenu">
            <div class="min-h-0 overflow-hidden">
              <div
                class="relative ml-3 pl-4 border-l-2 border-slate-100 space-y-1 pt-1"
              >
                <a
                  href="{{ route('inventory.items.index') }}"
                  class="flex w-full items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-medium transition group {{ request()->routeIs('inventory.items.*') ? 'text-blue-700 bg-blue-50 font-bold' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' }}"
                >
                  <span class="flex items-center gap-2">
                    <span
                      class="w-5 h-5 rounded-md font-mono text-[10px] flex items-center justify-center font-bold transition {{ request()->routeIs('inventory.items.*') ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 group-hover:bg-blue-100 group-hover:text-blue-600 text-slate-500' }}"
                      >1</span
                    >
                    <span>Data Master ATK</span>
                  </span>
                  @if (request()->routeIs('inventory.items.*'))
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                  @endif
                </a>
                <a
                  href="#"
                  class="flex w-full items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition group"
                >
                  <span class="flex items-center gap-2">
                    <span
                      class="w-5 h-5 rounded-md bg-slate-100 group-hover:bg-blue-100 group-hover:text-blue-600 text-slate-500 font-mono text-[10px] flex items-center justify-center font-bold transition"
                      >2</span
                    >
                    <span class="">Permintaan/Pengeluaran</span>
                  </span>
                </a>
                <a
                  href="#"
                  class="flex w-full items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition group"
                >
                  <span class="flex items-center gap-2">
                    <span
                      class="w-5 h-5 rounded-md bg-slate-100 group-hover:bg-blue-100 group-hover:text-blue-600 text-slate-500 font-mono text-[10px] flex items-center justify-center font-bold transition"
                      >3</span
                    >
                    <span class="">Kendali Stock</span>
                  </span>
                </a>
                <a
                  href="#"
                  class="flex w-full items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition group"
                >
                  <span class="flex items-center gap-2">
                    <span
                      class="w-5 h-5 rounded-md bg-slate-100 group-hover:bg-blue-100 group-hover:text-blue-600 text-slate-500 font-mono text-[10px] flex items-center justify-center font-bold transition"
                      >4</span
                    >
                    <span class="">Pengadaan</span>
                  </span>
                </a>
                <a
                  href="#"
                  class="flex w-full items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition group"
                >
                  <span class="flex items-center gap-2">
                    <span
                      class="w-5 h-5 rounded-md bg-slate-100 group-hover:bg-blue-100 group-hover:text-blue-600 text-slate-500 font-mono text-[10px] flex items-center justify-center font-bold transition"
                      >5</span
                    >
                    <span class="">Laporan</span>
                  </span>
                </a>
                <a
                  href="#"
                  class="flex w-full items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition group"
                >
                  <span class="flex items-center gap-2">
                    <span
                      class="w-5 h-5 rounded-md bg-slate-100 group-hover:bg-blue-100 group-hover:text-blue-600 text-slate-500 font-mono text-[10px] flex items-center justify-center font-bold transition"
                      >6</span
                    >
                    <span class="">Audit</span>
                  </span>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Menu 4: Role & Permission (Dedicated Settings Bar) -->
        <a
          href="{{ route('settings.roles.index') }}"
          class="relative group flex w-full items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('settings.roles.*') ? 'bg-linear-to-r from-amber-500 to-yellow-500 text-white font-semibold shadow-sm hover:brightness-105' : 'sidebar-menu-btn bg-white hover:bg-slate-50 text-slate-700 hover:text-blue-600 font-medium border border-slate-200' }}"
        >
          @if (request()->routeIs('settings.roles.*'))
            <span
              class="absolute -left-3 top-1.5 bottom-1.5 w-1.5 bg-blue-600 rounded-r-full shadow-sm"
            ></span>
          @endif
          <div
            class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ request()->routeIs('settings.roles.*') ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-600 group-hover:bg-blue-100 group-hover:text-blue-700 transition' }}"
          >
            <svg
              class="w-4 h-4 {{ request()->routeIs('settings.roles.*') ? 'text-white stroke-[2.2]' : 'stroke-2' }}"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
              ></path>
            </svg>
          </div>
          <span class="flex-1">Role &amp; Permission</span>
          @if (request()->routeIs('settings.roles.*'))
            <span class="w-1.5 h-1.5 rounded-full bg-white opacity-80"></span>
          @endif
        </a>

        <!-- Menu 5: Coming Soon 1 -->
        <div
          class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-slate-50/70 border border-slate-200/60 text-slate-400 text-sm"
        >
          <div class="flex items-center gap-3">
            <div
              class="w-7 h-7 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center shrink-0"
            >
              <svg
                class="w-4 h-4 stroke-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                ></path>
              </svg>
            </div>
            <span class="font-medium text-xs text-slate-500"
              >Surat &amp; Dokumen</span
            >
          </div>
          <span
            class="text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full bg-slate-200/70 text-slate-500"
            >Soon</span
          >
        </div>

        <!-- Menu 5: Coming Soon 2 -->
        <div
          class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-slate-50/70 border border-slate-200/60 text-slate-400 text-sm"
        >
          <div class="flex items-center gap-3">
            <div
              class="w-7 h-7 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center shrink-0"
            >
              <svg
                class="w-4 h-4 stroke-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                ></path>
              </svg>
            </div>
            <span class="font-medium text-xs text-slate-500"
              >Arsip Keuangan</span
            >
          </div>
          <span
            class="text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full bg-slate-200/70 text-slate-500"
            >Soon</span
          >
        </div>
      </nav>
    </div>

    <!-- Bottom: Refined Informasi Terkini Widget Card -->
    <div
      class="relative bg-linear-to-br from-slate-50 to-blue-50/40 rounded-2xl border border-blue-100 p-4 shadow-sm overflow-hidden shrink-0 mt-auto flex flex-col justify-between"
    >
      <!-- Watermark Pattern Accent -->
      <div
        class="absolute -right-4 -bottom-4 w-24 h-24 opacity-15 pointer-events-none text-blue-600"
      >
        <svg class="w-full h-full fill-current" viewBox="0 0 24 24">
          <path
            d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 14.5h-2v-2h2v2zm0-4h-2V7h2v5.5z"
          ></path>
        </svg>
      </div>
      <div class="relative z-10 space-y-2">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
            <h4 class="font-bold text-slate-900 text-xs tracking-tight">
              Informasi Terkini
            </h4>
          </div>
          <span
            class="text-[10px] font-semibold text-blue-600 bg-blue-100 px-2 py-0.5 rounded-md"
            >Update</span
          >
        </div>
        <p class="text-[11px] text-slate-600 font-medium leading-snug">
          Pembaruan sistem inventaris &amp; master berkas periode September
          2026.
        </p>
        <div class="pt-1 flex items-center justify-between">
          <span class="text-[10px] text-slate-400 font-mono">23 Sep 2026</span>
          <a
            href="#"
            class="text-[11px] font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition"
          >
            <span class="">Lihat</span>
            <span class="">→</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</aside>
<!-- END: LeftSidebar -->
