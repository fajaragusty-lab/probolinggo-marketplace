(() => {
  const q = (s, c = document) => c.querySelector(s);
  const qa = (s, c = document) => Array.from(c.querySelectorAll(s));

  qa('[data-thumb]').forEach((thumb) => {
    thumb.addEventListener('click', () => {
      const target = q('#' + thumb.dataset.target);
      if (!target) return;
      target.src = thumb.dataset.thumb;
    });
  });

  const desktopInput = q('#bannerImageInput');
  const mobileInput = q('#bannerMobileImageInput');
  const desktopPreview = q('#bannerPreviewDesktop');
  const mobilePreview = q('#bannerPreviewMobile');
  const titleInput = q('#bannerTitleInput');
  const subtitleInput = q('#bannerSubtitleInput');
  const ctaInput = q('#bannerCtaInput');
  const ctaLabel = q('#bannerPreviewCta');
  const titlePreview = q('#bannerPreviewTitle');
  const subtitlePreview = q('#bannerPreviewSubtitle');

  const previewFile = (input, target) => {
    if (!input || !target || !input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = (e) => {
      target.src = e.target?.result;
    };
    reader.readAsDataURL(input.files[0]);
  };

  desktopInput?.addEventListener('change', () => previewFile(desktopInput, desktopPreview));
  mobileInput?.addEventListener('change', () => previewFile(mobileInput, mobilePreview));

  titleInput?.addEventListener('input', () => {
    if (titlePreview) titlePreview.textContent = titleInput.value || 'Judul banner';
  });

  subtitleInput?.addEventListener('input', () => {
    if (subtitlePreview) subtitlePreview.textContent = subtitleInput.value || 'Deskripsi banner';
  });

  ctaInput?.addEventListener('input', () => {
    if (ctaLabel) ctaLabel.textContent = ctaInput.value || 'CTA';
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
