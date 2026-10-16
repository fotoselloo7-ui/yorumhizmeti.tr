/** Read-only browser geometry; screenshots and real clicks complement this probe. */
export function measureFeaturedFilters() {
  const s=document.querySelector('#featured');
  if(!s)return {missing:true};
  const rect=e=>e.getBoundingClientRect().toJSON();
  const visible=e=>!!e.getBoundingClientRect().height;
  const groupRail=s.querySelector('.yh24-featured-groups');
  const panels=[...s.querySelectorAll('[data-featured-filter-panel]')].filter(p=>!p.hidden);
  const panes=[...s.querySelectorAll('[data-featured-pane]')].filter(p=>!p.hidden);
  const results=s.querySelector('.yh18-featured-panes');
  return {viewport:[innerWidth,innerHeight],doc:document.documentElement.scrollWidth,
    group:s.querySelector('[data-featured-group].is-open')?.dataset.featuredGroup,
    all:s.classList.contains('is-group-results'),
    navHeight:rect(results).top-rect(groupRail).top,
    gutter:rect(s.querySelector('.container')).left+getComputedStyle(s.querySelector('.container')).paddingLeft.replace('px','')*1,
    groups:[...s.querySelectorAll('[data-featured-group]')].map(b=>({box:rect(b),text:b.innerText})),
    rails:panels.flatMap(p=>[...p.querySelectorAll('[data-featured-rail]')].filter(visible).map(r=>({kind:r.dataset.featuredRailKind,box:rect(r),width:r.clientWidth,scrollWidth:r.scrollWidth,scrollLeft:r.scrollLeft,overflow:getComputedStyle(r).overflowX,
      chips:[...r.children].filter(visible).map(e=>({text:e.innerText,box:rect(e),clipped:e.scrollWidth>e.clientWidth+2,active:e.getAttribute('aria-selected')==='true'}))}))),
    arrows:[...s.querySelectorAll('.nv43-rail-arrow')].filter(visible).length,
    children:panels.flatMap(p=>[...p.querySelectorAll('[data-featured-children-for]')].filter(visible).map(p=>p.dataset.featuredChildrenFor)),
    panes:panes.map(p=>p.dataset.featuredPane),
    cards:panes.flatMap(p=>[...p.querySelectorAll('.yh18-featured-card')].map(c=>({title:c.querySelector('h3')?.textContent,box:rect(c)})))
  };
}
export function validateFeaturedFilters(r) {
  const errors=[];
  if(r.missing)return ['Missing component'];
  if(r.doc>r.viewport[0])errors.push('Page overflow');
  if(r.viewport[0]<=768){
    if(r.arrows)errors.push('Desktop category arrows visible');
    if(new Set(r.groups.map(g=>Math.round(g.box.top))).size!==1)errors.push('Group row wrapped');
    if(r.groups.some(g=>g.box.height<44||g.box.height>50))errors.push('Group target size');
    if(r.navHeight<120||r.navHeight>190)errors.push('Navigation outside compact target: '+r.navHeight);
    if(r.children.length>1)errors.push('Nested desktop child panels');
    if(r.rails.some(x=>x.overflow!=='auto'||x.chips.some(c=>c.clipped)))errors.push('Chip scrolling/clipping');
    if(r.cards[0]&&r.cards[0].box.width<r.doc-100)errors.push('Package card too narrow');
  }
  return errors;
}
