<!-- BEGIN: MainNavbar -->
<header class="w-full bg-white/95 backdrop-blur-md border-b border-slate-200/90 shadow-2xs transition-shadow">
  <div
    class="max-w-430 mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-wrap items-center justify-between min-h-22"
  >
    <!-- Left Side: Authority Titles -->
    <div class="flex items-center">
      <div class="flex flex-col justify-center" data-purpose="title-container">
        <h1
          class="text-lg sm:text-xl font-extrabold tracking-tight text-black leading-tight"
        >
          BPTD Kelas II Jawa Timur
        </h1>
        <p
          class="text-xs sm:text-sm font-normal text-slate-800 tracking-normal mt-0.5"
        >
          Internal Management System
        </p>
      </div>
    </div>
    <!-- Right Side Actions: Superadmin, Real-Time Clock, Log Out Capsules -->
    <div
      class="flex items-center flex-wrap gap-2.5 sm:gap-3.5"
      data-purpose="header-actions"
    >
      <!-- Superadmin Pill -->
      <div
        class="capsule-pill bg-white hover:bg-slate-50 text-slate-800 text-[13px] font-medium rounded-full px-4 py-1.5 flex items-center gap-2 cursor-pointer select-none"
      >
        <svg
          class="w-4 h-4 text-blue-600 stroke-[2.2]"
          fill="none"
          stroke="currentColor"
          stroke-linecap="round"
          stroke-linejoin="round"
          viewBox="0 0 24 24"
        >
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
          <circle cx="12" cy="10" r="3"></circle>
        </svg>
        <span class="">{{ Auth::user()?->role?->label ?? (Auth::user()?->name ?? 'Superadmin') }}</span>
      </div>
      <!-- Digital Clock Pill -->
      <div
        class="capsule-pill bg-white text-slate-800 text-[13px] font-semibold font-mono rounded-full px-4 py-1.5 flex items-center gap-2.5 select-none"
        title="Waktu Indonesia Barat (WIB)"
      >
        <svg
          class="w-4 h-4 text-slate-700 stroke-2"
          fill="none"
          stroke="currentColor"
          stroke-linecap="round"
          stroke-linejoin="round"
          viewBox="0 0 24 24"
        >
          <circle cx="12" cy="12" r="10"></circle>
          <polyline points="12 6 12 12 16 14"></polyline>
        </svg>
        <span
          class="tracking-wider text-slate-900 font-medium"
          id="digital-clock"
          >09 : 50 : 47</span
        >
      </div>
      <!-- Log Out Pill -->
      <form id="logout-form" action="{{ route('logout') }}" method="POST" class="inline">
        @csrf
        <button
          type="submit"
          onclick="return confirmLogout(event);"
          class="capsule-pill bg-white hover:bg-red-50 text-slate-800 hover:text-red-600 text-[13px] font-medium rounded-full px-4 py-1.5 flex items-center gap-2 transition-colors cursor-pointer group"
        >
          <svg
            class="w-4 h-4 text-red-500 group-hover:text-red-600 stroke-[2.2]"
            fill="none"
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            viewBox="0 0 24 24"
          >
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" x2="9" y1="12" y2="12"></line>
          </svg>
          <span class="">Log Out</span>
        </button>
      </form>
    </div>
  </div>
</header>
<!-- END: MainNavbar -->
