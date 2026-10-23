(() => {
    'use strict';
    const host = document.querySelector('[data-landing-theme-host]');
    if (!host) return;
    const dialog = host.querySelector('.nvl-theme-dialog');
    const toggle = host.querySelector('[data-theme-open]');
    if (!dialog || !toggle) return;
    const choices = [...dialog.querySelectorAll('[data-theme-choice]')];
    const published = host.dataset.defaultTheme;
    const key = 'landing_theme_' + host.dataset.pageId;
    const original = host.querySelector('main').cloneNode(true);
    const legacy = published === '';
    const status = dialog.querySelector('[data-theme-status]');
    const fragment = template => {
        const copy = template.content.cloneNode(true);
        copy.querySelectorAll('[data-nvl-h1]').forEach(title => {
            const heading = document.createElement('h1');
            heading.append(...title.childNodes);
            title.replaceWith(heading);
        });
        return copy;
    };
    const apply = (theme, remember = false) => {
        const choice = choices.find(item => item.dataset.themeChoice === theme);
        if (!choice && theme !== published) return;
        if (!choice && legacy) {
            host.querySelector('main').replaceWith(original.cloneNode(true));
        } else {
            let main = host.querySelector('main');
            if (!main.matches('.nv-landing')) {
                const modern = host.querySelector('[data-landing-modern]');
                if (!modern) return;
                main.replaceWith(fragment(modern));
                main = host.querySelector('main');
            }
            const profile = JSON.parse(choice.dataset.themeConfig);
            const hero = host.querySelector('template[data-landing-hero="' + theme + '"]');
            if (!hero) return;
            main.dataset.theme = theme;
            main.querySelector('[data-theme-hero]').replaceChildren(fragment(hero));
            main.querySelectorAll('[data-component]').forEach(block => {
                block.dataset.variant = profile[block.dataset.component] || 'generic';
            });
        }
        choices.forEach(item => item.setAttribute('aria-pressed', String(item.dataset.themeChoice === theme)));
        if (remember && host.dataset.adminPreview !== '1') {
            try { if (theme === published) localStorage.removeItem(key); else localStorage.setItem(key, theme); }
            catch (_) { /* Private browsing may prohibit storage; the current view still works. */ }
        }
        status.textContent = choice ? choice.querySelector('strong').textContent + ' seçildi.' : 'Yayınlanan görünüm seçildi.';
        host.dispatchEvent(new CustomEvent('landing:themechange', {detail: {theme}}));
    };
    toggle.addEventListener('click', () => { dialog.showModal(); dialog.scrollTop = 0; dialog.querySelector('[data-theme-close]').focus({preventScroll: true}); });
    dialog.querySelector('[data-theme-close]').addEventListener('click', () => dialog.close());
    dialog.addEventListener('close', () => toggle.focus());
    dialog.addEventListener('click', event => { if (event.target === dialog) dialog.close(); });
    choices.forEach(choice => choice.addEventListener('click', () => { apply(choice.dataset.themeChoice, true); dialog.close(); }));
    dialog.querySelector('[data-theme-reset]').addEventListener('click', () => { apply(published, true); dialog.close(); });
    // Server default always wins in an authorized preview; no query-string theme for guests.
    if (host.dataset.adminPreview !== '1') {
        try { const saved = localStorage.getItem(key); if (choices.some(item => item.dataset.themeChoice === saved)) apply(saved); }
        catch (_) { /* SSR remains authoritative if storage is unavailable. */ }
    }
})();
