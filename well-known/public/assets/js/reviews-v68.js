(() => {
  'use strict';

  const viewport = document.querySelector('[data-review-viewport]');
  const track = viewport?.querySelector('[data-review-track]');
  if (!viewport || !track) return;

  const originalCards = Array.from(track.querySelectorAll(':scope > .nv30-review-card'));
  const total = originalCards.length;
  if (total < 2) return;

  // Add the next three cards only for the invisible end of the track.
  // This lets the final slide roll back to the first one without jumping.
  for (let i = 0; i < 3; i += 1) {
    const clone = originalCards[i % total].cloneNode(true);
    clone.classList.remove('nv30-review-primary');
    clone.setAttribute('aria-hidden', 'true');
    clone.dataset.reviewClone = 'true';
    track.appendChild(clone);
  }

  const cards = Array.from(track.children);
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  let index = 0;
  let sliding = false;
  let inView = true;
  let pause = false;

  const distance = () => {
    const first = cards[0];
    const gap = Number.parseFloat(getComputedStyle(track).columnGap) || 0;
    return first.getBoundingClientRect().width + gap;
  };

  const highlightVisibleFirst = () => {
    for (const card of cards) card.classList.remove('nv30-review-primary');
    cards[index].classList.add('nv30-review-primary');
  };

  const align = () => {
    track.classList.remove('nv69-is-sliding');
    track.style.transform = 'translate3d(-' + (index * distance()) + 'px,0,0)';
  };

  const finish = () => {
    if (!sliding) return;
    sliding = false;
    if (index >= total) {
      index = 0;
      // The clones at the far end are copies of the first cards, so resetting
      // the track is invisible to the visitor.
      align();
    } else {
      track.classList.remove('nv69-is-sliding');
    }
    highlightVisibleFirst();
  };

  track.addEventListener('transitionend', (event) => {
    if (event.target === track && event.propertyName === 'transform') finish();
  });

  const advance = () => {
    if (sliding || pause || !inView || document.hidden || reducedMotion.matches) return;
    sliding = true;
    index += 1;
    track.classList.add('nv69-is-sliding');
    requestAnimationFrame(() => {
      track.style.transform = 'translate3d(-' + (index * distance()) + 'px,0,0)';
    });
    // In case a browser cancels the CSS transition, don't freeze the carousel.
    window.setTimeout(finish, 950);
  };

  viewport.addEventListener('pointerenter', () => { pause = true; });
  viewport.addEventListener('pointerleave', () => { pause = false; });
  viewport.addEventListener('focusin', () => { pause = true; });
  viewport.addEventListener('focusout', (event) => {
    if (!viewport.contains(event.relatedTarget)) pause = false;
  });

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(([entry]) => {
      inView = Boolean(entry?.isIntersecting);
    }, { threshold: 0.12 });
    observer.observe(viewport);
  }

  window.addEventListener('resize', () => {
    if (!sliding) align();
  }, { passive: true });

  highlightVisibleFirst();
  window.setInterval(advance, 5200);
})();
