(() => {
  const q = (selector, context = document) => context.querySelector(selector);
  const qa = (selector, context = document) => Array.from(context.querySelectorAll(selector));

  qa('[data-thumb]').forEach((thumb) => {
    thumb.addEventListener('click', () => {
      const target = q(`#${thumb.dataset.target}`);
      if (!target) return;
      target.src = thumb.dataset.thumb;
    });
  });

  const adminShell = q('.bm-admin-shell');
  q('[data-bm-sidebar-open]')?.addEventListener('click', () => adminShell?.classList.add('is-sidebar-open'));
  qa('[data-bm-sidebar-close]').forEach((button) => {
    button.addEventListener('click', () => adminShell?.classList.remove('is-sidebar-open'));
  });

  const syncBannerPreviewText = (inputSelector, targetSelector, fallback) => {
    const input = q(inputSelector);
    const target = q(targetSelector);
    input?.addEventListener('input', () => {
      if (target) target.textContent = input.value || fallback;
    });
  };

  const previewFile = (inputSelector, targetSelector) => {
    const input = q(inputSelector);
    const target = q(targetSelector);
    input?.addEventListener('change', () => {
      if (!target || !input.files || !input.files[0]) return;
      const reader = new FileReader();
      reader.onload = (event) => {
        target.src = event.target?.result;
      };
      reader.readAsDataURL(input.files[0]);
    });
  };

  previewFile('#bannerImageInput', '#bannerPreviewDesktop');
  previewFile('#bannerMobileImageInput', '#bannerPreviewMobile');
  syncBannerPreviewText('#bannerTitleInput', '#bannerPreviewTitle', 'Judul banner');
  syncBannerPreviewText('#bannerSubtitleInput', '#bannerPreviewSubtitle', 'Deskripsi banner');
  syncBannerPreviewText('#bannerCtaInput', '#bannerPreviewCta', 'CTA');

  q('[data-address-geolocate]')?.addEventListener('click', () => {
    const latitude = q('#addressLatitude');
    const longitude = q('#addressLongitude');
    const recordedAt = q('#addressLocationRecordedAt');
    const status = q('#addressLocationStatus');
    if (!navigator.geolocation || !latitude || !longitude || !recordedAt) {
      if (status) status.textContent = 'Perangkat tidak mendukung GPS browser.';
      return;
    }
    if (status) status.textContent = 'Mengambil lokasi GPS...';
    navigator.geolocation.getCurrentPosition((position) => {
      latitude.value = position.coords.latitude.toFixed(7);
      longitude.value = position.coords.longitude.toFixed(7);
      recordedAt.value = new Date().toISOString();
      if (status) status.textContent = `GPS tersimpan (${latitude.value}, ${longitude.value}).`;
    }, () => {
      if (status) status.textContent = 'Izin lokasi ditolak atau lokasi tidak tersedia.';
    }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
  });

  qa('[data-shipment-tracker]').forEach((node) => {
    const url = node.getAttribute('data-track-url');
    if (!url || !navigator.geolocation) return;
    const csrf = qa('input[type="hidden"]').find((input) => /csrf/i.test(input.name));
    const sendPosition = () => {
      navigator.geolocation.getCurrentPosition((position) => {
        const body = new URLSearchParams({
          latitude: position.coords.latitude.toFixed(7),
          longitude: position.coords.longitude.toFixed(7),
          accuracy: `${position.coords.accuracy || ''}`,
        });
        if (csrf) body.append(csrf.name, csrf.value);
        fetch(url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8', 'X-Requested-With': 'XMLHttpRequest' },
          body: body.toString(),
          credentials: 'same-origin',
        }).catch(() => {});
      }, () => {}, { enableHighAccuracy: true, timeout: 8000, maximumAge: 15000 });
    };
    sendPosition();
    window.setInterval(sendPosition, 30000);
  });

  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('/service-worker.js').catch(() => {});
    });
  }

  let deferredPrompt = null;
  window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    deferredPrompt = event;
    const installBtn = q('#pwaInstallBtn');
    if (!installBtn) return;
    installBtn.classList.remove('d-none');
    installBtn.addEventListener('click', async () => {
      if (!deferredPrompt) return;
      deferredPrompt.prompt();
      await deferredPrompt.userChoice;
      deferredPrompt = null;
      installBtn.classList.add('d-none');
    }, { once: true });
  });
})();
