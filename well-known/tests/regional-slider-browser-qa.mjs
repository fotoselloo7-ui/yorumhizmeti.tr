import { chromium } from 'playwright';
import { fileURLToPath } from 'node:url';
import path from 'node:path';
const root=path.resolve(path.dirname(fileURLToPath(import.meta.url)),'..');
const styleBase=path.join(root,'public/assets/css/landing-themes.css');
const styleRail=path.join(root,'public/assets/css/regional-showcase-v1.css');
const script=path.join(root,'public/assets/js/regional-showcase-v1.js');
const browser=await chromium.launch({headless:true,args:['--no-sandbox']});
function card(n,kind){
  return `<article class="nvl-card nvl-card-${kind}" data-nvl-groups="${kind==='reference'?(n%2?'agency':'marketing'):''}">
  <div class="nvl-card-media"><a class="nvl-card-visual-link" href="/test-${kind}-${n}"><span>Özgün görsel ${n}</span></a></div>
  <div class="nvl-card-copy"><div class="nvl-card-tags"><small>Hizmet kategorisi</small></div>
    <h3><a href="/test-${kind}-${n}">NetVera Teknoloji Yazılım ${kind} - ${n} Uzun Başlıklı Kesilmeden Görünen Deneme Referansı</a></h3>
    <p>Uygulamanın farklı hizmet kapsamları, teslim süreçleri ve çalışma detaylarını anlatan uzun açıklama metni.</p>
    <div class="nvl-card-footer"><a class="nvl-card-link" href="/test-${kind}-${n}">Detaylı incele</a></div>
  </div></article>`;
}
function section(kind,num){
  const buttons=kind==='references'?`<div class="nvl-reference-filters" data-nvl-reference-filters><button data-nvl-reference-filter="all" class="is-active" aria-pressed="true">Tümü</button><button data-nvl-reference-filter="agency" aria-pressed="false">Ajans</button><button data-nvl-reference-filter="marketing" aria-pressed="false">SEO</button><small data-nvl-reference-count>3 çalışma</small></div>`:'';
  const singular={references:'reference',scripts:'script',blog:'blog'}[kind];
  return `<section class="nvl-section nvl-section-${kind}" data-component="${kind}" data-variant="editorial">
    <div class="nvl-container"><header class="nvl-section-heading"><div><span class="nvl-eyebrow">NETVERA</span><h2>Seçili Çalışmalarımız</h2></div></header>
      ${buttons}<div class="nvl-showcase-viewport" data-nvl-rail-viewport>
        <div class="nvl-card-grid nvl-card-grid-${kind}" data-nvl-grid>${Array.from({length:num},(_,n)=>card(n+1,singular)).join('')}</div>
      </div></div></section>`;
}
function assert(p,msg){if(!p)throw new Error(msg);}
try{
  for (const [width,height] of [[1280,900],[390,844]]) {
    const context=await browser.newContext({viewport:{width,height}});
    const page=await context.newPage();
    const errors=[];
    page.on('pageerror',e=>errors.push(e.message));
    await page.setContent('<!doctype html><html><head><meta charset="utf-8">'+
      '<style>body{margin:0;font-family:Arial,sans-serif}.nv-landing{--nvl-ink:#192646;--nvl-brand:#f47708;--nvl-soft:#fff2e7;--nvl-surface:#fff;--nvl-border:#d9dfea;--nvl-muted:#73809b;background:#fff7f0;color:#192646}.nvl-container{max-width:1200px;margin:0 auto;padding:0 20px}</style>'+
      '</head><body><div data-landing-theme-host><main class="nv-landing" data-theme="landing-01">'+
      section('references',3)+section('scripts',14)+section('blog',14)+'</main></div></body></html>');
    await page.addStyleTag({path:styleBase});
    await page.addStyleTag({path:styleRail});
    await page.addScriptTag({path:script});

    const ref=page.locator('section[data-component="references"]');
    const software=page.locator('section[data-component="scripts"]');
    const blog=page.locator('section[data-component="blog"]');
    const refCards=ref.locator('.nvl-card');
    assert(await refCards.count()===3,'Exactly 3 real reference records');
    assert(await ref.evaluate(el=>el.classList.contains('is-nvl-static')&&!el.classList.contains('is-nvl-loop')),
      'Small portfolio should remain completely static '+width);
    const small=await ref.evaluate(section=>{
      const cards=[...section.querySelectorAll('.nvl-card')];
      const viewport=section.querySelector('[data-nvl-rail-viewport]');
      return {widths:cards.map(c=>c.getBoundingClientRect().width),
        overflow:viewport.scrollWidth-viewport.clientWidth,
        unique:new Set(cards.map(c=>c.querySelector('h3 a').getAttribute('href'))).size};
    });
    assert(small.unique===3,'Static references duplicated');
    assert(small.widths.every(n=>n>=140&&n<=275),'Cards stretched beyond compact width '+width+' '+small.widths);
    assert(small.overflow<3,'3 reference items incorrectly scroll '+width+' '+small.overflow);
    const staticBefore=await ref.locator('[data-nvl-rail-viewport]').evaluate(el=>el.scrollLeft);
    await page.waitForTimeout(700);
    const staticAfter=await ref.locator('[data-nvl-rail-viewport]').evaluate(el=>el.scrollLeft);
    assert(Math.abs(staticAfter-staticBefore)<2,'Small reference collection moves unexpectedly');

    for(const section of [software,blog]){
      await section.locator('[data-nvl-rail-viewport]').scrollIntoViewIfNeeded();
      await page.mouse.move(0,0);
      await page.waitForTimeout(300);
      const stat=await section.evaluate(sec=>{
        const grid=sec.querySelector('[data-nvl-grid]');
        const cards=[...grid.querySelectorAll('.nvl-card')];
        const unique=new Set(cards.map(c=>c.querySelector('h3 a')?.getAttribute('href')));
        const titles=cards.map(c=>c.querySelector('h3'));
        return {
          active:sec.classList.contains('is-nvl-loop'),
          columns:grid.querySelectorAll(':scope > .nvl-rail-column').length,
          count:cards.length,unique:unique.size,
          oversized:cards.some(c=>c.getBoundingClientRect().width>275),
          clipped:titles.filter(t=>t.scrollHeight>t.clientHeight+2||t.scrollWidth>t.clientWidth+2).length
        };
      });
      assert(stat.active,'Genuine overflow should auto-loop '+width+'/'+await section.getAttribute('data-component'));
      assert(stat.columns===7,'Expected seven authentic two-row columns, not duplicate cards');
      assert(stat.count===14&&stat.unique===14,'Duplicate product/blog cards found');
      assert(!stat.oversized,'Product/blog cards enlarged to viewport');
      assert(stat.clipped===0,'Product/blog headlines clipped');
    }

    const slider=software.locator('[data-nvl-rail-viewport]');
    await slider.scrollIntoViewIfNeeded();
    await page.mouse.move(0,0);
    const before=await slider.evaluate(el=>el.scrollLeft);
    await page.waitForTimeout(1700);
    const after=await slider.evaluate(el=>el.scrollLeft);
    assert(after>before+16,'Continuous one-way auto-scroll did not move '+width+': '+before+'/'+after);

    const first=await software.locator('.nvl-rail-column').first().locator('h3 a').first().getAttribute('href');
    const step=await software.evaluate(sec=>{
      const grid=sec.querySelector('[data-nvl-grid]');
      const col=grid.querySelector('.nvl-rail-column');
      return col.getBoundingClientRect().width + parseFloat(getComputedStyle(grid).columnGap);
    });
    await slider.evaluate((el,step)=>{el.scrollLeft=step-1;},step);
    await page.waitForTimeout(400);
    const firstAfter=await software.locator('.nvl-rail-column').first().locator('h3 a').first().getAttribute('href');
    assert(firstAfter!==first,'Original first column was not moved to the end for continuous looping');
    assert(await software.locator('.nvl-card').count()===14,'Rotation cloned source cards');

    // Filter buttons preserve the source collection without adding new cards.
    await ref.locator('[data-nvl-reference-filter="marketing"]').click();
    assert(await ref.locator('.nvl-card:visible').count()===1,'Filtered reference count incorrect');
    await ref.locator('[data-nvl-reference-filter="all"]').click();
    assert(await ref.locator('.nvl-card:visible').count()===3,'Original references lost after resetting filter');

    // Mouse dragging is real interaction, not an inert grab cursor.
    await slider.scrollIntoViewIfNeeded();
    const box=await slider.boundingBox();
    const y=box.y+Math.min(55,box.height/3);
    const dragBefore=await slider.evaluate(el=>el.scrollLeft);
    await page.mouse.move(box.x+box.width*.78,y);
    await page.mouse.down();
    await page.mouse.move(box.x+box.width*.3,y,{steps:12});
    await page.mouse.up();
    const dragAfter=await slider.evaluate(el=>el.scrollLeft);
    assert(Math.abs(dragAfter-dragBefore)>25,'Mouse dragging did not change real scroll position');
    await page.mouse.move(0,0);

    // Theme switching must not add cards or carry the prior page's movement state.
    for(let i=1;i<=10;i++){
      await page.evaluate(i=>{
        const main=document.querySelector('main.nv-landing');
        main.dataset.theme='landing-'+String(i).padStart(2,'0');
        document.querySelector('[data-landing-theme-host]').dispatchEvent(
          new CustomEvent('landing:themechange',{detail:{theme:main.dataset.theme}}));
      },i);
      await page.waitForTimeout(55);
      assert(await refCards.count()===3,'Theme switching added reference duplicates: '+i);
      assert(await software.locator('.nvl-card').count()===14,'Theme switching damaged software cards: '+i);
    }
    assert(errors.length===0,'JavaScript errors '+width+': '+errors.join('; '));
    console.log('PASS '+width+'px compact 3-card static grid, 14-card two-row continuous looping, drag, filters, 10 themes, no cloning or clipped headings');
    await context.close();
  }
}finally{await browser.close();}
