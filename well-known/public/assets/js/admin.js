/**
 * YorumPanel Pro — admin.js
 * Admin Panel JavaScript
 */
document.addEventListener('DOMContentLoaded', () => {

    // Sidebar toggle (mobile)
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.admin-sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }

    // Auto slug from name/title
    const nameInput = document.querySelector('input[name="name"], input[name="title"]');
    const slugInput = document.querySelector('input[name="slug"]');
    if (nameInput && slugInput) {
        let userEdited = !!slugInput.value;
        slugInput.addEventListener('input', () => { userEdited = true; });
        nameInput.addEventListener('input', () => {
            if (!userEdited) slugInput.value = slugify(nameInput.value);
        });
    }

    // Char counter for SEO fields
    document.querySelectorAll('input[name="seo_title"], textarea[name="seo_description"]').forEach(field => {
        const hint = field.nextElementSibling;
        if (hint && hint.classList.contains('form-hint')) {
            const updateCount = () => {
                const len = field.value.length;
                const isTitle = field.name === 'seo_title';
                const min = isTitle ? 35 : 120;
                const max = isTitle ? 60 : 160;
                const color = len === 0 ? 'var(--color-text-secondary)' : (len >= min && len <= max ? 'var(--color-green)' : 'var(--color-amber)');
                hint.innerHTML = `<span style="color:${color}">${len} karakter</span> · ${min}-${max} önerilir`;
            };
            field.addEventListener('input', updateCount);
            updateCount();
        }
    });

    // Confirm delete on forms
    document.querySelectorAll('form[onsubmit]').forEach(form => {
        // Already has inline confirm
    });

    // Flash message auto-dismiss
    document.querySelectorAll('.alert-flash').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity .3s, transform .3s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // Table row click
    document.querySelectorAll('tr[data-href]').forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', () => { window.location = row.dataset.href; });
    });

    // Tab active state
    document.querySelectorAll('.tabs a').forEach(tab => {
        if (tab.href === window.location.href) tab.classList.add('active');
    });
});

function slugify(str) {
    const tr = { 'ç': 'c', 'ğ': 'g', 'ı': 'i', 'ö': 'o', 'ş': 's', 'ü': 'u', 'Ç': 'c', 'Ğ': 'g', 'İ': 'i', 'Ö': 'o', 'Ş': 's', 'Ü': 'u' };
    return str.toLowerCase().replace(/[çğışöüÇĞİŞÖÜ]/g, c => tr[c] || c)
        .replace(/[^a-z0-9\s-]/g, '').replace(/[\s_]+/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
}
