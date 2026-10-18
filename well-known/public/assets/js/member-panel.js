/* Progressive member interactions; all authorization and validation stays server-side. */
document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('.member-panel');
    if (!root) return;
    root.querySelectorAll('.member-filter-options').forEach(details => {
        const mobile = window.matchMedia('(max-width:680px)');
        const syncFilters = () => {
            const hasFilter = Array.from(details.querySelectorAll('select')).some(select => select.value !== '');
            details.open = !mobile.matches || hasFilter;
        };
        syncFilters();
        mobile.addEventListener('change', syncFilters);
    });
    const sidebar = root.querySelector('.member-sidebar');
    const toggle = root.querySelector('[data-member-nav-toggle]');
    if (sidebar && toggle) {
        toggle.hidden = false;
        sidebar.dataset.expanded = 'false';
        toggle.addEventListener('click', () => {
            const open = sidebar.dataset.expanded !== 'true';
            sidebar.dataset.expanded = String(open);
            toggle.setAttribute('aria-expanded', String(open));
        });
        sidebar.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                sidebar.dataset.expanded = 'false';
                toggle.setAttribute('aria-expanded', 'false');
                toggle.focus();
            }
        });
    }
    const passwordForm = root.querySelector('[data-member-password]');
    if (passwordForm) {
        const fields = passwordForm.querySelectorAll('input[type=password]');
        passwordForm.querySelector('[data-member-show-password]')?.addEventListener('change', event => {
            fields.forEach(field => { field.type = event.target.checked ? 'text' : 'password'; });
        });
        passwordForm.addEventListener('submit', event => {
            const password = passwordForm.elements.new_password.value;
            const confirmation = passwordForm.elements.new_password_confirmation.value;
            let message = '';
            if (new TextEncoder().encode(password).length > 72) message = 'Yeni şifre en fazla 72 bayt olabilir.';
            else if (password !== confirmation) message = 'Yeni şifre ve tekrarı eşleşmiyor.';
            passwordForm.querySelector('.member-form-feedback').textContent = message;
            if (message) event.preventDefault();
        });
    }
    root.querySelectorAll('[data-member-confirm]').forEach(form => {
        form.addEventListener('submit', event => {
            if (!window.confirm(form.dataset.memberConfirm)) event.preventDefault();
        });
    });
    root.querySelector('[data-member-print]')?.addEventListener('click', () => window.print());
    root.querySelectorAll('[data-member-copy]').forEach(button => {
        button.addEventListener('click', async () => {
            let feedback = button.closest('.member-bank').querySelector('.member-copy-feedback');
            if (!feedback) {
                feedback = document.createElement('p');
                feedback.className = 'member-copy-feedback';
                feedback.setAttribute('role', 'status');
                button.closest('.member-bank').append(feedback);
            }
            try { await navigator.clipboard.writeText(button.dataset.memberCopy); feedback.textContent = 'IBAN kopyalandı.'; }
            catch { feedback.textContent = 'Panoya erişilemedi. IBAN metnini seçip kopyalayabilirsiniz.'; }
        });
    });
    const conversation = root.querySelector('[data-member-conversation]');
    if (conversation) conversation.scrollTop = conversation.scrollHeight;
});
