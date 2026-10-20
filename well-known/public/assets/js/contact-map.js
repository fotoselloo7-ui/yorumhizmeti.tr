'use strict';
document.querySelectorAll('[data-contact-map]').forEach(stage => {
    const button = stage.querySelector('[data-load-contact-map]');
    const frame = stage.querySelector('iframe');
    if (!button || !frame) return;
    button.addEventListener('click', () => {
        // Server validation is authoritative; defend against an unexpected DOM value too.
        try {
            const url = new URL(stage.dataset.mapSrc);
            if (url.protocol !== 'https:' || !['www.google.com', 'google.com', 'maps.google.com'].includes(url.hostname)) return;
            // Explicit user intent: do not let a hidden/lazy frame defer navigation.
            frame.loading = 'eager';
            frame.src = url.href;
            frame.hidden = false;
            stage.querySelector('.nv-contact-map-placeholder').hidden = true;
            frame.focus();
        } catch (_) { /* Keep the fallback visible for an invalid URL. */ }
    }, {once: true});
});
