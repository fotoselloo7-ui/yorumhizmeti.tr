/* Regional landing vitrines: ONLY original server-rendered cards, no clones or gaps.
   Slow ping-pong drift, direct manipulation on desktop/touch, and theme-local state. */
(() => {
  'use strict';
  const host = document.querySelector('[data-landing-theme-host]');
  if (!host) return;
  const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const instances = new Map();
  const SCROLL_PX_PER_SECOND = 39;
  const now = () => performance.now();
  function isSideHeading(section, theme) {
    return (section.dataset.component === 'scripts' && theme === 'landing-01') ||
      (section.dataset.component === 'blog' && section.dataset.variant === 'editorial') ||
      (section.dataset.component === 'references' && theme === 'landing-01');
  }
  function teardown(section) {
    const s = instances.get(section);
    if (!s) return;
    s.destroyed = true;
    cancelAnimationFrame(s.raf);
    clearTimeout(s.resizeTimer);
    s.visibility?.disconnect();
    s.resize?.disconnect();
    for (const [element, type, cb, options] of s.listeners) {
      element.removeEventListener(type, cb, options);
    }
    s.viewport.style.removeProperty('scrollSnapType');
    s.grid.style.removeProperty('--nvl-rail-column-width');
    s.viewport.classList.remove('is-dragging');
    s.viewport.removeAttribute('tabindex');
    s.viewport.removeAttribute('aria-label');
    section.classList.remove('is-nvl-rail', 'nvl-is-side');
    instances.delete(section);
  }
  function init(section, theme) {
    teardown(section);
    if (!['references', 'scripts', 'blog'].includes(section.dataset.component)) return;
    const viewport = section.querySelector('[data-nvl-rail-viewport]');
    const grid = section.querySelector('[data-nvl-grid]');
    if (!viewport || !grid) return;
    // Remove the obsolete duplicate cards if an older script left them behind.
    grid.querySelectorAll('[data-nvl-rail-clone],[data-nvl-rail-spacer]').forEach(el => el.remove());
    const cards = [...grid.children].filter(el => el.classList.contains('nvl-card') && !el.hidden);
    if (!cards.length) return;
    section.classList.add('is-nvl-rail');
    section.classList.toggle('nvl-is-side', isSideHeading(section, theme));
    viewport.tabIndex = 0;
    viewport.setAttribute('aria-label', 'Kayan kartlar. Masaüstünde fareyle sürükleyin veya ok tuşlarını kullanın; telefonda parmakla kaydırın.');
    viewport.style.scrollSnapType = 'none';
    const s = {
      viewport, grid, section, destroyed:false, listeners:[], raf:0,
      resizeTimer:0, dragging:false, moved:false, pointer:null,
      startX:0, startScroll:0, lastTime:0, dir:1,
      holdUntil:now()+1200, focused:false, visible:true, max:0
    };
    instances.set(section,s);
    const listen=(element,type,cb,options)=>{
      element.addEventListener(type,cb,options);
      s.listeners.push([element,type,cb,options]);
    };
    function measure() {
      if (s.destroyed) return;
      const count=[...grid.children].filter(card=>card.classList.contains('nvl-card')&&!card.hidden).length;
      if(!count)return;
      const columns=Math.ceil(count/2);
      // For short selections keep two real rows, but make them slightly wider
      // than their viewport so automatic scrolling remains meaningful.
      const visibleWidth=viewport.clientWidth||section.clientWidth||300;
      const share=columns===1?1:columns===2?.63:(columns===3?.44:columns===4?.39:.34);
      const cell=Math.round(Math.max(visibleWidth<=600?165:235, visibleWidth*share));
      grid.style.setProperty('--nvl-rail-column-width',cell+'px');
      s.max=Math.max(0,viewport.scrollWidth-viewport.clientWidth);
      if(viewport.scrollLeft>s.max)viewport.scrollLeft=s.max;
      if(s.max===0)s.dir=1;
    }
    const postpone=ms=>{s.holdUntil=now()+ms;s.lastTime=0;};
    function releasePointer(event) {
      if(!s.dragging)return;
      if(s.pointer!==null && event.pointerId!==s.pointer)return;
      s.dragging=false;
      s.pointer=null;
      viewport.classList.remove('is-dragging');
      if(s.moved){
        postpone(1700);
        // A post-drag click on a link must not navigate accidentally.
        s.suppressClick=true;
        setTimeout(()=>{if(!s.destroyed)s.suppressClick=false;},180);
      } else postpone(600);
    }
    listen(viewport,'pointerdown',event=>{
      if(event.pointerType==='touch'||event.button!==0||event.target.closest('button'))return;
      s.dragging=true;s.moved=false;s.pointer=event.pointerId;
      s.startX=event.clientX;s.startScroll=viewport.scrollLeft;
      s.holdUntil=Infinity;
    });
    listen(viewport,'pointermove',event=>{
      if(!s.dragging||event.pointerId!==s.pointer)return;
      const dx=event.clientX-s.startX;
      if(Math.abs(dx)>5&&!s.moved){
        s.moved=true;viewport.classList.add('is-dragging');
        try{viewport.setPointerCapture(event.pointerId);}catch(_){}
      }
      if(s.moved){
        event.preventDefault();
        viewport.scrollLeft=Math.max(0,Math.min(s.max,s.startScroll-dx));
        s.dir=dx>0?-1:1;
      }
    });
    listen(viewport,'pointerup',releasePointer);
    listen(viewport,'pointercancel',releasePointer);
    listen(viewport,'lostpointercapture',releasePointer);
    listen(viewport,'click',event=>{
      if(!s.suppressClick)return;
      event.preventDefault();event.stopImmediatePropagation();
      s.suppressClick=false;
    },true);
    listen(viewport,'touchstart',()=>{s.holdUntil=Infinity;},{passive:true});
    listen(viewport,'touchend',()=>postpone(1600),{passive:true});
    listen(viewport,'touchcancel',()=>postpone(800),{passive:true});
    listen(viewport,'wheel',()=>postpone(1100),{passive:true});
    listen(viewport,'focusin',()=>{s.focused=true;});
    listen(viewport,'focusout',event=>{
      if(!viewport.contains(event.relatedTarget)){s.focused=false;postpone(700);}
    });
    listen(viewport,'keydown',event=>{
      if(event.key==='ArrowRight'||event.key==='ArrowLeft')postpone(1400);
    });
    listen(motion,'change',()=>{postpone(500);s.lastTime=0;});
    s.visibility=new IntersectionObserver(entries=>{
      s.visible=!!entries[0]?.isIntersecting;s.lastTime=0;
    },{threshold:0.01});
    s.visibility.observe(viewport);
    s.resize=new ResizeObserver(()=>{
      clearTimeout(s.resizeTimer);
      s.resizeTimer=setTimeout(()=>{if(!s.destroyed){measure();s.lastTime=0;}},200);
    });
    s.resize.observe(viewport);
    measure();
    function tick(t) {
      if(s.destroyed)return;
      const dt=s.lastTime?Math.min(64,t-s.lastTime):0;
      s.lastTime=t;
      if(s.max>1&&!motion.matches&&!s.dragging&&!s.focused&&s.visible&&!document.hidden&&t>s.holdUntil){
        let next=viewport.scrollLeft+s.dir*dt*SCROLL_PX_PER_SECOND/1000;
        if(next>=s.max){next=s.max;s.dir=-1;s.holdUntil=t+500;}
        else if(next<=0){next=0;s.dir=1;s.holdUntil=t+500;}
        viewport.scrollLeft=next;
      }
      s.raf=requestAnimationFrame(tick);
    }
    s.raf=requestAnimationFrame(tick);
  }
  function filter(section, group) {
    const grid=section.querySelector('[data-nvl-grid]');
    if(!grid)return;
    let total=0;
    grid.querySelectorAll(':scope > .nvl-card').forEach(card=>{
      const groups=(card.dataset.nvlGroups||'').trim().split(/\s+/);
      card.hidden=group!=='all'&&!groups.includes(group);
      if(!card.hidden)total++;
    });
    section.querySelectorAll('[data-nvl-reference-filter]').forEach(btn=>{
      const selected=btn.dataset.nvlReferenceFilter===group;
      btn.classList.toggle('is-active',selected);
      btn.setAttribute('aria-pressed',String(selected));
    });
    const result=section.querySelector('[data-nvl-reference-count]');
    if(result)result.textContent=total+' çalışma';
    init(section,host.querySelector('main.nv-landing')?.dataset.theme||'');
  }
  function refresh() {
    const main=host.querySelector('main.nv-landing');
    for(const section of instances.keys())if(!main?.contains(section))teardown(section);
    if(!main)return;
    main.querySelectorAll('section[data-component]').forEach(section=>{
      if(['references','scripts','blog'].includes(section.dataset.component))init(section,main.dataset.theme);
    });
  }
  host.addEventListener('click',event=>{
    const btn=event.target.closest('[data-nvl-reference-filter]');
    if(!btn||!host.contains(btn))return;
    const section=btn.closest('section[data-component="references"]');
    if(section)filter(section,btn.dataset.nvlReferenceFilter);
  });
  host.addEventListener('landing:themechange',()=>requestAnimationFrame(refresh));
  refresh();
})();
