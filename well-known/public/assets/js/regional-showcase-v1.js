(() => {
    'use strict';
    const landingHost = document.querySelector('[data-landing-theme-host]');
    if (!landingHost) return;
    const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const items = new WeakMap();
    const states = new Set();

    function dispose(section) {
        const old = items.get(section);
        if (!old) return;
        old.cancelled = true;
        if (old.frame) cancelAnimationFrame(old.frame);
        old.events.forEach(([node, name, cb]) => node.removeEventListener(name, cb));
        old.track.querySelectorAll('[data-nvl-rail-clone],[data-nvl-rail-spacer]').forEach(n => n.remove());
        old.viewport.removeAttribute('tabindex');
        old.viewport.removeAttribute('aria-label');
        items.delete(section);
        states.delete(old);
    }

    function activate(section, active) {
        dispose(section);
        section.classList.toggle('is-nvl-rail', active);
        if (!active) return;
        const viewport = section.querySelector('[data-nvl-rail-viewport]');
        const track = section.querySelector('[data-nvl-grid]');
        if (!viewport || !track) return;
        const originals = [...track.querySelectorAll(':scope > .nvl-card:not([hidden])')];
        if (!originals.length) return;
        // Only the scrollbar is enhanced. Original links/cards stay ordinary HTML.
        viewport.setAttribute('tabindex', '0');
        viewport.setAttribute('aria-label', 'İki satırlı kart vitrini; yatay kaydırarak tüm öğeleri inceleyin');
        viewport.scrollLeft = 0;
        const state = {viewport,track,cancelled:false,frame:0,events:[],paused:false,last:0,phase:0,distance:0};
        const add = (node, type, cb) => {node.addEventListener(type, cb);state.events.push([node,type,cb]);};
        items.set(section,state);
        states.add(state);
        const prepare = () => {
            if(state.cancelled)return;
            track.querySelectorAll('[data-nvl-rail-clone],[data-nvl-rail-spacer]').forEach(n=>n.remove());
            viewport.scrollLeft=0;
            const original=[...track.querySelectorAll(':scope > .nvl-card:not([hidden])')];
            // A single viewport's worth of cards does not need artificial movement.
            if (motion.matches || original.length < 5 || track.scrollWidth <= viewport.clientWidth + 5) {
                state.distance=0;return;
            }
            if (original.length % 2 === 1) {
                const spacer=document.createElement('span');
                spacer.className='nvl-rail-spacer';
                spacer.dataset.nvlRailSpacer='1';
                spacer.setAttribute('aria-hidden','true');
                track.appendChild(spacer);
            }
            const copies=original.map(card=>{
                const copy=card.cloneNode(true);
                copy.dataset.nvlRailClone='1';
                copy.setAttribute('aria-hidden','true');
                copy.querySelectorAll('a,button,input,[tabindex]').forEach(node=>node.setAttribute('tabindex','-1'));
                return copy;
            });
            track.append(...copies);
            state.distance=copies.length ? copies[0].offsetLeft - original[0].offsetLeft : 0;
            // Fallback for uncommon grid measuring during async font paint.
            if(state.distance<=0){copies.forEach(c=>c.remove());state.distance=0;}
        };
        add(viewport,'mouseenter',()=>state.paused=true);
        add(viewport,'mouseleave',()=>state.paused=false);
        add(viewport,'focusin',()=>state.paused=true);
        add(viewport,'focusout',()=>{state.paused=false;});
        add(viewport,'touchstart',()=>state.paused=true);
        add(viewport,'touchend',()=>{state.paused=false;state.last=0;});
        add(viewport,'wheel',()=>{state.paused=true;});
        prepare();
        let prev=0;
        const frame=(timestamp)=>{
            if(state.cancelled)return;
            const delta=prev?Math.min(60,timestamp-prev):0;prev=timestamp;
            if (state.distance>0 && !state.paused && !document.hidden && !motion.matches) {
                viewport.scrollLeft+=delta*.022; // ~22px / second, leisurely movement right-to-left.
                if(viewport.scrollLeft>=state.distance)viewport.scrollLeft-=state.distance;
            }
            state.frame=requestAnimationFrame(frame);
        };
        if(state.distance>0)state.frame=requestAnimationFrame(frame);
        state.refresh=()=>{ if(state.frame)cancelAnimationFrame(state.frame);state.frame=0;prepare();prev=0;if(state.distance>0)state.frame=requestAnimationFrame(frame); };
        // Browser fonts/images can change measurements; rebuild without losing source cards.
        add(window,'resize',()=>{clearTimeout(state.resizeDelay);state.resizeDelay=setTimeout(()=>!state.cancelled&&state.refresh(),220)});
        add(motion,'change',()=>state.refresh());
    }

    function filter(section,group) {
        const track=section.querySelector('[data-nvl-grid]');
        if(!track)return;
        let count=0;
        track.querySelectorAll(':scope > .nvl-card:not([data-nvl-rail-clone])').forEach(card=>{
            const visible=group==='all'||(card.dataset.nvlGroups||'').split(' ').includes(group);
            card.hidden=!visible;
            if(visible)count++;
        });
        section.querySelectorAll('[data-nvl-reference-filter]').forEach(btn=>{
            const active=btn.dataset.nvlReferenceFilter===group;
            btn.classList.toggle('is-active',active);btn.setAttribute('aria-pressed',String(active));
        });
        const label=section.querySelector('[data-nvl-reference-count]');
        if(label)label.textContent=count+' çalışma';
        const state=items.get(section);
        if(state){state.paused=false;state.refresh();}
    }

    function update() {
        const currentTheme=landingHost.querySelector('main.nv-landing')?.dataset.theme;
        if(!currentTheme)return;
        landingHost.querySelectorAll('main.nv-landing > section[data-component]').forEach(section=>{
            const kind=section.dataset.component;
            const variant=section.dataset.variant;
            // Match actual split-heading designs only; other ten-theme card layouts remain untouched.
            const side = (kind==='scripts' && currentTheme==='landing-01') ||
                         (kind==='blog' && variant==='editorial') ||
                         (kind==='references' && currentTheme==='landing-01');
            activate(section,side);
        });
    }

    landingHost.addEventListener('click',event=>{
        const button=event.target.closest('[data-nvl-reference-filter]');
        if(!button||!landingHost.contains(button))return;
        const section=button.closest('[data-component="references"]');
        if(section)filter(section,button.dataset.nvlReferenceFilter);
    });
    landingHost.addEventListener('landing:themechange',()=>requestAnimationFrame(update));
    update();
})();
