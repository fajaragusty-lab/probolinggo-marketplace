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
    const accuracy = q('#addressAccuracy');
    const recordedAt = q('#addressLocationRecordedAt');
    const status = q('#addressLocationStatus');
    const errorEl = q('#addressGpsError');
    const resultEl = q('#addressGpsResult');
    const latSpan = q('#addressGpsLat');
    const lngSpan = q('#addressGpsLng');
    const accSpan = q('#addressGpsAcc');
    const accRow = q('#addressGpsAccRow');
    const btn = q('#addressGpsBtn');

    const setStatus = (msg) => { if (status) status.textContent = msg; };
    const showError = (msg) => {
      if (errorEl) { errorEl.textContent = msg; errorEl.classList.remove('d-none'); }
      if (resultEl) resultEl.classList.add('d-none');
    };
    const hideError = () => { if (errorEl) errorEl.classList.add('d-none'); };

    if (!navigator.geolocation || !latitude || !longitude) {
      showError('Browser Anda tidak mendukung Geolocation. Gunakan browser modern seperti Chrome atau Firefox.');
      return;
    }
    hideError();
    if (btn) { btn.disabled = true; btn.textContent = '⏳ Mengambil lokasi...'; }
    setStatus('Sedang mengambil lokasi GPS, mohon tunggu...');

    navigator.geolocation.getCurrentPosition(
      (position) => {
        if (btn) { btn.disabled = false; btn.textContent = '📍 Gunakan Lokasi Saya'; }
        const lat = position.coords.latitude.toFixed(7);
        const lng = position.coords.longitude.toFixed(7);
        const acc = position.coords.accuracy;
        latitude.value = lat;
        longitude.value = lng;
        if (accuracy) accuracy.value = acc ? acc.toFixed(2) : '';
        if (recordedAt) recordedAt.value = new Date().toISOString();
        if (latSpan) latSpan.textContent = lat;
        if (lngSpan) lngSpan.textContent = lng;
        if (accSpan && acc) {
          accSpan.textContent = acc.toFixed(1);
          if (accRow) accRow.classList.remove('d-none');
        }
        if (resultEl) resultEl.classList.remove('d-none');
        setStatus('Lokasi berhasil ditangkap. Klik "Simpan Alamat" untuk menyimpan.');
        hideError();
      },
      (err) => {
        if (btn) { btn.disabled = false; btn.textContent = '📍 Gunakan Lokasi Saya'; }
        let msg;
        switch (err.code) {
          case err.PERMISSION_DENIED:
            msg = 'Izin lokasi ditolak. Aktifkan izin lokasi di pengaturan browser Anda lalu coba lagi.';
            break;
          case err.POSITION_UNAVAILABLE:
            msg = 'Lokasi tidak tersedia. Pastikan GPS perangkat aktif dan Anda memiliki koneksi internet.';
            break;
          case err.TIMEOUT:
            msg = 'Waktu habis saat mengambil lokasi. Coba lagi atau pindah ke area yang memiliki sinyal GPS lebih baik.';
            break;
          default:
            msg = 'Gagal mendapatkan lokasi GPS. Silakan coba lagi.';
        }
        showError(msg);
        setStatus('Gagal mengambil lokasi GPS.');
      },
      { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );
  });

  // Courier GPS toggle
  const courierGpsToggleBtn = q('[data-courier-gps-toggle]');
  const courierGpsStatus = q('#courierGpsStatus');
  if (courierGpsToggleBtn && navigator.geolocation) {
    let gpsActive = false;
    let gpsIntervals = [];

    const csrf = qa('input[type="hidden"]').find((input) => /csrf/i.test(input.name));

    const sendPosition = (url) => {
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
        }).then(() => {
          const now = new Date().toLocaleTimeString('id-ID');
          const shipmentId = url.match(/shipments\/(\d+)\/track/)?.[1];
          if (shipmentId) {
            const el = q(`#gpsUpdate_${shipmentId}`);
            if (el) { el.textContent = `GPS terakhir: ${now}`; el.classList.remove('d-none'); }
          }
          if (courierGpsStatus) courierGpsStatus.textContent = `GPS aktif — diperbarui ${now}`;
        }).catch(() => {});
      }, () => {
        if (courierGpsStatus) courierGpsStatus.textContent = 'GPS: gagal mengambil lokasi';
      }, { enableHighAccuracy: true, timeout: 8000, maximumAge: 15000 });
    };

    courierGpsToggleBtn.addEventListener('click', () => {
      if (!gpsActive) {
        gpsActive = true;
        courierGpsToggleBtn.textContent = '🛑 Nonaktifkan GPS';
        courierGpsToggleBtn.classList.replace('btn-outline-primary', 'btn-outline-danger');
        if (courierGpsStatus) courierGpsStatus.textContent = 'GPS: mengambil lokasi...';

        qa('[data-shipment-id][data-track-url]').forEach((node) => {
          const url = node.getAttribute('data-track-url');
          sendPosition(url);
          const id = window.setInterval(() => sendPosition(url), 30000);
          gpsIntervals.push(id);
        });
      } else {
        gpsActive = false;
        gpsIntervals.forEach((id) => window.clearInterval(id));
        gpsIntervals = [];
        courierGpsToggleBtn.textContent = '📍 Aktifkan GPS';
        courierGpsToggleBtn.classList.replace('btn-outline-danger', 'btn-outline-primary');
        if (courierGpsStatus) courierGpsStatus.textContent = 'GPS: nonaktif';
      }
    });
  } else if (courierGpsToggleBtn && !navigator.geolocation) {
    courierGpsToggleBtn.disabled = true;
    courierGpsToggleBtn.textContent = 'GPS tidak didukung';
    if (courierGpsStatus) courierGpsStatus.textContent = 'GPS: tidak didukung browser';
  }

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
