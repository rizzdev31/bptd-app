<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'BPTD Kelas II Jawa Timur - Internal Management System')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap"
      rel="stylesheet"
    />
    <script>
      tailwind = {
        config: {
          theme: {
            extend: {
              fontFamily: {
                sans: ['"Plus Jakarta Sans"', "sans-serif"],
                mono: ['"JetBrains Mono"', "monospace"],
              },
              colors: {
                brand: {
                  navy: "#0b2341",
                  gold: "#eab308",
                  blue: "#2563eb",
                  electric: "#264bf6",
                },
              },
            },
          },
        },
      };
    </script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <style>
      body {
        background-color: #f8fafc;
        font-family: "Plus Jakarta Sans", sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
      }
      /* Preloader Critical CSS */
      #app-preloader {
        position: fixed !important;
        inset: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background-color: #ffffff !important;
        z-index: 999999 !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        opacity: 1;
        transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
      }
      #app-preloader.preloader-hidden {
        opacity: 0 !important;
        pointer-events: none !important;
        transform: scale(1.04) !important;
      }
      .capsule-pill {
        box-shadow:
          0 2px 8px -2px rgba(0, 0, 0, 0.05),
          0 1px 3px 0 rgba(0, 0, 0, 0.03);
        border: 1.5px solid #e2e8f0;
        transition: all 0.2s ease-in-out;
      }
      .capsule-pill:hover {
        box-shadow: 0 4px 14px -3px rgba(0, 0, 0, 0.08);
      }
      .sidebar-menu-btn {
        box-shadow:
          0 2px 6px -1px rgba(0, 0, 0, 0.04),
          0 1px 3px 0 rgba(0, 0, 0, 0.02);
        border: 1px solid #e2e8f0;
      }
      .sidebar-menu-btn:hover {
        box-shadow: 0 4px 10px -2px rgba(0, 0, 0, 0.07);
      }
      .inventory-submenu {
        display: grid;
        grid-template-rows: 1fr;
        transition: grid-template-rows 0.3s ease-in-out;
      }
      .inventory-submenu.is-collapsed {
        grid-template-rows: 0fr;
      }
      @media (prefers-reduced-motion: reduce) {
        .inventory-submenu {
          transition: none;
        }
      }
      /* Canonical Tailwind v4 mappings for seamless rendering */
      .bg-linear-to-r {
        background-image: linear-gradient(to right, var(--tw-gradient-stops));
      }
      .bg-linear-to-br {
        background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
      }
      .max-w-430 {
        max-width: 1720px;
      }
      .max-w-8\.5 {
        max-width: 34px;
      }
      .min-h-55 {
        min-height: 220px;
      }
      .min-h-22 {
        min-height: 88px;
      }
      .max-h-155 {
        max-height: 620px;
      }

      /* ============================================================== */
      /* Smooth Entrance & Interactive Micro-Animations                */
      /* ============================================================== */
      @keyframes bptdFadeInUp {
        0% {
          opacity: 0;
          transform: translateY(12px);
        }
        100% {
          opacity: 1;
          transform: translateY(0);
        }
      }

      @keyframes bptdFadeIn {
        0% { opacity: 0; }
        100% { opacity: 1; }
      }

      @keyframes bptdScaleIn {
        0% {
          opacity: 0;
          transform: scale(0.96) translateY(6px);
        }
        100% {
          opacity: 1;
          transform: scale(1) translateY(0);
        }
      }

      @keyframes bptdGaugeCircle {
        0% {
          stroke-dashoffset: 91;
        }
      }

      .anim-fade-in-up {
        animation: bptdFadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
      }

      .anim-fade-in {
        animation: bptdFadeIn 0.3s ease-out both;
      }

      .anim-scale-in,
      .animate-scale-in {
        animation: bptdScaleIn 0.28s cubic-bezier(0.16, 1, 0.3, 1) both;
      }

      .gauge-animated {
        animation: bptdGaugeCircle 0.85s cubic-bezier(0.16, 1, 0.3, 1) forwards;
      }

      /* Stagger Delays */
      .anim-delay-50  { animation-delay: 50ms; }
      .anim-delay-100 { animation-delay: 100ms; }
      .anim-delay-150 { animation-delay: 150ms; }
      .anim-delay-200 { animation-delay: 200ms; }
      .anim-delay-250 { animation-delay: 250ms; }
      .anim-delay-300 { animation-delay: 300ms; }
      .anim-delay-350 { animation-delay: 350ms; }
      .anim-delay-400 { animation-delay: 400ms; }

      @media (prefers-reduced-motion: reduce) {
        .anim-fade-in-up,
        .anim-fade-in,
        .anim-scale-in,
        .animate-scale-in,
        .gauge-animated {
          animation: none !important;
          opacity: 1 !important;
          transform: none !important;
        }
      }
    </style>
    @stack('styles')
  </head>
  <body class="min-h-screen text-slate-800 flex flex-col lg:flex-row bg-[#f8fafc]">
    @if (request()->routeIs('dashboard'))
      <!-- BEGIN: Preloader Screen (Hanya saat login ke dashboard) -->
      <div
        id="app-preloader"
        class="fixed inset-0 z-50 bg-white flex flex-col items-center justify-center transition-all duration-700 ease-in-out select-none"
        style="{{ session('show_dashboard_preloader') ? '' : 'display: none !important;' }}"
        role="status"
        aria-live="polite"
        aria-busy="true"
        aria-label="Memuat antarmuka dashboard BPTD Kelas II Jawa Timur"
      >
        <div class="flex flex-col items-center justify-center px-6 text-center max-w-md w-full">
          <!-- Logo Kementerian Perhubungan -->
          <div class="w-12 h-12 mb-2 flex items-center justify-center">
            <img
              alt="Logo Kementerian Perhubungan"
              class="w-full h-full object-contain"
              src="{{ asset('assets/logo-kemenhub.png') }}"
            />
          </div>

          <!-- Video Bis Ukuran Kecil untuk Preloader -->
          <div class="relative w-44 sm:w-52 h-28 sm:h-32 flex items-center justify-center overflow-hidden my-1">
            <video
              id="preloader-bus-video"
              autoplay
              loop
              muted
              playsinline
              preload="auto"
              class="w-full h-full object-contain mix-blend-multiply pointer-events-none select-none"
              style="filter: contrast(102%) brightness(102.5%);"
            >
              <source src="{{ asset('assets/bus.mp4') }}" type="video/mp4" />
            </video>
          </div>

          <!-- Tulisan Identitas di Tengah Sesuai Permintaan -->
          <div class="mt-2 space-y-1">
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight uppercase">
              BPTD Kelas II Jawa Timur
            </h2>
            <p class="text-xs sm:text-[13px] font-semibold text-slate-500 uppercase tracking-wider">
              Kementerian Perhubungan
            </p>
            <p class="text-[11px] font-medium text-blue-600 tracking-widest uppercase">
              Internal Management System
            </p>
          </div>

          <!-- Indikator Loading & Progress Bar -->
          <div class="w-60 sm:w-64 mt-6">
            <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden shadow-inner relative">
              <div
                id="preloader-progress-bar"
                class="h-full bg-linear-to-r from-blue-500 to-blue-600 rounded-full w-0 transition-all ease-out"
              ></div>
            </div>
            <div class="flex justify-between items-center mt-2.5 text-[11px] text-slate-400 font-mono">
              <span id="preloader-status-text">Memverifikasi sesi login...</span>
              <span id="preloader-percentage-text">0%</span>
            </div>
          </div>
        </div>
      </div>
      <script>
        (function() {
          const hasSession = {{ session('show_dashboard_preloader') ? 'true' : 'false' }};
          const hasStorage = sessionStorage.getItem('bptd_show_preloader') === 'true';
          const preloader = document.getElementById('app-preloader');
          if (preloader) {
            if (hasSession || hasStorage) {
              preloader.style.removeProperty('display');
              window.__bptd_run_dashboard_preloader = true;
            } else {
              preloader.remove();
              window.__bptd_run_dashboard_preloader = false;
            }
          }
        })();
      </script>
      <!-- END: Preloader Screen -->
    @endif

    <!-- Sidebar Layout -->
    <div id="sidebar-slot" class="w-full lg:w-72 shrink-0">
      @include('layouts.sidebar')
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen justify-between">
      <div id="navbar-slot">
        @include('layouts.navbar')
      </div>

      <div id="dashboard-slot" class="flex-1">
        @yield('content')
      </div>

      <div id="footer-slot">
        @include('layouts.footer')
      </div>
    </div>

    <!-- Global Feedback Systems (Toast & Confirm Modal) -->
    @include('components.toast-container')
    @include('components.confirm-modal')

    <script>
      // Sidebar Interactivity
      function initializeSidebar() {
        const menuToggle = document.getElementById("sidebar-menu-toggle");
        const navigation = document.getElementById("sidebar-navigation");

        if (menuToggle && navigation) {
          menuToggle.addEventListener("click", () => {
            const isExpanded = menuToggle.getAttribute("aria-expanded") === "true";
            menuToggle.setAttribute("aria-expanded", String(!isExpanded));
            navigation.classList.toggle("hidden", isExpanded);
          });
        }

        const inventoryToggle = document.getElementById("inventory-menu-toggle");
        const inventorySubmenu = document.getElementById("inventory-submenu");
        const inventoryChevron = document.getElementById("inventory-chevron");

        if (inventoryToggle && inventorySubmenu && inventoryChevron) {
          inventoryToggle.addEventListener("click", () => {
            const isExpanded = inventoryToggle.getAttribute("aria-expanded") === "true";
            inventoryToggle.setAttribute("aria-expanded", String(!isExpanded));
            inventorySubmenu.classList.toggle("is-collapsed", isExpanded);
            inventoryChevron.classList.toggle("rotate-180", !isExpanded);
          });
        }
      }

      // Realtime Digital Clock
      function initializeClock() {
        const updateClock = () => {
          const now = new Date();
          const time = [now.getHours(), now.getMinutes(), now.getSeconds()]
            .map((value) => String(value).padStart(2, "0"))
            .join(" : ");

          for (const id of ["digital-clock", "footer-digital-clock"]) {
            const clock = document.getElementById(id);
            if (clock) clock.textContent = time;
          }
        };

        updateClock();
        window.setInterval(updateClock, 1000);
      }

      // Preloader Controller for Dashboard (Hanya saat transisi Login ke Dashboard)
      function initializePreloader() {
        const preloader = document.getElementById("app-preloader");
        if (!preloader || !window.__bptd_run_dashboard_preloader) {
          preloader?.remove();
          return;
        }

        const progressBar = document.getElementById("preloader-progress-bar");
        const percentText = document.getElementById("preloader-percentage-text");
        const statusText = document.getElementById("preloader-status-text");
        const video = document.getElementById("preloader-bus-video");

        if (video) {
          video.muted = true;
          video.play().catch(() => {});
        }

        const DURATION_MS = 2200;
        const startTime = performance.now();
        const stages = [
          [0, "Memverifikasi sesi login..."],
          [30, "Memuat modul inventaris ATK..."],
          [65, "Menyiapkan antarmuka dashboard..."],
          [90, "Sistem siap!"]
        ];

        function step(now) {
          const progress = Math.min((now - startTime) / DURATION_MS, 1);
          const eased = 1 - Math.pow(1 - progress, 3);
          const percent = Math.floor(eased * 100);

          if (progressBar) progressBar.style.width = `${percent}%`;
          if (percentText) percentText.textContent = `${percent}%`;
          if (statusText) {
            const currentStage = stages.findLast(([threshold]) => percent >= threshold);
            if (currentStage) statusText.textContent = currentStage[1];
          }

          if (progress < 1) {
            requestAnimationFrame(step);
          } else {
            if (statusText) statusText.textContent = "Selamat datang!";
            sessionStorage.removeItem("bptd_show_preloader");
            setTimeout(() => {
              preloader.classList.add("preloader-hidden");
              setTimeout(() => {
                video?.pause();
                preloader.remove();
              }, 700);
            }, 300);
          }
        }

        requestAnimationFrame(step);
      }

      // Logout Confirmation using custom modal
      function confirmLogout(event) {
        if (window.confirmSubmit) {
          window.confirmSubmit(event, {
            title: "Konfirmasi Keluar",
            message: "Apakah Anda yakin ingin keluar dari sistem internal BPTD Kelas II Jawa Timur?",
            confirmText: "Ya, Keluar",
            confirmButtonClass: "bg-red-600 hover:bg-red-700 text-white shadow-sm",
            iconType: "warning"
          });
          return false;
        }
        return true;
      }
      window.confirmLogout = confirmLogout;

      document.addEventListener("DOMContentLoaded", () => {
        initializeSidebar();
        initializeClock();
        initializePreloader();
      });
    </script>
    @stack('scripts')
  </body>
</html>
