'use strict';
// Progressive enhancement: details/summary works without JavaScript.
// Close on Escape or an outside pointer while preserving native keyboard activation.
document.querySelectorAll('[data-nv-side-contact]').forEach((dock) => {
  const trigger = dock.querySelector('summary');
  if (!trigger) return;
  const sync = () => trigger.setAttribute('aria-label', dock.open ? 'Hızlı iletişim panelini kapat' : 'Hızlı iletişim panelini aç');
  sync();
  dock.addEventListener('toggle', sync);
  document.addEventListener('pointerdown', (event) => {
    if (dock.open && !dock.contains(event.target)) dock.open = false;
  });
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && dock.open) {
      dock.open = false;
      trigger.focus();
    }
  });
});
