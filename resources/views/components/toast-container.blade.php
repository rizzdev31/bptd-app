<!-- BEGIN: Global Floating Toast Notification Container -->
<div
  id="toast-container"
  class="fixed top-28 sm:top-24 right-3 sm:right-6 flex flex-col gap-3 pointer-events-none max-w-sm sm:max-w-md w-full px-3 sm:px-0"
  style="z-index: 99999 !important;"
  aria-live="polite"
  aria-atomic="true"
></div>

<style>
  #toast-container {
    z-index: 99999 !important;
  }
  @keyframes toastSlideIn {
    0% {
      opacity: 0;
      transform: translateY(-16px) scale(0.96);
    }
    100% {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }
  @keyframes toastSlideOut {
    0% {
      opacity: 1;
      transform: translateY(0) scale(1);
      max-height: 160px;
      margin-bottom: 12px;
    }
    100% {
      opacity: 0;
      transform: translateX(40px) scale(0.95);
      max-height: 0;
      margin-bottom: 0;
      padding-top: 0;
      padding-bottom: 0;
    }
  }
  .toast-enter {
    animation: toastSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }
  .toast-exit {
    animation: toastSlideOut 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }
</style>

<script>
  (function () {
    const toastConfig = {
      success: {
        bg: 'bg-white',
        border: 'border-emerald-200/90',
        badgeBg: 'bg-emerald-100 text-emerald-800',
        badgeText: 'BERHASIL',
        iconBg: 'bg-emerald-500 text-white',
        barColor: 'bg-emerald-500',
        iconSvg: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>`
      },
      warning: {
        bg: 'bg-white',
        border: 'border-amber-200/90',
        badgeBg: 'bg-amber-100 text-amber-800',
        badgeText: 'PERHATIAN',
        iconBg: 'bg-amber-500 text-white',
        barColor: 'bg-amber-500',
        iconSvg: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`
      },
      error: {
        bg: 'bg-white',
        border: 'border-rose-200/90',
        badgeBg: 'bg-rose-100 text-rose-800',
        badgeText: 'GAGAL',
        iconBg: 'bg-rose-500 text-white',
        barColor: 'bg-rose-500',
        iconSvg: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"></path></svg>`
      },
      danger: {
        bg: 'bg-white',
        border: 'border-rose-200/90',
        badgeBg: 'bg-rose-100 text-rose-800',
        badgeText: 'GAGAL',
        iconBg: 'bg-rose-500 text-white',
        barColor: 'bg-rose-500',
        iconSvg: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"></path></svg>`
      },
      info: {
        bg: 'bg-white',
        border: 'border-blue-200/90',
        badgeBg: 'bg-blue-100 text-blue-800',
        badgeText: 'INFORMASI',
        iconBg: 'bg-blue-600 text-white',
        barColor: 'bg-blue-600',
        iconSvg: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`
      }
    };

    window.showToast = function (options, typeArg) {
      const container = document.getElementById('toast-container');
      if (!container) return;

      let type = 'info';
      let title = '';
      let message = '';
      let duration = 4000;

      const knownTypes = ['success', 'warning', 'error', 'danger', 'info'];

      if (typeof options === 'string') {
        // Mendukung kedua urutan parameter: showToast('error', 'Pesan') ATAU showToast('Pesan', 'error')
        if (typeof typeArg === 'string' && knownTypes.includes(options.toLowerCase()) && !knownTypes.includes(typeArg.toLowerCase())) {
          type = options.toLowerCase();
          message = typeArg;
        } else {
          message = options;
          type = (typeof typeArg === 'string' && knownTypes.includes(typeArg.toLowerCase())) ? typeArg.toLowerCase() : 'info';
        }
      } else if (options && typeof options === 'object') {
        message = options.message || '';
        type = options.type || typeArg || 'info';
        title = options.title || '';
        duration = options.duration || 4000;
      }

      if (type === 'danger') type = 'error';

      const conf = toastConfig[type] || toastConfig.info;
      const finalTitle = title || conf.badgeText;

      const toast = document.createElement('div');
      toast.className = `toast-enter pointer-events-auto relative overflow-hidden rounded-2xl ${conf.bg} border ${conf.border} shadow-xl shadow-slate-900/10 transition-all backdrop-blur-xs`;

      toast.innerHTML = `
        <div class="p-4 flex items-start gap-3.5">
          <div class="w-8 h-8 rounded-xl ${conf.iconBg} flex items-center justify-center shrink-0 shadow-xs mt-0.5">
            ${conf.iconSvg}
          </div>
          <div class="flex-1 min-w-0 pr-2">
            <div class="flex items-center gap-2">
              <span class="text-[10px] font-black tracking-wider uppercase px-2 py-0.5 rounded-full ${conf.badgeBg}">
                ${finalTitle}
              </span>
              <span class="text-[10px] text-slate-400 font-mono">BPTD Persediaan</span>
            </div>
            <p class="text-xs sm:text-[13px] text-slate-700 font-medium leading-relaxed mt-1.5 break-words">
              ${message}
            </p>
          </div>
          <button
            type="button"
            class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition shrink-0 cursor-pointer"
            aria-label="Tutup notifikasi"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <!-- Progress Timer Bar -->
        <div class="w-full bg-slate-100 h-1 absolute bottom-0 left-0 overflow-hidden">
          <div class="toast-progress h-full ${conf.barColor} w-full transition-all linear"></div>
        </div>
      `;

      container.appendChild(toast);

      const closeBtn = toast.querySelector('button');
      const progressBar = toast.querySelector('.toast-progress');

      let startTime = Date.now();
      let remaining = duration;
      let timer = null;
      let isPaused = false;

      function dismiss() {
        if (toast.classList.contains('toast-exit')) return;
        toast.classList.remove('toast-enter');
        toast.classList.add('toast-exit');
        setTimeout(() => {
          toast.remove();
        }, 320);
      }

      function updateProgress() {
        if (!isPaused) {
          const elapsed = Date.now() - startTime;
          const percent = Math.max(0, 100 - (elapsed / duration) * 100);
          progressBar.style.width = `${percent}%`;

          if (percent <= 0) {
            dismiss();
            return;
          }
        }
        timer = requestAnimationFrame(updateProgress);
      }

      timer = requestAnimationFrame(updateProgress);

      toast.addEventListener('mouseenter', () => {
        isPaused = true;
      });

      toast.addEventListener('mouseleave', () => {
        isPaused = false;
        startTime = Date.now() - (1 - parseFloat(progressBar.style.width) / 100) * duration;
      });

      closeBtn.addEventListener('click', () => {
        cancelAnimationFrame(timer);
        dismiss();
      });
    };

    // Auto-detect Laravel Flash Sessions on Page Load
    document.addEventListener('DOMContentLoaded', () => {
      @if (session('status'))
        window.showToast({
          type: 'success',
          title: 'Berhasil',
          message: @json(session('status'))
        });
      @endif

      @if (session('success'))
        window.showToast({
          type: 'success',
          title: 'Berhasil',
          message: @json(session('success'))
        });
      @endif

      @if (session('toast_success'))
        window.showToast({
          type: 'success',
          title: 'Berhasil',
          message: @json(session('toast_success'))
        });
      @endif

      @if (session('error'))
        window.showToast({
          type: 'error',
          title: 'Kesalahan',
          message: @json(session('error'))
        });
      @endif

      @if (session('toast_error'))
        window.showToast({
          type: 'error',
          title: 'Kesalahan',
          message: @json(session('toast_error'))
        });
      @endif

      @if (session('warning'))
        window.showToast({
          type: 'warning',
          title: 'Perhatian',
          message: @json(session('warning'))
        });
      @endif

      @if (session('toast_warning'))
        window.showToast({
          type: 'warning',
          title: 'Perhatian',
          message: @json(session('toast_warning'))
        });
      @endif

      @if (session('info'))
        window.showToast({
          type: 'info',
          title: 'Informasi',
          message: @json(session('info'))
        });
      @endif

      @if (session('toast_info'))
        window.showToast({
          type: 'info',
          title: 'Informasi',
          message: @json(session('toast_info'))
        });
      @endif

      @if ($errors->any())
        window.showToast({
          type: 'error',
          title: 'Periksa Formulir',
          message: @json($errors->first())
        });
      @endif
    });
  })();
</script>
<!-- END: Global Floating Toast Notification Container -->
