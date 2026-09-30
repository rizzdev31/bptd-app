<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Login - INTERNAL BPTD Kelas II Jawa Timur</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&amp;display=swap"
      rel="stylesheet"
    />
    <!-- Tailwind CSS Configuration & CDN -->
    <script>
      tailwind = {
        config: {
          theme: {
            extend: {
              colors: {
                brand: {
                  blue: "#007BFB",
                  "blue-hover": "#006CE0",
                  dark: "#111827",
                  muted: "#374151",
                },
              },
              fontFamily: {
                sans: [
                  "Inter",
                  "system-ui",
                  "-apple-system",
                  "BlinkMacSystemFont",
                  '"Segoe UI"',
                  "Roboto",
                  "sans-serif",
                ],
              },
              boxShadow: {
                "soft-input":
                  "0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04)",
                pill: "0 2px 4px 0 rgba(0, 0, 0, 0.03)",
                btn: "0 4px 14px 0 rgba(0, 123, 251, 0.35)",
              },
            },
          },
        },
      };
    </script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <style data-purpose="custom-inputs">
      body {
        background-color: #ffffff;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
      }
      /* Preloader Critical CSS - Ensures full-screen white coverage from frame 0 before external CSS/scripts load */
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
      /* Subtle custom inset and smooth border finish for the realistic pill input aesthetics */
      .field-input {
        background-color: #fafafa;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease-in-out;
      }
      .field-input:focus {
        background-color: #ffffff;
        border-color: #007bfb;
        box-shadow: 0 0 0 3px rgba(0, 123, 251, 0.12);
        outline: none;
      }
      .bg-linear-to-r {
        background-image: linear-gradient(to right, var(--tw-gradient-stops));
      }
    </style>
  </head>
  <body
    class="bg-white min-h-screen text-slate-800 font-sans antialiased flex flex-col justify-center selection:bg-blue-500 selection:text-white"
  >
    <!-- BEGIN: Preloader Screen -->
    <div
      id="app-preloader"
      class="fixed inset-0 z-50 bg-white flex flex-col items-center justify-center transition-all duration-700 ease-in-out select-none"
      role="status"
      aria-live="polite"
      aria-busy="true"
      aria-label="Memuat aplikasi BPTD Kelas II Jawa Timur"
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
          <p class="text-[11px] font-medium text-[#007BFB] tracking-widest uppercase">
            Internal Management System
          </p>
        </div>

        <!-- Indikator Loading & Progress Bar -->
        <div class="w-60 sm:w-64 mt-6">
          <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden shadow-inner relative">
            <div
              id="preloader-progress-bar"
              class="h-full bg-linear-to-r from-blue-500 to-[#007BFB] rounded-full w-0 transition-all ease-out"
            ></div>
          </div>
          <div class="flex justify-between items-center mt-2.5 text-[11px] text-slate-400 font-mono">
            <span id="preloader-status-text">Memuat sistem...</span>
            <span id="preloader-percentage-text">0%</span>
          </div>
        </div>
      </div>
    </div>
    <script>
      (function() {
        const hasErrors = {{ $errors->any() ? 'true' : 'false' }};
        const isLogout = {{ (session('show_logout_preloader') || session('status')) ? 'true' : 'false' }};
        const seenLogin = sessionStorage.getItem('bptd_login_preloader_seen') === 'true';

        // Preloader HANYA dijalankan untuk:
        // 1. Masuk ke login pertama kali (!seenLogin && !hasErrors)
        // 2. Transisi setelah logout (isLogout)
        // Dan TIDAK dijalankan jika ada error validasi atau refresh biasa
        const shouldRun = !hasErrors && (isLogout || !seenLogin);

        if (!shouldRun) {
          const preloader = document.getElementById('app-preloader');
          if (preloader) {
            preloader.style.setProperty('display', 'none', 'important');
            preloader.remove();
          }
          window.__bptd_skip_login_preloader = true;
        } else {
          window.__bptd_is_logout_preloader = isLogout;
        }
      })();
    </script>
    <!-- END: Preloader Screen -->

    <!-- BEGIN: MainContent -->
    <main
      class="w-full max-w-7xl mx-auto px-6 py-8 md:py-12 lg:px-12 flex-1 flex items-center"
      data-purpose="login-container"
    >
      <div
        class="w-full grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center"
      >
        <!-- BEGIN: LeftColumn -->
        <!-- Contains ministry logo, title, description, authentication form and status indicators -->
        <section
          class="lg:col-span-5 xl:col-span-5 flex flex-col justify-center z-10"
          data-purpose="auth-section"
        >
          <!-- Header Brand: Ministry emblem & office name -->
          <header
            class="flex items-center gap-3.5 mb-10"
            data-purpose="header-brand"
          >
            <div
              class="w-14 h-14 shrink-0 flex items-center justify-center"
            >
              <img
                alt="Logo Kementerian Perhubungan"
                class="w-full h-full object-contain"
                src="{{ asset('assets/logo-kemenhub.png') }}"
              />
            </div>
            <div class="leading-tight">
              <h1
                class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight"
              >
                BPTD Kelas II Jawa Timur
              </h1>
              <p class="text-xs sm:text-sm font-medium text-slate-600 mt-0.5">
                Kementerian Perhubungan
              </p>
            </div>
          </header>
          <!-- System Identification & Description -->
          <div class="mb-7" data-purpose="system-info">
            <h2
              class="text-2xl sm:text-3xl font-black text-black tracking-tight uppercase mb-2"
            >
              INTERNAL BPTD
            </h2>
            <p
              class="text-xs sm:text-[13px] leading-relaxed text-slate-700 max-w-md font-normal"
            >
              Aplikasi internal terintegrasi BPTD Kelas II Jawa Timur untuk
              mendukung proses kerja yang lebih rapi dan sistematis.
            </p>
          </div>
          <!-- Alerts: Session status & Validation errors -->
          @if ($errors->any())
            <div class="mb-5 p-3.5 bg-red-50/90 border border-red-200 rounded-2xl text-red-700 text-xs sm:text-sm font-medium flex items-start gap-2.5 shadow-sm max-w-sm w-full">
              <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
              <div class="flex-1">
                <ul class="list-disc list-inside space-y-0.5">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          @endif

          @if (session('status'))
            <div class="mb-5 p-3.5 bg-emerald-50/90 border border-emerald-200 rounded-2xl text-emerald-700 text-xs sm:text-sm font-medium flex items-center gap-2.5 shadow-sm max-w-sm w-full">
              <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              <span>{{ session('status') }}</span>
            </div>
          @endif

          <!-- Authentication Form -->
          <form
            action="{{ route('login') }}"
            class="space-y-4 max-w-sm w-full"
            data-purpose="login-form"
            method="POST"
          >
            @csrf
            <!-- Username / NIP Field -->
            <div class="space-y-1.5">
              <label
                class="block text-sm font-medium text-slate-800"
                for="username"
              >
                Username / NIP :
              </label>
              <input
                autocomplete="username"
                class="field-input block w-full px-4 py-3.5 rounded-2xl text-slate-900 text-sm shadow-soft-input transition"
                id="username"
                name="username"
                value="{{ old('username') }}"
                required
                type="text"
              />
            </div>
            <!-- Password Field -->
            <div class="space-y-1.5">
              <label
                class="block text-sm font-medium text-slate-800"
                for="password"
              >
                Password :
              </label>
              <input
                autocomplete="current-password"
                class="field-input block w-full px-4 py-3.5 rounded-2xl text-slate-900 text-sm shadow-soft-input transition tracking-widest"
                id="password"
                name="password"
                required
                type="password"
              />
            </div>
            <!-- Submit Button -->
            <div class="pt-2">
              <button
                class="w-28 py-2.5 px-6 bg-[#007BFB] hover:bg-[#006CE0] active:scale-95 text-white font-medium text-sm rounded-xl transition duration-150 ease-in-out shadow-btn focus:outline-none focus:ring-2 focus:ring-[#007BFB] focus:ring-offset-2"
                data-purpose="submit-button"
                type="submit"
              >
                Login
              </button>
            </div>
          </form>
          <!-- Status Badges: Role and Dynamic Realtime Clock -->
          <footer
            class="mt-14 sm:mt-20 flex flex-wrap items-center gap-3 text-xs sm:text-sm text-slate-800"
            data-purpose="footer-status-pills"
          >
            <!-- Superadmin Indicator Pill -->
            <div
              class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-full shadow-pill font-medium"
              data-purpose="badge-role"
            >
              <svg
                aria-hidden="true"
                class="w-4 h-4 text-[#007BFB]"
                fill="none"
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                viewBox="0 0 24 24"
              >
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                <path d="M12 11a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"></path>
                <path d="M7 17.5a5 5 0 0 1 10 0"></path>
              </svg>
              <span class="">Superadmin</span>
            </div>
            <!-- Real-Time Digital Clock Pill -->
            <div
              class="inline-flex items-center gap-2.5 px-4 py-2 bg-white border border-slate-200 rounded-full shadow-pill font-semibold text-slate-800 tabular-nums"
              data-purpose="badge-clock"
            >
              <svg
                aria-hidden="true"
                class="w-4 h-4 text-slate-700"
                fill="none"
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.8"
                viewBox="0 0 24 24"
              >
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <span aria-live="polite" id="digital-clock" class=""
                >13 : 50 : 09</span
              >
            </div>
          </footer>
        </section>
        <!-- END: LeftColumn -->
        <!-- BEGIN: RightColumn -->
        <!-- Visual Display: Animated Bus Showcase -->
        <section
          class="lg:col-span-7 xl:col-span-7 order-last flex items-center justify-center relative overflow-visible"
          data-purpose="visual-bus-display"
        >
          <div
            class="w-full flex justify-center items-center relative py-4 lg:py-0"
          >
            <div class="w-full flex items-center justify-center relative">
              <video
                id="bus-video"
                loop
                muted
                playsinline
                preload="auto"
                class="w-full h-auto max-w-4xl lg:max-w-5xl xl:scale-110 2xl:scale-120 object-contain mix-blend-multiply pointer-events-none select-none transition-transform duration-700 ease-out"
                style="filter: contrast(102%) brightness(102.5%); mask-image: radial-gradient(ellipse 96% 90% at 50% 50%, black 82%, transparent 100%); -webkit-mask-image: radial-gradient(ellipse 96% 90% at 50% 50%, black 82%, transparent 100%);"
              >
                <source src="{{ asset('assets/bus.mp4') }}" type="video/mp4" />
                Browser Anda tidak mendukung tag video.
              </video>
            </div>
          </div>
        </section>
        <!-- END: RightColumn -->
      </div>
    </main>
    <!-- END: MainContent -->
    <!-- BEGIN: Scripts -->
    <script data-purpose="app-logic">
      // Preloader Controller (Durasi terarah: Awal masuk & Logout)
      (function initializePreloader() {
        const mainVideo = document.getElementById("bus-video");
        const usernameInput = document.getElementById("username");

        if (window.__bptd_skip_login_preloader) {
          if (mainVideo) {
            mainVideo.muted = true;
            mainVideo.play().catch(() => {});
          }
          if (usernameInput) {
            usernameInput.focus();
          }
          return;
        }

        const preloader = document.getElementById("app-preloader");
        const progressBar = document.getElementById("preloader-progress-bar");
        const percentText = document.getElementById("preloader-percentage-text");
        const statusText = document.getElementById("preloader-status-text");
        const preloaderVideo = document.getElementById("preloader-bus-video");

        if (!preloader || !progressBar || !percentText) return;

        if (preloaderVideo) {
          preloaderVideo.muted = true;
          preloaderVideo.play().catch(() => {});
        }

        const isLogout = Boolean(window.__bptd_is_logout_preloader);
        const TOTAL_DURATION_MS = isLogout ? 2000 : 2800;
        const startTime = performance.now();

        const statusStages = isLogout
          ? [
              { threshold: 0, text: "Memproses keluar dari sistem..." },
              { threshold: 30, text: "Mengakhiri sesi otentikasi..." },
              { threshold: 65, text: "Membersihkan token keamanan..." },
              { threshold: 95, text: "Sesi telah berakhir dengan aman" },
            ]
          : [
              { threshold: 0, text: "Memulai sistem aplikasi..." },
              { threshold: 25, text: "Memeriksa modul inventaris ATK..." },
              { threshold: 55, text: "Memverifikasi konfigurasi keamanan..." },
              { threshold: 82, text: "Menyiapkan antarmuka pengguna..." },
              { threshold: 98, text: "Sistem siap!" },
            ];

        function updateProgress(currentTime) {
          const elapsed = currentTime - startTime;
          const progress = Math.min(elapsed / TOTAL_DURATION_MS, 1);
          const easeProgress = 1 - Math.pow(1 - progress, 3);
          const percent = Math.floor(easeProgress * 100);

          progressBar.style.width = `${percent}%`;
          percentText.textContent = `${percent}%`;

          for (let i = statusStages.length - 1; i >= 0; i--) {
            if (percent >= statusStages[i].threshold) {
              if (statusText.textContent !== statusStages[i].text) {
                statusText.textContent = statusStages[i].text;
              }
              break;
            }
          }

          if (progress < 1) {
            requestAnimationFrame(updateProgress);
          } else {
            progressBar.style.width = "100%";
            percentText.textContent = "100%";
            statusText.textContent = isLogout ? "Sampai jumpa kembali!" : "Selamat datang!";

            sessionStorage.setItem("bptd_login_preloader_seen", "true");
            if (isLogout) {
              sessionStorage.removeItem("bptd_auth");
              sessionStorage.removeItem("bptd_show_preloader");
            }

            setTimeout(() => {
              preloader.classList.add("preloader-hidden");
              preloader.setAttribute("aria-busy", "false");

              if (mainVideo) {
                mainVideo.muted = true;
                mainVideo.play().catch(() => {});
              }

              if (usernameInput) {
                usernameInput.focus();
              }

              setTimeout(() => {
                if (preloaderVideo) preloaderVideo.pause();
                preloader.remove();
              }, 700);
            }, 300);
          }
        }

        requestAnimationFrame(updateProgress);
      })();

      // Update live clock every second to match format "HH : MM : SS"
      function updateClock() {
        const clockElement = document.getElementById("digital-clock");
        if (!clockElement) return;

        const now = new Date();
        const hours = String(now.getHours()).padStart(2, "0");
        const minutes = String(now.getMinutes()).padStart(2, "0");
        const seconds = String(now.getSeconds()).padStart(2, "0");

        clockElement.textContent = `${hours} : ${minutes} : ${seconds}`;
      }

      // Initialize clock and setup recurring tick
      updateClock();
      setInterval(updateClock, 1000);

      // Handle login form submission
      const loginForm = document.querySelector("form");
      if (loginForm) {
        loginForm.addEventListener("submit", () => {
          sessionStorage.setItem("bptd_auth", "true");
          // Tandai agar dashboard menampilkan preloader saat dimuat
          sessionStorage.setItem("bptd_show_preloader", "true");
        });
      }
    </script>
    <!-- END: Scripts -->
  </body>
</html>