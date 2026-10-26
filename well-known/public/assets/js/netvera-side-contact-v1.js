'use strict';
// On a regional landing page the theme selector is part of the same
// fixed left-edge rail, never an independently positioned overlapping button.
// It remains one selector per page, with its original dialog and handlers intact.
const nvSideRail = document.querySelector('[data-nv-contact-rail]');
const nvLandingThemeTools = document.querySelector('[data-landing-theme-host] .landing-theme-tools');
if (nvSideRail && nvLandingThemeTools) {
  nvSideRail.appendChild(nvLandingThemeTools);
  nvSideRail.classList.add('nv-contact-rail--themed');
}

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
