const components = [
  ["sidebar-slot", "./sidebar.html"],
  ["navbar-slot", "./navbar.html"],
  ["dashboard-slot", "./dashboard.html"],
  ["footer-slot", "./footer.html"],
];

async function loadComponents() {
  await Promise.all(
    components.map(async ([slotId, url]) => {
      const response = await fetch(url);
      if (!response.ok) {
        throw new Error(`Failed to load ${url}: ${response.status}`);
      }

      document.getElementById(slotId).innerHTML = await response.text();
    }),
  );

  initializeSidebar();
  initializeClock();
  initializeLogout();
}

function initializeLogout() {
  const logoutButtons = document.querySelectorAll("header button");
  logoutButtons.forEach((btn) => {
    if (btn.textContent.includes("Log Out")) {
      btn.addEventListener("click", () => {
        sessionStorage.removeItem("bptd_auth");
        window.location.href = "login.html";
      });
    }
  });
}

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
      const isExpanded =
        inventoryToggle.getAttribute("aria-expanded") === "true";
      inventoryToggle.setAttribute("aria-expanded", String(!isExpanded));
      inventorySubmenu.classList.toggle("is-collapsed", isExpanded);
      inventoryChevron.classList.toggle("rotate-180", !isExpanded);
    });
  }
}

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

function runPreloader(ready) {
  const preloader = document.getElementById("app-preloader");
  const skip = document.documentElement.classList.contains("skip-preloader");
  sessionStorage.removeItem("bptd_show_preloader");

  if (!preloader || skip) {
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

  const MIN_DURATION_MS = 3000;
  const statusStages = [
    [0, "Memverifikasi sesi login..."],
    [30, "Memuat modul inventaris ATK..."],
    [60, "Menyiapkan dashboard..."],
    [90, "Sistem siap!"],
  ];

  let isReady = false;
  ready.finally(() => {
    isReady = true;
  });

  const setProgress = (percent) => {
    if (progressBar) progressBar.style.width = `${percent}%`;
    if (percentText) percentText.textContent = `${percent}%`;
    if (statusText) {
      const stage = statusStages.findLast(([threshold]) => percent >= threshold);
      statusText.textContent = stage[1];
    }
  };

  const hide = () => {
    setProgress(100);
    if (statusText) statusText.textContent = "Selamat datang!";
    setTimeout(() => {
      preloader.classList.add("preloader-hidden");
      setTimeout(() => {
        video?.pause();
        preloader.remove();
      }, 700);
    }, 300);
  };

  const startTime = performance.now();
  const tick = (now) => {
    const progress = Math.min((now - startTime) / MIN_DURATION_MS, 1);
    const eased = 1 - Math.pow(1 - progress, 3);
    // Tahan di 95% sampai komponen dashboard selesai dimuat
    setProgress(Math.floor(eased * 95));

    if (progress >= 1 && isReady) {
      hide();
    } else {
      requestAnimationFrame(tick);
    }
  };
  requestAnimationFrame(tick);
}

const componentsLoaded = loadComponents().catch((error) => {
  console.error(error);
  document.getElementById("app-status").textContent =
    "Komponen halaman gagal dimuat. Buka aplikasi melalui server web lokal.";
});

runPreloader(componentsLoaded);
