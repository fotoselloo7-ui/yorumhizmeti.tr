// Read-only: invoke through the browser's DOM evaluation, never a browser launcher.
export function measureStorefront() {
  const rect = el => {const r = el.getBoundingClientRect(); return {x:r.x,y:r.y,width:r.width,height:r.height,right:r.right,bottom:r.bottom};};
  const visible = el => el.getClientRects().length && getComputedStyle(el).visibility !== 'hidden';
  const width = document.documentElement.clientWidth;
  const scrolled = el => {for(let p=el.parentElement;p;p=p.parentElement){const s=getComputedStyle(p);if(/auto|scroll|hidden|clip/.test(s.overflowX)&&p.scrollWidth>p.clientWidth+1)return true;}return false;};
  const outliers = Array.from(document.querySelectorAll('main h1,main h2,main h3,main p,main button,main input,main select,main textarea,main article,main .yv-faq-list,main .nv26-catalog-tile,main .nv40-product-card')).filter(el=>visible(el)&&!scrolled(el)).map(el=>({tag:el.tagName,class:el.className,text:el.textContent.trim().slice(0,65),...rect(el)})).filter(r=>r.x < -2 || r.right > width+2);
  const faq = Array.from(document.querySelectorAll('.premium-faq-question')).filter(visible).map(el=>({text:el.textContent.trim(),font:getComputedStyle(el).fontSize,padding:getComputedStyle(el).padding,expanded:el.getAttribute('aria-expanded'),answerHidden:document.getElementById(el.getAttribute('aria-controls'))?.hidden,...rect(el)}));
  const catalog = Array.from(document.querySelectorAll('.nv40-product-card')).map(el=>({card:rect(el),title:rect(el.querySelector('h3')),price:rect(el.querySelector('.nv40-product-bottom strong')),cta:rect(el.querySelector('.nv40-product-bottom a'))}));
  const art = Array.from(document.querySelectorAll('.yv-simple-hero-art-v12,.yv-blog-hero-art-v8,.yv-category-hero-art-v5')).map(el=>({class:el.className,display:getComputedStyle(el).display}));
  return {url:location.pathname,viewport:{width:innerWidth,height:innerHeight},documentWidth:width,scrollWidth:document.documentElement.scrollWidth,outliers,faq,catalog,art,h1:document.querySelector('h1')?.textContent.trim(),motion:Array.from(document.querySelectorAll('.nv-motion-enter')).map(el=>({class:el.className,opacity:getComputedStyle(el).opacity,animation:getComputedStyle(el).animationName})),brokenImages:Array.from(document.images).filter(el=>visible(el)&&el.complete&&!el.naturalWidth).map(el=>el.getAttribute('src'))};
}
export function validateStorefront(state) {
  const errors=[];
  if(state.scrollWidth>state.documentWidth+1)errors.push('Document horizontal overflow');
  if(state.outliers.length)errors.push('Content outside viewport: '+state.outliers.map(x=>x.class||x.tag).join(', '));
  if(state.brokenImages.length)errors.push('Broken visible images');
  for(const q of state.faq){if(q.height<44)errors.push('FAQ target under 44px');if(q.font.replace('px','')*1<14)errors.push('FAQ type under 14px');if(q.expanded==='true'&&q.answerHidden)errors.push('FAQ ARIA/visibility mismatch');}
  for(const card of state.catalog){if(card.price.right>card.cta.x+1&&card.price.y<card.cta.bottom&&card.cta.y<card.price.bottom)errors.push('Price/CTA overlap');if(card.cta.bottom>card.card.bottom+1)errors.push('CTA outside card');}
  if(state.viewport.width<=600&&state.art.some(x=>x.display!=='none'))errors.push('Mobile decorative hero remains');
  return errors;
}
