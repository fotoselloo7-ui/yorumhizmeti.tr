/* Real package thumbnails and administrator-selected reference media. */
(() => {
  const init = () => {
    document.querySelectorAll('.nv-real-product-gallery-thumbs').forEach(rail => {
      const container = rail.closest('.yv-product-gallery-v5');
      const image = container?.querySelector('.nv-service-gallery-main-image');
      if (!image) return;
      rail.querySelectorAll('[data-nv-gallery-thumb]').forEach(button => {
        button.addEventListener('click', () => {
          const src = button.getAttribute('data-nv-gallery-src');
          if (!src || !src.startsWith('/')) return;
          image.src = src;
          image.alt = button.getAttribute('data-nv-gallery-alt') || 'Hizmet görseli';
          rail.querySelectorAll('[data-nv-gallery-thumb]').forEach(b =>
            b.setAttribute('aria-pressed', String(b === button))
          );
        });
      });
    });
    document.querySelectorAll('.nv-service-references [data-nv-ref-player]').forEach(button => {
      button.addEventListener('click', () => {
        const media = button.closest('.nv-service-reference-photo');
        const src = button.getAttribute('data-nv-ref-src') || '';
        // Only known players supplied by ReferencesService::externalPlayer.
        const allowed = /^https:\/\/(?:www\.youtube-nocookie\.com\/embed\/|player\.vimeo\.com\/video\/|player\.mediadelivery\.net\/embed\/|iframe\.videodelivery\.net\/|customer-[a-z0-9]+\.cloudflarestream\.com\/)/i;
        if (!media || !allowed.test(src) || media.querySelector('iframe')) return;
        const frame = document.createElement('iframe');
        frame.title = button.getAttribute('aria-label') || 'Referans videosu';
        frame.src = src;
        frame.loading = 'lazy';
        frame.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
        frame.allowFullscreen = true;
        button.hidden = true;
        media.appendChild(frame);
      });
    });
  };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, { once: true });
  else init();
})();
