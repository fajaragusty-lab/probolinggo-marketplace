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
