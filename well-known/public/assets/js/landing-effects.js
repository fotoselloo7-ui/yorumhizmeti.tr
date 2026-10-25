(() => {
    'use strict';
    const host = document.querySelector('[data-landing-theme-host]');
    const motion = window.matchMedia('(pointer: fine) and (prefers-reduced-motion: no-preference)');
    if (!host || !motion.matches) return;

    const selector = '.nvl-hero, .nvl-contact-shell, .landing-hero, .landing-contact-shell';
    let active = null;
    let frame = 0;
    let point = null;

    const clear = () => {
        if (frame) cancelAnimationFrame(frame);
        frame = 0;
        point = null;
        active?.classList.remove('landing-pointer-active');
        active = null;
    };

    host.addEventListener('pointermove', event => {
        if (!motion.matches || (event.pointerType !== 'mouse' && event.pointerType !== 'pen')) return;
        const surface = event.target.closest(selector);
        if (!surface || !host.contains(surface)) { clear(); return; }
        if (active !== surface) {
            active?.classList.remove('landing-pointer-active');
            active = surface;
            surface.classList.add('landing-pointer-active');
        }
        point = [event.clientX, event.clientY];
        if (frame) return;
        frame = requestAnimationFrame(() => {
            frame = 0;
            if (!active || !point) return;
            const rect = active.getBoundingClientRect();
            active.style.setProperty('--landing-pointer-x', `${point[0] - rect.left}px`);
            active.style.setProperty('--landing-pointer-y', `${point[1] - rect.top}px`);
        });
    }, {passive: true});
    host.addEventListener('pointerleave', clear);
    host.addEventListener('landing:themechange', clear);
    motion.addEventListener('change', clear);
})();
