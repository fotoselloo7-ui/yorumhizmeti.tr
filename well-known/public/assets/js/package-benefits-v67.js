(() => {
  'use strict';
  document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-benefit-prev],[data-benefit-next]');
    if (!button) return;
    const widget = button.closest('[data-benefit-carousel]');
    if (!widget) return;
    const pages = [...widget.querySelectorAll('[data-benefit-page]')];
    if (pages.length < 2) return;
    let selected = pages.findIndex(page => !page.hidden);
    if (selected < 0) selected = 0;
    const next = (selected + (button.hasAttribute('data-benefit-next') ? 1 : -1) + pages.length) % pages.length;
    pages.forEach((page,index) => { page.hidden = index !== next; });
    const counter = widget.querySelector('[data-benefit-counter]');
    if (counter) counter.textContent = (next + 1) + ' / ' + pages.length;
  });
})();