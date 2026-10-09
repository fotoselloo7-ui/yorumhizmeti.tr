/* NetVera V56: manual grab/drag of the EXISTING keyframe carousels.
 * Move the CSS animation clock rather than overriding transform. This keeps
 * endless-loop timing and resumes automatically at the exact release position.
 */
(() => {
  'use strict';
  const strip = document.querySelector('.yh49-category-marquee');
  if (!strip) return;

  const isReduced = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const rows = [...strip.querySelectorAll('.yh49-marquee-row')];
  rows.forEach(row => {
    const track = row.querySelector('.yh49-marquee-track');
    const set = track?.querySelector('.yh49-marquee-set');
    if (!track || !set) return;
    const isRight = row.classList.contains('yh49-marquee-secondary');
    let gesture = null;
    let suppressClick = false;

    const currentAnimation = () => track.getAnimations().find(a =>
      a instanceof CSSAnimation || a.effect?.getKeyframes()?.some(k => k.transform)) ||
      track.getAnimations()[0];

    row.addEventListener('dragstart', event => event.preventDefault());
    row.querySelectorAll('a,img').forEach(anchor => { anchor.draggable = false; });

    row.addEventListener('pointerdown', event => {
      if (event.pointerType === 'mouse' && event.button !== 0) return;
      if (isReduced()) return;
      const animation = currentAnimation();
      const width = set.getBoundingClientRect().width;
      if (!animation || !width) return;
      const timing = animation.effect?.getComputedTiming();
      const duration = timing?.duration || 0;
      if (!(duration > 0 && Number.isFinite(duration))) return;
      animation.pause();
      gesture = {
        pointerId: event.pointerId,
        startX: event.clientX,
        startY: event.clientY,
        startTime: Number(animation.currentTime) || 0,
        animation, duration, width,
        moved: false
      };
      // Do not capture taps: a plain click must still follow the category link.
    }, {passive:true});

    row.addEventListener('pointermove', event => {
      if (!gesture || gesture.pointerId !== event.pointerId) return;
      const dx = event.clientX - gesture.startX;
      const dy = event.clientY - gesture.startY;
      if (!gesture.moved && Math.abs(dx) < 5) return;
      if (!gesture.moved && Math.abs(dy) > Math.abs(dx) * 1.4) {
        gesture.animation.play();
        gesture = null;
        return;
      }
      if(!gesture.moved){try{row.setPointerCapture?.(event.pointerId)}catch(_){}}
      gesture.moved = true;
      row.classList.add('yh56-is-dragging');
      // Left ribbon: mouse moves left => animation progresses forwards.
      // Right ribbon reverses the movement direction.
      const direction = isRight ? 1 : -1;
      const raw = gesture.startTime + direction * dx / gesture.width * gesture.duration;
      gesture.animation.currentTime = ((raw % gesture.duration) + gesture.duration) % gesture.duration;
    }, {passive:true});

    function finish(event) {
      if (!gesture || (event && event.pointerId !== gesture.pointerId)) return;
      const {animation,moved} = gesture;
      gesture = null;
      row.classList.remove('yh56-is-dragging');
      if (moved) {
        if(event && row.hasPointerCapture?.(event.pointerId)){
          try{row.releasePointerCapture(event.pointerId)}catch(_){}
        }
        suppressClick = true;
        window.setTimeout(() => { suppressClick = false; }, 100);
      }
      try { animation.play(); } catch (_) {}
    }
    row.addEventListener('pointerup', finish);
    row.addEventListener('pointercancel', finish);
    row.addEventListener('lostpointercapture', () => finish());
    row.addEventListener('click', event => {
      if (!suppressClick) return;
      event.preventDefault();
      event.stopImmediatePropagation();
    }, true);
  });
})();
