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
  for(const [width,height] of [[1280,900],[390,844]]){
    const context=await browser.newContext({viewport:{width,height}});
    const page=await context.newPage();
    const errors=[];
    page.on('pageerror',e=>errors.push(e.message));
    await page.setContent(`<!doctype html><html><head><meta charset="utf-8">
    <style>body{margin:0;font-family:Arial,sans-serif}.nv-landing{--nvl-ink:#192646;--nvl-brand:#f47708;--nvl-soft:#fff2e7;--nvl-surface:#fff;--nvl-border:#d9dfea;--nvl-muted:#73809b;background:#fff7f0;color:#192646}.nvl-container{max-width:1200px;margin:0 auto;padding:0 20px}</style>
    </head><body><div data-landing-theme-host><main class="nv-landing" data-theme="landing-01">
    ${section('references',3)}${section('scripts',6)}${section('blog',7)}
    </main></div></body></html>`);
    await page.addStyleTag({path:styleBase});
    await page.addStyleTag({path:styleRail});
    await page.addScriptTag({path:script});
    const reference=page.locator('[data-component="references"]');
    const references=reference.locator(':scope > .nvl-container > .nvl-showcase-viewport [data-nvl-grid] > .nvl-card');
    assert(await references.count()===3,'Three ORIGINAL references only');
    assert(await page.locator('[data-nvl-rail-clone],[data-nvl-rail-spacer]').count()===0,'Unexpected repeated references or blank placeholders');
    const stats=await page.evaluate(()=>{
      const sections=[...document.querySelectorAll('section.is-nvl-rail')];
      return sections.map(section=>{
        const view=section.querySelector('[data-nvl-rail-viewport]');
        const grid=section.querySelector('[data-nvl-grid]');
        const titles=[...section.querySelectorAll('.nvl-card-copy h3')];
        return {kind:section.dataset.component,count:grid.querySelectorAll(':scope > .nvl-card').length,
          overflow:view.scrollWidth-view.clientWidth,
          rows:getComputedStyle(grid).gridTemplateRows,
          clipped:titles.filter(el=>el.scrollHeight>el.clientHeight+2||el.scrollWidth>el.clientWidth+2).length,
          clippedDetails:titles.filter(el=>el.scrollHeight>el.clientHeight+2||el.scrollWidth>el.clientWidth+2).slice(0,2).map(el=>({text:el.textContent,scrollWidth:el.scrollWidth,clientWidth:el.clientWidth,scrollHeight:el.scrollHeight,clientHeight:el.clientHeight,display:getComputedStyle(el).display,whiteSpace:getComputedStyle(el).whiteSpace,overflow:getComputedStyle(el).overflow,clamp:getComputedStyle(el).webkitLineClamp})),
          hiddenOriginals:[...grid.children].filter(el=>el.hasAttribute('data-nvl-rail-clone')).length};
      });
    });
    for(const group of stats){
      assert(group.clipped===0,'Card title clipped '+width+'/'+group.kind+': '+JSON.stringify(group.clippedDetails));
      assert(group.hiddenOriginals===0,'Duplicate nodes '+width+'/'+group.kind);
      assert(group.rows.split(' ').filter(Boolean).length>=2,'Two real grid rows missing: '+width+'/'+group.kind+':'+group.rows);
      assert(group.overflow>40,'Autoplay cannot move, no horizontal overflow: '+width+'/'+group.kind+':'+group.overflow);
    }
    const viewport=reference.locator('[data-nvl-rail-viewport]');
    await viewport.scrollIntoViewIfNeeded();
    const before=await viewport.evaluate(el=>el.scrollLeft);
    await page.waitForTimeout(2550);
    const after=await viewport.evaluate(el=>el.scrollLeft);
    assert(after>before+20,'Autoplay does not move on its own '+width+': '+before+' -> '+after);
    await reference.locator('[data-nvl-reference-filter="marketing"]').click();
    const filtered=await reference.locator('[data-nvl-grid] > .nvl-card:visible').count();
    assert(filtered===1,'Filtered portfolio should show only its original matching cards '+width+':'+filtered);
    await reference.locator('[data-nvl-reference-filter="all"]').click();
    assert(await reference.locator('[data-nvl-grid] > .nvl-card:visible').count()===3,'Filter reset lost original cards');
    await reference.locator('[data-nvl-rail-viewport]').scrollIntoViewIfNeeded();
    const box=await viewport.boundingBox();
    const y=box.y+Math.min(70,box.height/3);
    await page.mouse.move(box.x+box.width*.8,y);
    await page.mouse.down();
    await page.mouse.move(box.x+box.width*.3,y,{steps:8});
    await page.mouse.up();
    assert(await references.count()===3,'Mouse drag created duplicate reference cards');
    for(let i=1;i<=10;i++){
      await page.evaluate(i=>{
        const main=document.querySelector('main.nv-landing');
        main.dataset.theme='landing-'+String(i).padStart(2,'0');
        document.querySelector('[data-landing-theme-host]').dispatchEvent(
          new CustomEvent('landing:themechange',{detail:{theme:main.dataset.theme}}));
      },i);
      await page.waitForTimeout(45);
      assert(await references.count()===3,'Switching to theme '+i+' duplicates references');
    }
    assert(errors.length===0,'Browser JS error '+width+':'+errors.join('; '));
    console.log('PASS regional carousel '+width+'px: unique cards, 2 rows, autoplay, no clipping, filters and 10 themes');
    await context.close();
  }
}finally{await browser.close();}
