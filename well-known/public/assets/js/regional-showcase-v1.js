/* Regional-only vitrines.
   1–4 items: normal compact cards, no animation.
   5+ items: two rows; auto-loop ONLY when a full column is offscreen.
   An active loop MOVES original columns: NO card cloning and NO ping-pong. */
(() => {
  'use strict';
  const host = document.querySelector('[data-landing-theme-host]');
  if (!host) return;
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const instances = new Map();
  const SPEED = 25; // pixels per second
  const GAP = 14;

  function isSideHeading(section, theme) {
    return (section.dataset.component === 'scripts' && theme === 'landing-01')
      || (section.dataset.component === 'blog' && section.dataset.variant === 'editorial')
      || (section.dataset.component === 'references' && theme === 'landing-01');
  }
  function sourceCards(grid) {
    return [...grid.querySelectorAll('.nvl-card')];
  }
  function destroy(section) {
    const state = instances.get(section);
    if (!state) return;
    state.destroyed = true;
    cancelAnimationFrame(state.frame);
    clearTimeout(state.resizeTimer);
    state.resize?.disconnect();
    state.observer?.disconnect();
    state.listeners.forEach(([node, type, handler, options]) =>
      node.removeEventListener(type, handler, options));
    if (state.pauseButton) state.pauseButton.remove();
    // Restore exact original document order, including hidden filtered cards.
    state.grid.replaceChildren(...state.original);
    state.grid.style.removeProperty('--nvl-rail-card-width');
    state.viewport.removeAttribute('aria-label');
    state.viewport.removeAttribute('tabindex');
    state.viewport.scrollLeft = 0;
    section.classList.remove('is-nvl-rail','is-nvl-static','is-nvl-loop','nvl-is-side');
    instances.delete(section);
  }
  function init(section, theme) {
    destroy(section);
    const type=section.dataset.component;
    if (!['scripts','blog','references'].includes(type)) return;
    const viewport=section.querySelector('[data-nvl-rail-viewport]');
    const grid=section.querySelector('[data-nvl-grid]');
    if (!viewport||!grid) return;
    grid.querySelectorAll('[data-nvl-rail-clone],[data-nvl-rail-spacer]').forEach(n=>n.remove());
    const original=sourceCards(grid);
    const shown=original.filter(n=>!n.hidden);
    if (!shown.length) return;
    const state={
      section,viewport,grid,original,shown,destroyed:false,frame:0,
      listeners:[],resizeTimer:0,resize:null,observer:null,visible:true,
      manualPause:false,hover:false,focus:false,dragging:false,
      dragged:false,suppressClick:false,startX:0,startScroll:0,
      pointerId:null,pauseUntil:performance.now()+450,last:0,
      loop:false,step:0,button:null,pixelRemainder:0
    };
    instances.set(section,state);
    const on=(element,event,fn,options)=>{
      element.addEventListener(event,fn,options);
      state.listeners.push([element,event,fn,options]);
    };
    section.classList.toggle('nvl-is-side',isSideHeading(section,theme));
    const compact=shown.length<=4;
    section.classList.add(compact?'is-nvl-static':'is-nvl-rail');
    viewport.tabIndex=0;
    viewport.setAttribute('aria-label','Kartları yatay kaydırın. Masaüstünde fareyle sürükleyebilir, telefonda parmağınızla gezebilirsiniz.');

    function resetLayout() {
      if(state.destroyed || state.dragging) return;
      state.grid.replaceChildren(...state.original);
      state.grid.style.removeProperty('--nvl-rail-card-width');
      state.section.classList.remove('is-nvl-loop');
      state.loop=false;
      state.step=0;
      state.pixelRemainder=0;
      state.viewport.scrollLeft=0;
      const visible=state.original.filter(n=>!n.hidden);
      if(state.pauseButton){state.pauseButton.remove();state.pauseButton=null;}
      if(visible.length<=4) return;
      // Width comes from CSS clamp, never from viewport/card count.
      const measured=state.grid.scrollWidth-state.viewport.clientWidth;
      const colWidth=visible[0].getBoundingClientRect().width;
      state.step=colWidth+(parseFloat(getComputedStyle(state.grid).columnGap)||GAP);
      if(reduceMotion.matches || measured<state.step-3 || visible.length<6) return;
      // Two original cards per column; moving whole columns after leaving view
      // preserves the visual sequence without ever repeating records.
      const columns=[];
      for(let i=0;i<visible.length;i+=2){
        const column=document.createElement('div');
        column.className='nvl-rail-column';
        column.append(...visible.slice(i,i+2));
        columns.push(column);
      }
      // Hidden filtered records remain in state.original but not in the moving track.
      state.grid.replaceChildren(...columns);
      state.section.classList.add('is-nvl-loop');
      state.step=columns[0].getBoundingClientRect().width+(parseFloat(getComputedStyle(state.grid).columnGap)||GAP);
      state.loop=true;
      if(!state.manualPause){
        const button=document.createElement('button');
        button.type='button';
        button.className='nvl-rail-pause';
        button.textContent='Durdur';
        button.setAttribute('aria-label','Otomatik kart akışını durdur');
        button.setAttribute('aria-pressed','false');
        state.pauseButton=button;
        viewport.insertAdjacentElement('afterend',button);
        on(button,'click',()=>{
          state.manualPause=!state.manualPause;
          button.textContent=state.manualPause?'Oynat':'Durdur';
          button.setAttribute('aria-label',state.manualPause?'Otomatik kart akışını başlat':'Otomatik kart akışını durdur');
          button.setAttribute('aria-pressed',String(state.manualPause));
          state.pauseUntil=performance.now()+500;
        });
      }
    }

    on(viewport,'mouseenter',()=>state.hover=true);
    on(viewport,'mouseleave',()=>state.hover=false);
    on(viewport,'focusin',()=>{state.focus=true;state.manualPause=true;if(state.pauseButton){state.pauseButton.textContent='Oynat';state.pauseButton.setAttribute('aria-pressed','true');}});
    on(viewport,'focusout',event=>{if(!viewport.contains(event.relatedTarget))state.focus=false;});
    on(viewport,'touchstart',()=>state.pauseUntil=Infinity,{passive:true});
    on(viewport,'touchend',()=>state.pauseUntil=performance.now()+1600,{passive:true});
    on(viewport,'touchcancel',()=>state.pauseUntil=performance.now()+500,{passive:true});
    on(viewport,'wheel',()=>state.pauseUntil=performance.now()+1300,{passive:true});
    on(viewport,'pointerdown',event=>{
      if(event.pointerType==='touch'||event.button!==0||event.target.closest('button'))return;
      state.dragging=true;state.dragged=false;state.startX=event.clientX;
      state.startScroll=viewport.scrollLeft;state.pointerId=event.pointerId;
      state.pauseUntil=Infinity;
    });
    on(viewport,'pointermove',event=>{
      if(!state.dragging||event.pointerId!==state.pointerId)return;
      const dx=event.clientX-state.startX;
      if(Math.abs(dx)>5&&!state.dragged){
        state.dragged=true;
        viewport.classList.add('is-dragging');
        try{viewport.setPointerCapture(event.pointerId);}catch(_){}
      }
      if(state.dragged){
        event.preventDefault();
        viewport.scrollLeft=Math.max(0,state.startScroll-dx);
      }
    });
    const endDrag=event=>{
      if(!state.dragging||event.pointerId!==state.pointerId)return;
      state.dragging=false;state.pointerId=null;
      viewport.classList.remove('is-dragging');
      state.pauseUntil=performance.now()+1500;
      if(state.dragged){
        state.suppressClick=true;
        setTimeout(()=>{if(!state.destroyed)state.suppressClick=false;},180);
      }
    };
    on(viewport,'pointerup',endDrag);
    on(viewport,'pointercancel',endDrag);
    on(viewport,'lostpointercapture',endDrag);
    on(viewport,'click',event=>{
      if(!state.suppressClick)return;
      event.preventDefault();event.stopImmediatePropagation();
      state.suppressClick=false;
    },true);
    on(reduceMotion,'change',()=>{state.manualPause=reduceMotion.matches;resetLayout();});
    state.observer=new IntersectionObserver(entries=>{
      state.visible=!!entries[0]?.isIntersecting;state.last=0;
    },{threshold:0.01});
    state.observer.observe(viewport);
    state.resize=new ResizeObserver(()=>{
      clearTimeout(state.resizeTimer);
      state.resizeTimer=setTimeout(()=>{resetLayout();state.last=0;},250);
    });
    state.resize.observe(viewport);
    resetLayout();

    function tick(time){
      if(state.destroyed)return;
      const elapsed=state.last?Math.min(70,time-state.last):0;
      state.last=time;
      if(window.__nvRegionalTestMode){
        section.dataset.nvlDebug=JSON.stringify({
          loop:state.loop,reduce:reduceMotion.matches,manual:state.manualPause,
          hover:state.hover,focus:state.focus,drag:state.dragging,
          visible:state.visible,hidden:document.hidden,
          pauseRemaining:Math.round(state.pauseUntil-time),
          step:state.step,left:Math.round(viewport.scrollLeft),elapsed:Math.round(elapsed)
        });
      }
      if(state.loop && !reduceMotion.matches && !state.manualPause &&
         !state.hover && !state.focus && !state.dragging &&
         state.visible && !document.hidden && time>state.pauseUntil){
        // Chromium's scrollLeft may quantize subpixel assignments to integers.
        // Accumulate fractional frames so slow motion does not round to zero forever.
        state.pixelRemainder+=elapsed*SPEED/1000;
        const wholePixels=Math.floor(state.pixelRemainder);
        if(wholePixels>0){
          state.pixelRemainder-=wholePixels;
          viewport.scrollLeft+=wholePixels;
        }
        // Entire leading column has left the screen; rotate ORIGINAL nodes.
        if(viewport.scrollLeft>=state.step){
          const front=grid.firstElementChild;
          if(front){
            grid.appendChild(front);
            viewport.scrollLeft-=state.step;
          }
        }
      }
      state.frame=requestAnimationFrame(tick);
    }
    state.frame=requestAnimationFrame(tick);
  }
  function filter(section,value){
    // First restore original card elements from an existing loop.
    destroy(section);
    const grid=section.querySelector('[data-nvl-grid]');
    if(!grid)return;
    let count=0;
    grid.querySelectorAll(':scope > .nvl-card').forEach(card=>{
      const included=value==='all'||(card.dataset.nvlGroups||'').trim().split(/\s+/).includes(value);
      card.hidden=!included;if(included)count++;
    });
    section.querySelectorAll('[data-nvl-reference-filter]').forEach(button=>{
      const selected=button.dataset.nvlReferenceFilter===value;
      button.classList.toggle('is-active',selected);
      button.setAttribute('aria-pressed',String(selected));
    });
    const label=section.querySelector('[data-nvl-reference-count]');
    if(label)label.textContent=count+' çalışma';
    init(section,host.querySelector('main.nv-landing')?.dataset.theme||'');
  }
  function refresh(){
    const main=host.querySelector('main.nv-landing');
    for(const node of instances.keys())if(!main?.contains(node))destroy(node);
    if(!main)return;
    main.querySelectorAll('section[data-component]').forEach(section=>{
      if(['scripts','blog','references'].includes(section.dataset.component)){
        init(section,main.dataset.theme);
      }
    });
  }
  host.addEventListener('click',event=>{
    const button=event.target.closest('[data-nvl-reference-filter]');
    if(!button||!host.contains(button))return;
    const section=button.closest('section[data-component="references"]');
    if(section)filter(section,button.dataset.nvlReferenceFilter);
  });
  host.addEventListener('landing:themechange',()=>requestAnimationFrame(refresh));
  refresh();
})();