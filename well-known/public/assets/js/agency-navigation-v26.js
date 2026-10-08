(() => {
  'use strict';
  const menu = document.querySelector('.nv26-menu-shell');
  if (!menu) return;
  const entries = [...menu.querySelectorAll('[data-mega-entry]')];
  const close = () => {
    entries.forEach(entry => {
      const trigger = entry.querySelector('[data-mega-trigger]');
      const panel = entry.querySelector('[data-mega-panel]');
      trigger.setAttribute('aria-expanded', 'false');
      entry.classList.remove('is-open');
      panel.hidden = true;
    });
  };
  const open = entry => {
    entries.forEach(item => {
      const active = item === entry;
      item.classList.toggle('is-open', active);
      item.querySelector('[data-mega-trigger]').setAttribute('aria-expanded', String(active));
      item.querySelector('[data-mega-panel]').hidden = !active;
    });
  };
  entries.forEach(entry => {
    const trigger = entry.querySelector('[data-mega-trigger]');
    const panel = entry.querySelector('[data-mega-panel]');
    trigger.addEventListener('click', event => {
      event.stopPropagation();
      entry.classList.contains('is-open') ? close() : open(entry);
    });
    entry.addEventListener('pointerenter', event => {
      if (window.matchMedia('(min-width: 961px) and (hover: hover)').matches && event.pointerType === 'mouse') open(entry);
    });
    entry.addEventListener('pointerleave', event => {
      if (window.matchMedia('(min-width: 961px) and (hover: hover)').matches && event.pointerType === 'mouse') close();
    });
    // Only explicit click/Enter/Space opens a panel. Opening on focus first
    // caused touch/click to immediately toggle an already-open panel closed.
    panel.addEventListener('click', e => {
      if (e.target.closest('a')) {
        close();
        menu.classList.remove('open');
        document.getElementById('mobileMenuBtn')?.setAttribute('aria-expanded','false');
      }
    });
  });
  document.addEventListener('pointerdown', e => {
    if (!menu.contains(e.target) && !document.getElementById('mobileMenuBtn')?.contains(e.target)) {
      close();
      menu.querySelector('.nv26-quick[open]')?.removeAttribute('open');
    }
  });
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      close();
      menu.querySelector('.nv26-quick[open]')?.removeAttribute('open');
    }
  });
  menu.addEventListener('focusout', e => {
    if (!e.relatedTarget || !menu.contains(e.relatedTarget)) close();
  });
  window.addEventListener('resize', () => {
    if (window.innerWidth > 960) menu.classList.remove('open');
    close();
  }, {passive: true});
})();
