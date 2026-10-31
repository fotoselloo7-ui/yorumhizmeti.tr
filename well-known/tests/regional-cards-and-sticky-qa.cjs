/* NetVera: CSS+JS behavior test for all 10 themes (no DB, no live requests). */
'use strict';
const {chromium} = require('playwright');
const fs = require('node:fs');
const path = require('node:path');
const root = path.resolve(__dirname, '..');
const css = [
  'public/assets/css/landing-themes.css',
  'public/assets/css/editorial-landings.css',
  'public/assets/css/regional-showcase-v1.css'
].map(p => fs.readFileSync(path.join(root,p),'utf8')).join('\n');
const code = fs.readFileSync(path.join(root,'public/assets/js/regional-showcase-v1.js'),'utf8');
const make = (theme,count) => {
  const cards = Array.from({length:count}, (_,i) => `<article class="nvl-card nvl-card-blog" data-nvl-groups=" agency ">
  <div class="nvl-card-media"><div class="nvl-card-media-mark">N</div></div>
  <div class="nvl-card-copy"><div class="nvl-card-tags"><small>Blog</small></div>
  <h3><a href="#card-${i}">${i===0?'Alanya Google Ads çalışma rehberi': 'Alanya reklam yönetimi - '+i}</a></h3>
  <div class="nvl-card-footer"><a class="nvl-card-link" href="#card-${i}">Yazıyı oku ↗</a></div></div></article>`).join('');
  const faq = Array.from({length:9},(_,i) => `<div class="nvl-deep-qa"><h4>Soru ${i+1}?</h4><div>Cevap metni uzun olabilir; bütün yanıtlar erişilebilir kalmalı. ${'Detaylı açıklama. '.repeat(7)}</div></div>`).join('');
  return `<!doctype html><html lang="tr"><head><meta name="viewport" content="width=device-width,initial-scale=1"><style>*,*::before,*::after{box-sizing:border-box}body{margin:0;font:14px Arial,sans-serif}</style><style>${css}</style></head><body><div data-landing-theme-host><main class="nv-landing" data-theme="${theme}">
    <section class="nvl-section nvl-section-blog" data-component="blog" data-variant="editorial"><div class="nvl-container">
      <header class="nvl-section-heading"><div><h2>Google Ads Rehberleri</h2></div></header>
      <div class="nvl-showcase-viewport" data-nvl-rail-viewport><div class="nvl-card-grid" data-nvl-grid>${cards}</div></div>
    </div></section>
    <section class="nvl-deep-guide"><div class="nvl-container"><div class="nvl-deep-heading"><h2>Yerel Uzman Rehberi</h2></div>
    <div class="nvl-deep-layout"><article class="nvl-deep-prose">
      ${Array.from({length:22},() => '<p style="min-height:110px">Yerel arama niyeti ve kullanıcı ihtiyaçlarını ayrıntılarıyla açıklayan uzun rehber paragrafı.</p>').join('')}
    </article><aside class="nvl-deep-questions" tabindex="0"><h3>Merak edilenler</h3>${faq}</aside></div></div></section>
    </main></div></body></html>`;
};
function check(ok, message){if(!ok)throw new Error(message);}
(async()=>{
  const browser = await chromium.launch({headless:true});
  let n=0;
  for (const width of [1920,1440,900,390]) for (let t=1;t<=10;t++) for (const count of [3,10]) {
    const page=await browser.newPage({viewport:{width,height:850},reducedMotion:'reduce'});
    const theme='landing-'+String(t).padStart(2,'0');
    await page.setContent(make(theme,count));
    await page.addScriptTag({content:code});
    await page.waitForTimeout(80);
    const metrics=await page.evaluate(() => {
      const section=document.querySelector('.nvl-section-blog');
      const viewport=section.querySelector('[data-nvl-rail-viewport]');
      const cards=[...section.querySelectorAll('.nvl-card')];
      const first=cards[0];
      const h3=first.querySelector('h3').getBoundingClientRect();
      const cta=first.querySelector('.nvl-card-footer').getBoundingClientRect();
      const side=document.querySelector('.nvl-deep-questions');
      const style=getComputedStyle(side);
      return {
        scrollMax:viewport.scrollWidth-viewport.clientWidth,
        viewportWidth:viewport.clientWidth,
        firstGap:cta.top-h3.bottom,
        cardHeight:first.getBoundingClientRect().height,
        cards:cards.length,
        controls:section.querySelectorAll('.nvl-rail-controls button').length,
        rail:section.classList.contains('is-nvl-rail'),
        sticky:style.position,
        maxHeight:style.maxHeight,
        width:document.documentElement.scrollWidth,
        asideHeight:side.offsetHeight,
        asideOverflow:style.overflowY,
        asideTop:parseFloat(style.top),
        cardRects:cards.map(c=>c.getBoundingClientRect().toJSON()),
        viewportRect:viewport.getBoundingClientRect().toJSON(),

      };
    });
    const label=`${theme}, ${width}px, ${count} cards`;
    check(metrics.cards===count,label+': lost card');
    check(metrics.width<=width+2,label+': page horizontal overflow');
    check(metrics.firstGap>=0&&metrics.firstGap<36,label+': title/CTA gap='+metrics.firstGap);
    check(metrics.cardHeight<340,label+': card contains unnecessary whitespace '+metrics.cardHeight);
    for(let i=0;i<metrics.cardRects.length;i++) for(let j=i+1;j<metrics.cardRects.length;j++){
      const a=metrics.cardRects[i],b=metrics.cardRects[j];
      const x=Math.min(a.right,b.right)-Math.max(a.left,b.left);
      const y=Math.min(a.bottom,b.bottom)-Math.max(a.top,b.top);
      check(x<1||y<1,label+': card overlap between #'+i+' and #'+j);
    }
    if(count>4){
      const visible=metrics.cardRects.filter(c=>c.left>=metrics.viewportRect.left-2&&c.right<=metrics.viewportRect.right+2);
      const cols=metrics.viewportRect.width>=1050?4:metrics.viewportRect.width>=660?3:2;
      check(visible.length>=2*cols,label+': not enough full columns / clipped cards '+visible.length+' / '+2*cols);
    }
    if (count<=4) {
      check(!metrics.rail&&metrics.controls===0,label+': small set should not become carousel');
    } else {
      check(metrics.rail&&metrics.controls===2,label+': missing manual navigation');
      // Scroll to the REAL end: final column must fit entirely in viewport.
      await page.evaluate(() => {
        const v=document.querySelector('[data-nvl-rail-viewport]');
        v.scrollLeft=v.scrollWidth;
      });
      await page.waitForTimeout(120);
      const end=await page.evaluate(()=>{
        const v=document.querySelector('[data-nvl-rail-viewport]').getBoundingClientRect();
        const last=[...document.querySelectorAll('.nvl-card')].at(-1).getBoundingClientRect();
        return {v:v.toJSON(),card:last.toJSON()};
      });
      check(end.card.right<=end.v.right+2&&end.card.left>=end.v.left-2,
        label+': last card cut off on the right/left: '+JSON.stringify(end));
      // Controls still navigate toward the start of the real collection.
      await page.locator('.nvl-rail-prev').click({force:true});
      await page.waitForTimeout(80);
    }
    if(width>900){
      check(metrics.sticky==='sticky',label+': Q&A not sticky as whole column');
      check(metrics.asideOverflow==='visible',label+': Q&A has internal scrollbar');
      check(metrics.maxHeight==='none',label+': Q&A has height clipping');
      const expected=Math.min(96,850-metrics.asideHeight-24);
      check(Math.abs(metrics.asideTop-expected)<=2,label+': expected sticky top '+expected+' got '+metrics.asideTop);
    } else {
      check(metrics.sticky==='static',label+': Q&A must stack on tablets and mobiles');
    }
    if (t===3&&count===10 && width===1440 && process.env.RUNNER_TEMP){
      await page.screenshot({path:process.env.RUNNER_TEMP+'/netvera-regional-cards-desktop.png',fullPage:true});
    }
    await page.close();n++;
  }
  await browser.close();
  console.log(`PASS ${n}: all 10 themes, 4 viewport widths, no card overlap/cutoff, whole-column sticky FAQ`);
})().catch(e=>{console.error(e);process.exit(1)});
