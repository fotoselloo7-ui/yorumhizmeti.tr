(() => {
  'use strict';

  const switcher = document.querySelector('[data-featured-switcher]');
  if (switcher) {
    const tabs = Array.from(switcher.querySelectorAll('[data-featured-tab]'));
    const panes = Array.from(switcher.querySelectorAll('[data-featured-pane]'));
    const title = switcher.querySelector('[data-featured-title]');
    const subtitle = switcher.querySelector('[data-featured-subtitle]');
    const avatar = switcher.querySelector('[data-featured-avatar]');

    const classNames = [
      'instagram','tiktok','youtube','facebook','twitter','threads','telegram',
      'spotify','discord','linkedin','twitch','google','web','seo','ecommerce',
      'mobileapp','content','graphic','local','default'
    ];

    tabs.forEach((tab) => {
      tab.addEventListener('click', () => {
        const id = tab.getAttribute('data-featured-tab');
        const name = tab.getAttribute('data-title') || 'Hizmetler';
        const platformClass = tab.getAttribute('data-class') || 'default';

        tabs.forEach((item) => {
          const active = item === tab;
          item.classList.toggle('active', active);
          item.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        panes.forEach((pane) => {
          const active = pane.getAttribute('data-featured-pane') === id;
          pane.classList.toggle('active', active);
          pane.hidden = !active;
        });

        if (title) title.textContent = name;
        if (subtitle) {
          subtitle.textContent = name + ' kategorisindeki öne çıkarılan paketleri karşılaştırın.';
        }
        if (avatar) {
          classNames.forEach((cls) => avatar.classList.remove(cls));
          avatar.classList.add(platformClass);
          avatar.innerHTML = tab.querySelector('.icon') ? tab.querySelector('.icon').outerHTML : tab.innerHTML;
        }
      });
    });
  }

  const footer = document.querySelector('.site-footer');
  if (footer && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      document.body.classList.toggle('footer-in-view', entries.some((entry) => entry.isIntersecting));
    }, { threshold: 0.08 });
    observer.observe(footer);
  }
})();