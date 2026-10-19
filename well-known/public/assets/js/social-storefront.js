/* Social-only progressive enhancement. Prices, package amounts and cart actions remain server-owned. */
document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('.social-storefront');
    if (!root) return;
    const categoryMenu = root.querySelector('.social-side-nav');
    if (categoryMenu) {
        const narrow = window.matchMedia('(max-width:900px)');
        const syncMenu = () => { categoryMenu.open = !narrow.matches; };
        syncMenu();
        narrow.addEventListener('change', syncMenu);
    }
    const filters = root.querySelector('[data-social-filter]');
    if (filters) {
        filters.addEventListener('click', event => {
            const button = event.target.closest('button[data-intent]');
            if (!button) return;
            filters.querySelectorAll('button').forEach(item => {
                item.classList.toggle('is-active', item === button);
                item.setAttribute('aria-pressed', String(item === button));
            });
            let count = 0;
            root.querySelectorAll('[data-social-intent]').forEach(card => {
                card.hidden = button.dataset.intent !== 'all' && card.dataset.socialIntent !== button.dataset.intent;
                if (!card.hidden) count++;
            });
            root.querySelector('.social-filter-feedback').textContent = count + ' hizmet gösteriliyor.';
        });
    }
});
