(() => {
  'use strict';

  const switcher = document.querySelector('[data-featured-switcher]');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  if (switcher) {
    const tabs = [...switcher.querySelectorAll('[data-featured-tab]')];
    const groupButtons = [...switcher.querySelectorAll('[data-featured-group]')];
    const filterPanels = [...switcher.querySelectorAll('[data-featured-filter-panel]')];
    const panes = [...switcher.querySelectorAll('[data-featured-pane]')];
    const title = switcher.querySelector('[data-featured-title]');
    const subtitle = switcher.querySelector('[data-featured-subtitle]');
    const avatar = switcher.querySelector('[data-featured-avatar]');
    const platformClasses = [
      'instagram','tiktok','youtube','facebook','twitter','threads','telegram',
      'spotify','discord','linkedin','twitch','google','web','seo','ecommerce',
      'mobileapp','content','graphic','local','default'
    ];

    let visible = false;
    let paused = false;
    let rotation;

    const activePane = () => panes.find(pane => !pane.hidden);
    const step = track => {
      const card = track.querySelector('.yh18-featured-card');
      if (!card) return track.clientWidth;
      const gap = parseFloat(getComputedStyle(track).columnGap) || 0;
      return card.getBoundingClientRect().width + gap;
    };

    const updateControls = (pane) => {
      const track = pane.querySelector('[data-featured-track]');
      const prev = pane.querySelector('[data-slide-prev]');
      const next = pane.querySelector('[data-slide-next]');
      const status = pane.querySelector('[data-slide-count]');
      const controls = pane.querySelector('.yh18-carousel-controls');
      if (!track || !controls) return;

      const cards = track.querySelectorAll('.yh18-featured-card');
      const movable = cards.length > 1 && track.scrollWidth > track.clientWidth + 3;
      controls.hidden = !movable;
      if (!movable) return;

      const current = Math.min(cards.length, 1 + Math.round(track.scrollLeft / Math.max(step(track), 1)));
      if (status) status.textContent = current + ' / ' + cards.length;
      if (prev) prev.setAttribute('aria-label', 'Önceki paketler');
      if (next) next.setAttribute('aria-label', 'Sonraki paketler');
    };

    const scrollTrack = (pane, direction, userInitiated = false) => {
      const track = pane?.querySelector('[data-featured-track]');
      if (!track) return;
      const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
      if (maxScroll < 3) return;
      const distance = step(track);
      let target = track.scrollLeft + direction * distance;
      if (direction > 0 && track.scrollLeft >= maxScroll - 3) target = 0;
      if (direction < 0 && track.scrollLeft < 3) target = maxScroll;
      track.scrollTo({
        left: Math.max(0, Math.min(maxScroll, target)),
        behavior: reducedMotion.matches ? 'auto' : 'smooth'
      });
      if (userInitiated) pauseTemporarily();
    };

    let resume;
    const pauseTemporarily = () => {
      paused = true;
      clearTimeout(resume);
      resume = setTimeout(() => { paused = false; }, 12000);
    };

    const startRotation = () => {
      clearInterval(rotation);
      if (reducedMotion.matches) return;
      rotation = setInterval(() => {
        const pane = activePane();
        if (!pane || !visible || paused || document.hidden || pane.matches(':hover') ||
            pane.contains(document.activeElement) || switcher.matches(':hover')) return;
        scrollTrack(pane, 1);
      }, 6500);
    };

    panes.forEach(pane => {
      const track = pane.querySelector('[data-featured-track]');
      if (!track) return;
      pane.querySelector('[data-slide-prev]')?.addEventListener('click', () => scrollTrack(pane, -1, true));
      pane.querySelector('[data-slide-next]')?.addEventListener('click', () => scrollTrack(pane, 1, true));
      track.addEventListener('scroll', () => updateControls(pane), {passive:true});
      track.addEventListener('pointerdown', pauseTemporarily, {passive:true});
      track.addEventListener('wheel', pauseTemporarily, {passive:true});
    });

    // Hover changes only the visible filters; selecting a chip changes the featured cards.
    // Previewing a group also selects its first REAL featured category, so the
    // filter heading can never say Social Media while showing Google packages.
    const showGroup = (key, activateFirst = true) => {
      const panel = filterPanels.find(item => item.dataset.featuredFilterPanel === key);
      if (!panel) return;
      groupButtons.forEach(button => {
        const open = button.dataset.featuredGroup === key;
        button.classList.toggle('is-open', open);
        button.setAttribute('aria-expanded', String(open));
      });
      filterPanels.forEach(item => { item.hidden = item !== panel; });
      if (activateFirst) {
        const first = panel.querySelector('[data-featured-tab]');
        if (first && !first.classList.contains('active')) switchTo(first);
      }
    };
    groupButtons.forEach(button => {
      const key = button.dataset.featuredGroup;
      button.addEventListener('pointerenter', event => {
        if (event.pointerType === 'mouse' || event.pointerType === 'pen') showGroup(key);
      });
      button.addEventListener('focus', () => showGroup(key));
      button.addEventListener('click', () => { showGroup(key); pauseTemporarily(); });
      button.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
          const selected = tabs.find(tab => tab.classList.contains('active'));
          if (selected) showGroup(selected.dataset.featuredParentGroup, false);
          button.blur();
        }
      });
    });

    const switchTo = tab => {
      const id = tab.dataset.featuredTab;
      const name = tab.dataset.title || 'Hizmetler';
      const platformClass = tab.dataset.class || 'default';
      showGroup(tab.dataset.featuredParentGroup, false);

      tabs.forEach(item => {
        const active = item === tab;
        item.classList.toggle('active', active);
        item.setAttribute('aria-selected', active ? 'true' : 'false');
        item.tabIndex = active ? 0 : -1;
      });
      panes.forEach(pane => {
        const active = pane.dataset.featuredPane === id;
        pane.classList.toggle('active', active);
        pane.hidden = !active;
        if (active) requestAnimationFrame(() => updateControls(pane));
      });
      if (title) title.textContent = name;
      if (subtitle) subtitle.textContent = name + ' kategorisindeki öne çıkan paketleri inceleyin.';
      if (avatar) {
        platformClasses.forEach(cls => avatar.classList.remove(cls));
        avatar.classList.add(platformClass);
        const platformIcon = tab.querySelector('.icon, svg, i');
        if (platformIcon) avatar.replaceChildren(platformIcon.cloneNode(true));
      }
      pauseTemporarily();
    };

    tabs.forEach((tab, index) => {
      tab.setAttribute('role', 'tab');
      tab.tabIndex = index === 0 ? 0 : -1;
      tab.addEventListener('click', () => switchTo(tab));
      tab.addEventListener('keydown', e => {
        if (!['ArrowRight', 'ArrowLeft', 'Home', 'End'].includes(e.key)) return;
        e.preventDefault();
        const groupTabs = tabs.filter(item => item.dataset.featuredParentGroup === tab.dataset.featuredParentGroup);
        const current = groupTabs.indexOf(tab);
        const next = e.key === 'Home' ? 0 : e.key === 'End' ? groupTabs.length - 1
          : (current + (e.key === 'ArrowRight' ? 1 : -1) + groupTabs.length) % groupTabs.length;
        groupTabs[next].focus();
        switchTo(groupTabs[next]);
      });
    });

    if ('IntersectionObserver' in window) {
      const observer = new IntersectionObserver(entries => {
        visible = entries.some(entry => entry.isIntersecting);
      }, { threshold: 0.15 });
      observer.observe(switcher);
    } else visible = true;

    const resize = () => {
      panes.forEach(pane => updateControls(pane));
    };
    window.addEventListener('resize', resize, {passive:true});
    reducedMotion.addEventListener?.('change', startRotation);
    switcher.addEventListener('focusin', pauseTemporarily);
    requestAnimationFrame(resize);
    startRotation();
  }

  // Keep existing fixed contact bubbles away from the footer.
  const footer = document.querySelector('.site-footer');
  if (footer && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver(entries => {
      document.body.classList.toggle('footer-in-view', entries.some(entry => entry.isIntersecting));
    }, { threshold: 0.08 });
    observer.observe(footer);
  }
})();
