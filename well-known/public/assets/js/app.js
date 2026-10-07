/* =====================================================
   YorumPanel Pro - Frontend JavaScript
   ===================================================== */

document.addEventListener('DOMContentLoaded', function () {

    // ── Mobile Menu Toggle ──
    const menuBtn = document.getElementById('mobileMenuBtn');
    const navMain = document.getElementById('navMain');

    if (menuBtn && navMain) {
        menuBtn.addEventListener('click', function () {
            navMain.classList.toggle('open');
            // Change icon
            const isOpen = navMain.classList.contains('open');
            menuBtn.setAttribute('aria-expanded', isOpen);
        });

        // Close menu on link click
        navMain.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                navMain.classList.remove('open');
            });
        });

        // Close on outside click
        document.addEventListener('click', function (e) {
            if (!menuBtn.contains(e.target) && !navMain.contains(e.target)) {
                navMain.classList.remove('open');
            }
        });
    }

    // ── Header scroll effect ──
    const header = document.querySelector('.site-header');
    if (header) {
        let lastScroll = 0;
        window.addEventListener('scroll', function () {
            const currentScroll = window.pageYOffset;
            if (currentScroll > 10) {
                header.style.boxShadow = '0 2px 8px rgba(0,0,0,0.06)';
            } else {
                header.style.boxShadow = 'none';
            }
            lastScroll = currentScroll;
        }, { passive: true });
    }

    // ── Flash message auto-dismiss ──
    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(function () { alert.remove(); }, 300);
        }, 5000);
    });

    // ── FAQ Accordion ──
    document.querySelectorAll('.faq-question').forEach(function (q) {
        q.addEventListener('click', function () {
            const item = this.parentElement;
            // Close siblings
            item.parentElement.querySelectorAll('.faq-item.open').forEach(function (openItem) {
                if (openItem !== item) {
                    openItem.classList.remove('open');
                }
            });
            item.classList.toggle('open');
        });
    });

    // ── Slug auto-generate ──
    const nameInput = document.querySelector('input[name="name"]');
    const slugInput = document.querySelector('input[name="slug"]');
    if (nameInput && slugInput && !slugInput.value) {
        nameInput.addEventListener('input', function () {
            slugInput.value = slugify(this.value);
        });
    }

    function slugify(str) {
        var trMap = { 'ç': 'c', 'ğ': 'g', 'ı': 'i', 'ö': 'o', 'ş': 's', 'ü': 'u',
                      'Ç': 'c', 'Ğ': 'g', 'İ': 'i', 'Ö': 'o', 'Ş': 's', 'Ü': 'u' };
        return str.split('').map(function (c) { return trMap[c] || c; }).join('')
            .toLowerCase().trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/[\s_]+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
    }

    // ── Gateway selector (checkout) ──
    document.querySelectorAll('.gateway-option').forEach(function (opt) {
        opt.addEventListener('click', function () {
            document.querySelectorAll('.gateway-option').forEach(function (o) {
                o.classList.remove('selected');
            });
            this.classList.add('selected');
            var radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        });
    });

    // ── Delete confirmations ──
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm(this.dataset.confirm || 'Bu işlemi yapmak istediğinizden emin misiniz?')) {
                e.preventDefault();
            }
        });
    });

    // ── Smooth scroll for anchor links ──
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ── Search box (homepage) ──
    var homeSearch = document.getElementById('homeSearch');
    if (homeSearch) {
        homeSearch.addEventListener('keypress', function (e) {
            if (e.key === 'Enter' && this.value.trim()) {
                window.location.href = '/kategoriler?q=' + encodeURIComponent(this.value.trim());
            }
        });
    }

    // ── Table row click ──
    document.querySelectorAll('tr[data-href]').forEach(function (row) {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function (e) {
            if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('a') || e.target.closest('button')) return;
            window.location.href = this.dataset.href;
        });
    });

    // ── Scroll Fade-In Animations ──
    var fadeElements = document.querySelectorAll('.fade-in-up');
    if (fadeElements.length > 0 && 'IntersectionObserver' in window) {
        var fadeObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    fadeObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        fadeElements.forEach(function (el, index) {
            el.style.transitionDelay = (index % 4) * 0.1 + 's';
            fadeObserver.observe(el);
        });
    } else {
        // Fallback: show all immediately
        fadeElements.forEach(function (el) {
            el.classList.add('visible');
        });
    }

    // ── Checkout Pre-fill dynamic fields ──
    const prefillData = sessionStorage.getItem('prefill_fields');
    if (prefillData && window.location.pathname.includes('/odeme')) {
        try {
            const fields = JSON.parse(prefillData);
            for (let key in fields) {
                const input = document.querySelector(`[name="${key}"]`);
                if (input && !input.value) {
                    input.value = fields[key];
                }
            }
            // Clear after filling
            sessionStorage.removeItem('prefill_fields');
        } catch(e) {}
    }
    // ── Panel Sidebar Toggle (Mobile) ──
    const panelToggle = document.getElementById('panelSidebarToggle');
    const panelSidebar = document.getElementById('panelSidebar');
    if (panelToggle && panelSidebar) {
        panelToggle.addEventListener('click', function () {
            panelSidebar.classList.toggle('open');
            const isOpen = panelSidebar.classList.contains('open');
            panelToggle.querySelector('span').textContent = isOpen ? 'Menüyü Kapat' : 'Panel Menüsü';
        });
    }

    // ── Panel File Upload Display ──
    const fileInput = document.getElementById('supportAttachment');
    const filePlaceholder = document.getElementById('filePlaceholder');
    if (fileInput && filePlaceholder) {
        fileInput.addEventListener('change', function () {
            if (this.files.length) {
                filePlaceholder.querySelector('span').textContent = this.files[0].name;
            }
        });
    }

});
