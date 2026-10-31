/* NetVera regional landing vitrines (10 themes).
 * Fully reachable, snap-aligned ORIGINAL cards. No ghost clones, recirculating
 * columns or permanently half-visible final cards. Filters preserve all nodes.
 * 1-4 cards: wrap compactly, no carousel. 5+: 2-row horizontal, manual drag,
 * touch swipe, keyboard and previous/next arrows; no accidental link clicks.
 */
(() => {
  'use strict';
  const host = document.querySelector('[data-landing-theme-host]');
  if (!host) return;
  const instances = new Map();
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

  function isSide(section, theme) {
    return (section.dataset.component === 'scripts' && theme === 'landing-01')
      || (section.dataset.component === 'blog' && section.dataset.variant === 'editorial')
      || (section.dataset.component === 'references' && theme === 'landing-01');
  }
  function destroy(section) {
    const state = instances.get(section);
    if (!state) return;
    state.destroyed = true;
    state.resize?.disconnect();
    if (state.frame) cancelAnimationFrame(state.frame);
    for (const [node, event, callback, opts] of state.listeners) {
      node.removeEventListener(event, callback, opts);
    }
    state.controls?.remove();
    state.grid.replaceChildren(...state.original);
    state.viewport.removeAttribute('aria-label');
    state.viewport.removeAttribute('tabindex');
    state.viewport.scrollLeft = 0;
    state.viewport.classList.remove('is-dragging');
    section.classList.remove('is-nvl-rail','is-nvl-static','is-nvl-loop','nvl-is-side');
    instances.delete(section);
  }
  function init(section, theme) {
    destroy(section);
    if (!['scripts','blog','references'].includes(section.dataset.component)) return;
    const viewport = section.querySelector('[data-nvl-rail-viewport]');
    const grid = section.querySelector('[data-nvl-grid]');
    if (!viewport || !grid) return;
    const original = [...grid.querySelectorAll(':scope > .nvl-card')];
    const visible = original.filter(card => !card.hidden);
    if (!visible.length) return;

    const rail = visible.length > 4;
    section.classList.toggle('nvl-is-side', isSide(section, theme));
    section.classList.add(rail ? 'is-nvl-rail' : 'is-nvl-static');
    const state = {
      viewport, grid, original, section, listeners: [], resize: null, controls: null,
      frame: 0, destroyed: false, dragging: false, pointerId: null,
      startX: 0, startScroll: 0, moved: false, suppressClick: false,
      prev: null, next: null
    };
    instances.set(section, state);
    const listen = (node, event, handler, opts) => {
      node.addEventListener(event, handler, opts);
      state.listeners.push([node, event, handler, opts]);
    };
    if (!rail) return;
    viewport.tabIndex = 0;
    viewport.setAttribute('aria-label', 'Kartları yatay kaydırın; ok tuşları, kaydırma veya ok düğmeleriyle tüm kartlara ulaşabilirsiniz.');

    const controls = document.createElement('nav');
    controls.className = 'nvl-rail-controls';
    controls.setAttribute('aria-label', 'Kart gezinme');
    for (const [kind, symbol, label] of [
      ['prev', '←', 'Önceki kartlar'],
      ['next', '→', 'Sonraki kartlar']
    ]) {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'nvl-rail-' + kind;
      button.textContent = symbol;
      button.setAttribute('aria-label', label);
      controls.appendChild(button);
      state[kind] = button;
    }
    viewport.insertAdjacentElement('afterend', controls);
    state.controls = controls;

    const maxScroll = () => Math.max(0, viewport.scrollWidth - viewport.clientWidth);
    const step = () => {
      const first = grid.querySelector('.nvl-card:not([hidden])');
      const width = first?.getBoundingClientRect().width || 260;
      return width + (parseFloat(getComputedStyle(grid).columnGap) || 14);
    };
    const move = direction => {
      const left = Math.min(maxScroll(), Math.max(0,
        Math.round((viewport.scrollLeft + direction * step()) / step()) * step()
      ));
      viewport.scrollTo({left, behavior: reduceMotion.matches ? 'instant' : 'smooth'});
    };
    let pending = 0;
    const update = () => {
      pending = 0;
      if (state.destroyed) return;
      const max = maxScroll();
      controls.hidden = max <= 3;
      state.prev.disabled = max <= 3 || viewport.scrollLeft <= 2;
      state.next.disabled = max <= 3 || viewport.scrollLeft >= max - 2;
    };
    const queueUpdate = () => {
      if (!pending) pending = requestAnimationFrame(update);
    };
    listen(state.prev, 'click', () => move(-1));
    listen(state.next, 'click', () => move(1));
    listen(viewport, 'scroll', queueUpdate, {passive: true});
    listen(viewport, 'keydown', event => {
      if (event.target !== viewport || !['ArrowLeft','ArrowRight'].includes(event.key)) return;
      event.preventDefault();
      move(event.key === 'ArrowRight' ? 1 : -1);
    });
    listen(viewport, 'pointerdown', event => {
      if (event.pointerType === 'touch' || event.button !== 0 || event.target.closest('button')) return;
      state.dragging = true; state.moved = false; state.pointerId = event.pointerId;
      state.startX = event.clientX; state.startScroll = viewport.scrollLeft;
    });
    listen(viewport, 'pointermove', event => {
      if (!state.dragging || state.pointerId !== event.pointerId) return;
      const dx = event.clientX - state.startX;
      if (Math.abs(dx) > 5 && !state.moved) {
        state.moved = true;
        viewport.classList.add('is-dragging');
        try { viewport.setPointerCapture(event.pointerId); } catch (_) {}
      }
      if (state.moved) {
        event.preventDefault();
        viewport.scrollLeft = Math.max(0, Math.min(maxScroll(), state.startScroll - dx));
      }
    });
    const endDrag = event => {
      if (!state.dragging || event.pointerId !== state.pointerId) return;
      state.dragging = false; state.pointerId = null;
      viewport.classList.remove('is-dragging');
      if (state.moved) {
        state.suppressClick = true;
        setTimeout(() => { if (!state.destroyed) state.suppressClick = false; }, 220);
        // Finish at a complete card boundary, never retain a partially dragged step.
        const left = Math.min(maxScroll(), Math.max(0,
          Math.round(viewport.scrollLeft / step()) * step()
        ));
        viewport.scrollTo({left, behavior: reduceMotion.matches ? 'instant' : 'smooth'});
      }
      queueUpdate();
    };
    for (const event of ['pointerup', 'pointercancel', 'lostpointercapture']) {
      listen(viewport, event, endDrag);
    }
    listen(viewport, 'click', event => {
      if (!state.suppressClick) return;
      event.preventDefault(); event.stopImmediatePropagation();
      state.suppressClick = false;
    }, true);
    state.resize = new ResizeObserver(queueUpdate);
    state.resize.observe(viewport);
    state.resize.observe(grid);
    queueUpdate();
  }

  function filter(section, group) {
    destroy(section);
    const grid = section.querySelector('[data-nvl-grid]');
    if (!grid) return;
    let visibleCount = 0;
    grid.querySelectorAll(':scope > .nvl-card').forEach(card => {
      const groups = (card.dataset.nvlGroups || '').trim().split(/\s+/);
      card.hidden = group !== 'all' && !groups.includes(group);
      if (!card.hidden) visibleCount++;
    });
    section.querySelectorAll('[data-nvl-reference-filter]').forEach(button => {
      const active = button.dataset.nvlReferenceFilter === group;
      button.classList.toggle('is-active', active);
      button.setAttribute('aria-pressed', String(active));
    });
    const counter = section.querySelector('[data-nvl-reference-count]');
    if (counter) counter.textContent = visibleCount + ' çalışma';
    init(section, host.querySelector('main.nv-landing')?.dataset.theme || '');
  }

  function refresh() {
    const main = host.querySelector('main.nv-landing');
    for (const node of instances.keys()) if (!main?.contains(node)) destroy(node);
    if (!main) return;
    main.querySelectorAll('section[data-component]').forEach(section => {
      if (['scripts','blog','references'].includes(section.dataset.component)) {
        init(section, main.dataset.theme);
      }
    });
  }
  host.addEventListener('click', event => {
    const button = event.target.closest('[data-nvl-reference-filter]');
    if (!button || !host.contains(button)) return;
    const section = button.closest('section[data-component="references"]');
    if (section) filter(section, button.dataset.nvlReferenceFilter);
  });
  host.addEventListener('landing:themechange', () => requestAnimationFrame(refresh));
  refresh();
})();
