(() => {
  'use strict';
  const host = document.querySelector('[data-landing-theme-host]');
  if (!host) return;
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
  const active = new Map();
  const prefersDesktopSideHeading = (type, variant, theme) =>
    (type === 'scripts' && theme === 'landing-01') ||
    (type === 'blog' && variant === 'editorial') ||
    (type === 'references' && theme === 'landing-01');

  function teardown(section) {
    const state = active.get(section);
    if (!state) return;
    state.cancelled = true;
    cancelAnimationFrame(state.raf);
    clearTimeout(state.resizeTimer);
    state.observer?.disconnect();
    state.resize?.disconnect();
    state.events.forEach(([node, type, cb, opts]) => node.removeEventListener(type, cb, opts));
    state.track.querySelectorAll('[data-nvl-rail-clone],[data-nvl-rail-spacer]').forEach(n => n.remove());
    state.viewport.removeAttribute('tabindex');
    state.viewport.removeAttribute('aria-label');
    state.viewport.style.scrollSnapType = '';
    state.section.classList.remove('is-nvl-rail','nvl-is-side');
    active.delete(section);
  }

  function setup(section, theme) {
    teardown(section);
    const type = section.dataset.component;
    if (!['scripts','blog','references'].includes(type)) return;
    const viewport = section.querySelector('[data-nvl-rail-viewport]');
    const track = section.querySelector('[data-nvl-grid]');
    if (!viewport || !track) return;
    const cards = [...track.children].filter(el => el.classList.contains('nvl-card') && !el.hidden);
    if (!cards.length) return;
    section.classList.add('is-nvl-rail');
    section.classList.toggle('nvl-is-side',prefersDesktopSideHeading(type, section.dataset.variant, theme));
    viewport.setAttribute('tabindex','0');
    viewport.setAttribute('aria-label','İki satırlı kayan kartlar. Fareyle sürükleyin, parmakla kaydırın veya ok tuşlarını kullanın.');
    viewport.style.scrollSnapType = 'none';
    const state = {
      section,viewport,track,cancelled:false,raf:0,events:[],cycle:0,last:0,
      dragging:false,dragged:false,startX:0,startScroll:0,pauseUntil:0,focus:false,
      visible:true,resizeTimer:0
    };
    active.set(section,state);
    const listen = (node,event,cb,opts) => {
      node.addEventListener(event,cb,opts);state.events.push([node,event,cb,opts]);
    };
    const makeClone = (original) => {
      const clone = original.cloneNode(true);
      clone.dataset.nvlRailClone = '1';
      clone.setAttribute('aria-hidden','true');
      clone.querySelectorAll('a,button,input,select,textarea,[tabindex]').forEach(el=>{
        el.setAttribute('tabindex','-1');
      });
      return clone;
    };
    function rebuild() {
      if (state.cancelled) return;
      const previousFraction = state.cycle ? (viewport.scrollLeft % state.cycle) / state.cycle : 0;
      track.querySelectorAll('[data-nvl-rail-clone],[data-nvl-rail-spacer]').forEach(el=>el.remove());
      const originals = [...track.children].filter(el=>el.classList.contains('nvl-card') && !el.hidden);
      state.cycle = 0;
      if(!originals.length)return;
      // Column-major two-row flow needs a final blank cell for odd counts.
      const appendCycle = (asClone) => {
        const nodes=originals.map(node=>asClone ? makeClone(node) : node);
        if(asClone)track.append(...nodes);
        if(originals.length % 2 === 1) {
          const blank=document.createElement('span');
          blank.className='nvl-rail-spacer';
          blank.dataset.nvlRailSpacer='1';
          blank.setAttribute('aria-hidden','true');
          track.append(blank);
        }
        return nodes[0];
      };
      appendCycle(false);
      const cloneFirst = appendCycle(true);
      // offsetLeft is relative to grid track's containing box for both cells.
      state.cycle = cloneFirst.offsetLeft - originals[0].offsetLeft;
      if (state.cycle <= 0) return;
      // Ensure at least a whole original cycle can slide before wrap-around.
      // Short lists receive extra inert cloned cycles; no duplicate SEO links.
      let guard=0;
      while (track.scrollWidth - viewport.clientWidth < state.cycle + 12 && guard++ < 24) {
        appendCycle(true);
      }
      viewport.scrollLeft = previousFraction * state.cycle;
    }
    const stopBriefly = (ms=1100) => {state.pauseUntil=performance.now()+ms;};
    listen(viewport,'pointerdown',event=>{
      if(event.pointerType==='touch' || event.button!==0)return; // native touch swipe stays enabled
      state.dragging=true;state.dragged=false;state.startX=event.clientX;
      state.startScroll=viewport.scrollLeft;state.pauseUntil=Infinity;
      // Capture only after an actual drag; ordinary product/blog clicks must work.
    });
    listen(viewport,'pointermove',event=>{
      if(!state.dragging)return;
      const shift=event.clientX-state.startX;
      if(Math.abs(shift)>4 && !state.dragged) {
        state.dragged=true;
        viewport.classList.add('is-dragging');
        viewport.setPointerCapture(event.pointerId);
      }
      if(state.dragged){
        event.preventDefault();
        viewport.scrollLeft=state.startScroll-shift;
        if(state.cycle>0){
          if(viewport.scrollLeft<0)viewport.scrollLeft+=state.cycle;
          if(viewport.scrollLeft>=state.cycle)viewport.scrollLeft-=state.cycle;
        }
      }
    });
    const dragEnd=()=>{
      if(!state.dragging)return;
      state.dragging=false;viewport.classList.remove('is-dragging');
      stopBriefly(1400);
      if(state.dragged)setTimeout(()=>{if(!state.cancelled)state.dragged=false;},150);
    };
    listen(viewport,'pointerup',dragEnd);
    listen(viewport,'pointercancel',dragEnd);
    listen(viewport,'lostpointercapture',dragEnd);
    listen(viewport,'click',event=>{
      if(!state.dragged)return;
      event.preventDefault();event.stopPropagation();state.dragged=false;
    },true);
    listen(viewport,'touchstart',()=>{state.pauseUntil=Infinity;},{passive:true});
    listen(viewport,'touchend',()=>stopBriefly(1600),{passive:true});
    listen(viewport,'touchcancel',()=>stopBriefly(500),{passive:true});
    listen(viewport,'focusin',()=>state.focus=true);
    listen(viewport,'focusout',event=>{if(!viewport.contains(event.relatedTarget)){state.focus=false;stopBriefly(700);}});
    listen(viewport,'wheel',()=>stopBriefly(1300),{passive:true});
    listen(viewport,'scroll',()=>{
      // Manual scrolling stays available. RAF resets only at the cycle boundary.
      if(!state.dragging&&state.cycle>0&&viewport.scrollLeft>=state.cycle)viewport.scrollLeft-=state.cycle;
    },{passive:true});
    const onReduce=()=>{if(!reduced.matches)rebuild();};
    listen(reduced,'change',onReduce);
    state.observer=new IntersectionObserver(entries=>{
      state.visible=entries[0]?.isIntersecting ?? false;
    },{threshold:0.01});
    state.observer.observe(viewport);
    state.resize=new ResizeObserver(()=>{
      clearTimeout(state.resizeTimer);
      state.resizeTimer=setTimeout(()=>!state.cancelled&&!state.dragging&&rebuild(),200);
    });
    state.resize.observe(viewport);
    rebuild();
    function tick(now) {
      if(state.cancelled)return;
      const dt=state.last ? Math.min(now-state.last,50) : 0;
      state.last=now;
      if(state.cycle>0 && !reduced.matches && !state.dragging &&
         !state.focus && now>state.pauseUntil && state.visible && !document.hidden){
        viewport.scrollLeft+=dt*0.021; // ~21 pixels per second, uninterrupted and calm.
        if(viewport.scrollLeft>=state.cycle)viewport.scrollLeft-=state.cycle;
      }
      state.raf=requestAnimationFrame(tick);
    }
    if(!reduced.matches)state.raf=requestAnimationFrame(tick);
  }

  function filterReferences(section,group){
    const grid=section.querySelector('[data-nvl-grid]');
    if(!grid)return;
    let shown=0;
    grid.querySelectorAll(':scope > .nvl-card:not([data-nvl-rail-clone])').forEach(card=>{
      const match=group==='all'||(card.dataset.nvlGroups||'').trim().split(/\s+/).includes(group);
      card.hidden=!match;if(match)shown++;
    });
    section.querySelectorAll('[data-nvl-reference-filter]').forEach(button=>{
      const yes=button.dataset.nvlReferenceFilter===group;
      button.classList.toggle('is-active',yes);button.setAttribute('aria-pressed',String(yes));
    });
    const count=section.querySelector('[data-nvl-reference-count]');
    if(count)count.textContent=shown+' çalışma';
    setup(section,host.querySelector('main.nv-landing')?.dataset.theme||'');
  }

  function update(){
    const main=host.querySelector('main.nv-landing');
    // Theme switching can replace all page elements, so stale timers get cleaned.
    for(const node of active.keys())if(!main?.contains(node))teardown(node);
    if(!main)return;
    main.querySelectorAll('section[data-component]').forEach(section=>{
      if(['scripts','blog','references'].includes(section.dataset.component))setup(section,main.dataset.theme);
    });
  }
  host.addEventListener('click',event=>{
    const button=event.target.closest('[data-nvl-reference-filter]');
    if(!button||!host.contains(button))return;
    const section=button.closest('[data-component="references"]');
    if(section)filterReferences(section,button.dataset.nvlReferenceFilter);
  });
  host.addEventListener('landing:themechange',()=>requestAnimationFrame(update));
  update();
})();
