import { chromium } from 'playwright';
import fs from 'node:fs';
import path from 'node:path';

const origin = process.env.QA_ORIGIN || 'http://127.0.0.1:8006';
const output = process.env.QA_OUTPUT || 'qa-artifacts';
const runs = [
  { route:'/',slug:'anasayfa' },
  { route:'/kategoriler',slug:'tum-hizmetler' },
  { route:'/kategori/instagram-hizmetleri',slug:'instagram' },
  { route:'/kategori/web-site-hizmetleri',slug:'web-site' },
  { route:'/paket/google-harita-yorum-toplama-baslangic-paketi-10-davet',slug:'paket-detay' },
  { route:'/blog',slug:'blog' },
  { route:'/iletisim',slug:'iletisim' },
  { route:'/sss',slug:'sss' },
  { route:'/sepet',slug:'sepet' },
  { route:'/giris',slug:'giris' },
  { route:'/kayit',slug:'kayit' },
  { route:'/sayfa/hakkimizda',slug:'hakkimizda' },
  { route:'/admin/giris',slug:'admin-giris' },
];
const screens=[
  {w:390,h:844,name:'mobil'},
  {w:768,h:1024,name:'tablet'},
  {w:1440,h:900,name:'masaustu'},
  {w:1920,h:1080,name:'genis'}
];
fs.mkdirSync(output,{recursive:true});
const browser=await chromium.launch({headless:true,args:['--no-sandbox']});
const results=[];
let failed=false;

for(const screen of screens){
  const ctx=await browser.newContext({viewport:{width:screen.w,height:screen.h},deviceScaleFactor:1});
  for(const p of runs){
    // Run a wide range of routes on mobile/desktop; tablet/wide sanity-check key pages.
    if ((screen.name==='tablet'||screen.name==='genis') && !['anasayfa','tum-hizmetler','instagram','paket-detay','blog'].includes(p.slug)) continue;
    const page=await ctx.newPage();
    const errors=[];
    page.on('pageerror',e=>errors.push('JS: '+e.message));
    page.on('console',msg=>{if(msg.type()==='error'&&!msg.text().includes('favicon'))errors.push('CONSOLE: '+msg.text().slice(0,160));});
    let status=0,fullUrl='';
    try{
      const res=await page.goto(origin+p.route,{waitUntil:'domcontentloaded',timeout:30000});
      status=res?.status()||0;
      await page.waitForTimeout(500);
      fullUrl=page.url();
      const report=await page.evaluate(()=>{
        const vw=window.innerWidth;
        const root=document.documentElement;
        const main=document.querySelector('main')||document.body;
        const failedImgs=[...document.querySelectorAll('img')].filter(i=>i.complete&&i.naturalWidth===0).map(i=>(i.currentSrc||i.src||'').slice(0,180)).slice(0,10);
        const overflow=[...document.querySelectorAll('body *')]
          .filter(el=>{
            const rect=el.getBoundingClientRect();
            const cs=getComputedStyle(el);
            if(cs.display==='none'||cs.visibility==='hidden'||rect.width===0||rect.height===0)return false;
            if(cs.position==='absolute'||cs.position==='fixed')return false;
            if(el.matches('svg,svg *,script,style'))return false;
            return rect.right>vw+12||rect.left < -12;
          })
          .slice(0,12)
          .map(el=>({tag:el.tagName,cls:String(el.className).slice(0,80),r:Math.round(el.getBoundingClientRect().right)}));
        const geom = {};
        for (const selector of [
          '.container','.yh6-hero','.yh6-hero-grid','.yh6-hero-art','.yh6-platform-bar','.yh6-why',
          '.yv-category-hero-v5','.yv-category-hero-grid-v5','.yv-category-hero-art-v5','.yv-category-hero-woman-v5',
          '.yv-blog-hero-grid-v8','.yv-blog-hero-art-v8','.yv-blog-woman-v8',
          '.yv-product-hero-grid-v5','.yv-product-hero-visual-v5',
          '.yh18-featured-head','.yh18-featured-tabs','.site-footer','.footer-grid-v9'
        ]){
          const el=document.querySelector(selector);
          if (!el) continue;
          const b=el.getBoundingClientRect();
          const st=getComputedStyle(el);
          geom[selector]={x:Math.round(b.x),y:Math.round(b.y),w:Math.round(b.width),h:Math.round(b.height),display:st.display,grid:st.gridTemplateColumns,overflow:st.overflow};
        }
        return {
          geom,
          title:document.title,
          h1:(document.querySelector('h1')?.textContent||'').trim().slice(0,100),
          bodyText:(main.innerText||'').slice(0,200),
          rootScroll:root.scrollWidth,
          viewport:vw,
          overflow,
          failedImgs,
          brokenLinks:[...document.querySelectorAll('a[href]')]
            .filter(a=>a.getAttribute('href')==='#'||a.getAttribute('href')==='javascript:void(0)')
            .slice(0,12).map(a=>(a.textContent||a.getAttribute('aria-label')||'').trim().slice(0,50))
        };
      });
      let switcher=null;
      if(p.route==='/' && screen.name==='masaustu'){
        const tabs=page.locator('[data-featured-tab]');
        const panes=page.locator('[data-featured-pane]:not([hidden])');
        const count=await tabs.count();
        if(count>1){
          const first=await panes.first().getAttribute('data-featured-pane');
          await tabs.nth(1).click();
          const second=await panes.first().getAttribute('data-featured-pane');
          switcher={tabs:count,changed:first!==second};
          if(!switcher.changed){errors.push('Featured category tab did not switch content');failed=true}
        }
      }
      const record={route:p.route,screen:screen.name,status,url:fullUrl,report,switcher,errors};
      results.push(record);
      const shot=path.join(output,`${p.slug}-${screen.name}.png`);
      await page.screenshot({path:shot,fullPage:true,animations:'disabled',timeout:30000});
      if(status>=500||report.bodyText.includes('Veritabanı bağlantı hatası')||report.rootScroll>screen.w+3){
        failed=true;
      }
      console.log(`${screen.name.padEnd(9)} ${p.route.padEnd(60)} HTTP ${status} overflow=${report.overflow.length} missingImg=${report.failedImgs.length} errors=${errors.length}`);
    }catch(e){
      failed=true;results.push({route:p.route,screen:screen.name,status,url:fullUrl,errors:[String(e)]});
      console.error('FAIL',screen.name,p.route,String(e).slice(0,250));
    }finally{await page.close()}
  }
  await ctx.close();
}

fs.writeFileSync(path.join(output,'report.json'),JSON.stringify({created:new Date().toISOString(),runs:results},null,2));
const bad=results.filter(x=>x.status>=500||x.status===0||x.report?.rootScroll>x.report?.viewport+3||x.errors?.some(e=>e.includes('did not switch')));
console.log(`\nREPORT: ${results.length} page/viewport combinations; critical failures: ${bad.length}; images and overflow details saved to report.json`);
await browser.close();
if(failed) process.exitCode=1;
