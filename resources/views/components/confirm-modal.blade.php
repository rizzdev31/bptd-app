<!-- BEGIN: BPTD Custom Confirmation Modal Dialog -->
<div
  id="bptd-confirm-modal"
  class="fixed inset-0 z-[99998] hidden bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 select-none"
  role="dialog"
  aria-modal="true"
  aria-labelledby="confirm-modal-title"
  aria-describedby="confirm-modal-message"
>
  <div
    id="bptd-confirm-card"
    class="bg-white rounded-3xl max-w-md w-full border border-slate-200/90 shadow-2xl p-6 flex flex-col items-center text-center space-y-4 transform transition-all scale-95 opacity-0"
  >
    <!-- Icon Badge Circle -->
    <div
      id="confirm-modal-icon-wrapper"
      class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-sm"
    >
      <div id="confirm-modal-icon"></div>
    </div>

    <!-- Title & Description -->
    <div class="space-y-1.5 w-full">
      <h3
        id="confirm-modal-title"
        class="text-lg font-extrabold text-slate-900 tracking-tight"
      >
        Konfirmasi Tindakan
      </h3>

      <div
        id="confirm-modal-badge"
        class="hidden inline-block px-2.5 py-1 rounded-full text-xs font-mono font-bold"
      ></div>

      <p
        id="confirm-modal-message"
        class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-sm mx-auto"
      >
        Apakah Anda yakin ingin melanjutkan tindakan ini?
      </p>
    </div>

    <!-- Action Buttons -->
    <div class="pt-2 w-full flex items-center gap-3">
      <button
        type="button"
        id="confirm-modal-cancel"
        class="flex-1 py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition"
      >
        Batal
      </button>
      <button
        type="button"
        id="confirm-modal-submit"
        class="flex-1 py-2.5 px-4 rounded-xl text-white font-semibold text-xs shadow-md transition"
      >
        Ya, Lanjutkan
      </button>
    </div>
  </div>
</div>

<script>
  (function () {
    const modal = document.getElementById('bptd-confirm-modal');
    const card = document.getElementById('bptd-confirm-card');
    const titleEl = document.getElementById('confirm-modal-title');
    const msgEl = document.getElementById('confirm-modal-message');
    const badgeEl = document.getElementById('confirm-modal-badge');
    const iconWrapper = document.getElementById('confirm-modal-icon-wrapper');
    const iconEl = document.getElementById('confirm-modal-icon');
    const cancelBtn = document.getElementById('confirm-modal-cancel');
    const submitBtn = document.getElementById('confirm-modal-submit');

    let currentCallback = null;

    const styles = {
      warning: {
        wrapper: 'bg-amber-100 text-amber-600',
        submit: 'bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-600 hover:to-yellow-600 shadow-amber-500/20',
        badge: 'bg-amber-50 text-amber-700 border border-amber-200',
        icon: `<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`
      },
      danger: {
        wrapper: 'bg-rose-100 text-rose-600',
        submit: 'bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 shadow-rose-500/20',
        badge: 'bg-rose-50 text-rose-700 border border-rose-200',
        icon: `<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>`
      },
      primary: {
        wrapper: 'bg-blue-100 text-blue-600',
        submit: 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-blue-500/20',
        badge: 'bg-blue-50 text-blue-700 border border-blue-200',
        icon: `<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`
      }
    };

    function openConfirm(options) {
      const type = options.type || 'warning';
      const conf = styles[type] || styles.warning;

      titleEl.textContent = options.title || 'Konfirmasi';
      msgEl.textContent = options.message || 'Apakah Anda yakin ingin melanjutkan?';

      if (options.badgeText) {
        badgeEl.textContent = options.badgeText;
        badgeEl.className = `inline-block px-3 py-1 rounded-full text-xs font-mono font-bold mt-1 mb-1 ${conf.badge}`;
        badgeEl.classList.remove('hidden');
      } else {
        badgeEl.classList.add('hidden');
      }

      iconWrapper.className = `w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-sm ${conf.wrapper}`;
      iconEl.innerHTML = conf.icon;

      submitBtn.className = `flex-1 py-2.5 px-4 rounded-xl text-white font-semibold text-xs shadow-md transition ${conf.submit}`;
      submitBtn.textContent = options.confirmText || 'Ya, Lanjutkan';
      cancelBtn.textContent = options.cancelText || 'Batal';

      currentCallback = options.onConfirm || null;

      modal.classList.remove('hidden');
      requestAnimationFrame(() => {
        card.classList.remove('scale-95', 'opacity-0');
        card.classList.add('scale-100', 'opacity-100');
      });
      document.body.classList.add('overflow-hidden');
    }

    function closeConfirm() {
      card.classList.remove('scale-100', 'opacity-100');
      card.classList.add('scale-95', 'opacity-0');
      setTimeout(() => {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        currentCallback = null;
      }, 150);
    }

    cancelBtn.addEventListener('click', closeConfirm);

    submitBtn.addEventListener('click', () => {
      const cb = currentCallback;
      closeConfirm();
      if (typeof cb === 'function') {
        cb();
      }
    });

    // Close on backdrop click
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        closeConfirm();
      }
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
        closeConfirm();
      }
    });

    // Global APIs
    window.confirmDialog = function (options) {
      openConfirm(options);
    };

    window.confirmSubmit = function (event, options) {
      if (event) {
        event.preventDefault();
      }
      const form = event.target.closest('form');
      if (!form) return;

      openConfirm({
        ...options,
        onConfirm: () => {
          form.submit();
        }
      });
    };
  })();
</script>
<!-- END: BPTD Custom Confirmation Modal Dialog -->
