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
        const groups=page.locator('[data-featured-group]');
        const count=await tabs.count();
        const groupCount=await groups.count();
        if(!groupCount)throw new Error('Featured category groups not rendered');
        const geometry=await page.locator('.yh24-featured-navigation').evaluate(el=>{
          const parent=el.getBoundingClientRect();
          return {
            overflow:el.scrollWidth>el.clientWidth+2,
            allInside:[...el.querySelectorAll('[data-featured-group]')].every(btn=>{
              const b=btn.getBoundingClientRect();
              return b.left>=parent.left-2 && b.right<=parent.right+2;
            })
          };
        });
        if(geometry.overflow||!geometry.allInside){
          errors.push('Featured groups overflow navigation container');failed=true;
        }
        if(count>1){
          const first=await panes.first().getAttribute('data-featured-pane');
          if(groupCount>1){
            await groups.nth(1).hover();
            const key=await groups.nth(1).getAttribute('data-featured-group');
            const panel=page.locator('[data-featured-filter-panel]:not([hidden])');
            if((await panel.getAttribute('data-featured-filter-panel'))!==key){
              throw new Error('Hover failed to reveal the group subcategory filters');
            }
            await panel.locator('[data-featured-tab]').first().click();
          }else{
            await page.locator('[data-featured-filter-panel]:not([hidden]) [data-featured-tab]').nth(1).click();
          }
          const second=await panes.first().getAttribute('data-featured-pane');
          switcher={tabs:count,groupCount,changed:first!==second};
          if(!switcher.changed){errors.push('Featured grouped filter did not switch packages');failed=true}
        }
      }
      if(p.route==='/' && screen.name==='mobil'){
        const groups=page.locator('[data-featured-group]');
        if(await groups.count()){
          await groups.last().click();
          const key=await groups.last().getAttribute('data-featured-group');
          const panel=page.locator('[data-featured-filter-panel]:not([hidden])');
          if((await panel.getAttribute('data-featured-filter-panel'))!==key){
            throw new Error('Mobile group tap failed to open its filter panel');
          }
          const overflow=await page.locator('.yh24-featured-subfilters').evaluate(el=>el.scrollWidth>el.clientWidth+3);
          if(overflow)throw new Error('Mobile subcategory filters overflow the page');
          if(!await panel.locator('[data-featured-tab]').count()){
            throw new Error('Active category filter panel has no live filter choices');
          }
        }
      }
      // Regression: account pages must be centered, legible and truly split on desktop.
      if((p.route==='/giris'||p.route==='/kayit') && (screen.name==='masaustu'||screen.name==='mobil')){
        const auth=page.locator('.auth25-shell');
        if(!(await auth.count()))throw new Error('Premium account layout missing');
        const box=await auth.evaluate(el=>{
          const r=el.getBoundingClientRect();
          const left=el.querySelector('.auth25-showcase').getBoundingClientRect();
          const right=el.querySelector('.auth25-panel').getBoundingClientRect();
          return {x:r.x,w:r.width,right:r.right,viewport:innerWidth,leftRight:left.right,rightX:right.x,
            panelWidth:right.width,formWidth:el.querySelector('.auth25-form').getBoundingClientRect().width};
        });
        if(box.x < -2||box.right > box.viewport + 2||box.formWidth<245){
          errors.push('Account page overflows or has too narrow a form');failed=true;
        }
        if(screen.name==='masaustu'&&(box.rightX<box.leftRight-2||box.panelWidth<350)){
          errors.push('Account page two-column layout collapsed on desktop');failed=true;
        }
        if(p.route==='/giris'){
          const pw=page.locator('#password'),toggle=page.locator('[data-auth-toggle="password"]');
          await pw.fill('qa-visible');
          await toggle.click();
          if(await pw.getAttribute('type')!=='text'){errors.push('Password show button did not reveal password');failed=true;}
          await toggle.click();
          if(await pw.getAttribute('type')!=='password'){errors.push('Password hide button did not conceal password');failed=true;}
        }
        if(p.route==='/kayit'){
          if((await page.locator('#name').count())!==1||(await page.locator('#password_confirmation').count())!==1){
            errors.push('Registration fields missing');failed=true;
          }
        }
      }
      // Mega menu is a separate keyboard/touch module from the category tile directory.
      if((p.route==='/'||p.route==='/kategoriler') && ['masaustu','mobil'].includes(screen.name)){
        const triggers=page.locator('#navMain [data-mega-trigger]');
        if((await triggers.count())<2){
          errors.push('Social and agency mega menu entries missing');failed=true;
        }else if(screen.name==='masaustu'){
          await triggers.first().hover();
          const panel=page.locator('#navMain [data-mega-panel]:not([hidden])');
          if((await panel.count())!==1 || !(await panel.locator('a.nv26-mega-parent[href^="/kategori/"], a.nv26-social-mega-card[href^="/kategori/"]').count())){
            errors.push('Hover mega menu does not show real category links');failed=true;
          }
          await page.keyboard.press('Escape');
          if(await page.locator('#navMain [data-mega-panel]:not([hidden])').count()){
            errors.push('Mega menu Escape close failed');failed=true;
          }
        }else{
          const btn=page.locator('#mobileMenuBtn');
          await btn.click();
          if(!(await page.locator('#navMain').evaluate(el=>el.classList.contains('open')))){
            errors.push('Mobile navigation drawer did not open');failed=true;
          }
          await triggers.first().click();
          if(!(await page.locator('#navMain [data-mega-panel]:not([hidden])').count())){
            errors.push('Mobile group accordion did not expand');failed=true;
          }
          await btn.click();
        }
      }
      if(p.route==='/kategoriler' && ['masaustu','mobil'].includes(screen.name)){
        if(!(await page.locator('.nv26-catalog-group').count()) || !(await page.locator('.nv26-catalog-tile').count())){
          errors.push('Live colorful category grid is missing');failed=true;
        }
        if(!(await page.locator('.nv26-catalog-tile a[href^="/kategori/"]').count())){
          errors.push('Category tiles are not linked to catalog records');failed=true;
        }
        const dimensions=await page.locator('.nv26-catalog-tiles').first().evaluate(el=>({
          width:el.clientWidth,scroll:el.scrollWidth
        }));
        if(dimensions.scroll>dimensions.width+3){
          errors.push('Platform category tiles overflow their grid');failed=true;
        }
      }
      if(p.route==='/' && screen.name==='masaustu'){
        const invisibleQuickCards=await page.locator('.yh6-platform-bar a').evaluateAll(cards=>
          cards.filter(card=>{
            const style=getComputedStyle(card);
            const backgroundMissing=style.backgroundImage==='none' &&
              ['transparent','rgba(0, 0, 0, 0)'].includes(style.backgroundColor);
            return backgroundMissing;
          }).map(card=>card.className)
        );
        if(invisibleQuickCards.length){
          errors.push('Homepage quick service cards have no background: '+invisibleQuickCards.join(', '));failed=true;
        }
      }
      // Distinct premium agency / marketing dropdowns; social tiles are unchanged.
      if(p.route==='/' && (screen.name==='masaustu'||screen.name==='mobil')){
        if(screen.name==='mobil') await page.locator('#mobileMenuBtn').click();
        for(const group of ['agency','marketing']){
          const btn=page.locator('[aria-controls="nv26-panel-'+group+'"][data-mega-trigger]');
          if(!await btn.count()) continue; // An admin can deliberately hide a group.
          if(screen.name==='masaustu') await btn.hover();
          else await btn.click();
          const panel=page.locator('#nv26-panel-'+group);
          if(await panel.isHidden()){errors.push(group+' premium dropdown did not open');failed=true;continue;}
          const info=await panel.evaluate(root=>{
            const cards=[...root.querySelectorAll('.nv27-service-card')];
            const links=[...root.querySelectorAll('.nv27-subcategory-link')];
            const grid=root.querySelector('.nv27-service-grid');
            const cardBoxes=cards.map(el=>el.getBoundingClientRect());
            const duplicateCard=cardBoxes.some((rect,i)=>cardBoxes.slice(i+1).some(other=>
              Math.min(rect.right,other.right)-Math.max(rect.left,other.left)>4 &&
              Math.min(rect.bottom,other.bottom)-Math.max(rect.top,other.top)>4));
            return {cards:cards.length,childLinks:links.length,
              badHref:links.some(a=>{const href=a.getAttribute('href')||''; return !href.startsWith('/kategori/')||!href.includes('?alt=');}),
              overflow:!!grid && grid.scrollWidth>grid.clientWidth+3,
              overlap:duplicateCard,
              hasSpotlight:!!root.querySelector('.nv27-service-spotlight')};
          });
          if(!info.cards||!info.hasSpotlight||info.overflow||info.overlap||info.badHref){
            errors.push(group+' premium layout or category links invalid: '+JSON.stringify(info));failed=true;
          }
          if(screen.name==='masaustu'){
            await page.screenshot({path:path.join(output,'premium-menu-'+group+'-desktop.png'),animations:'disabled'});
          }
        }
        if(screen.name==='mobil') await page.locator('#mobileMenuBtn').click();
        else await page.keyboard.press('Escape');
      }
      // Regression checks for screenshot-confirmed layout failures.
      const g=report.geom||{};
      if(screen.w>=1180){
        for(const key of ['.yv-category-hero-art-v5','.yv-blog-hero-art-v8','.yv-product-hero-visual-v5']){
          if(g[key] && g[key].w<180){
            errors.push('Collapsed hero artwork: '+key+' width='+g[key].w);
            failed=true;
          }
        }
      }
      if(p.route==='/' && screen.w<=768 && g['.yh6-platform-bar'] && g['.yh6-why']){
        const stripBottom=g['.yh6-platform-bar'].y+g['.yh6-platform-bar'].h;
        if(stripBottom>g['.yh6-why'].y+3){
          errors.push('Homepage service strip overlaps next section by '+(stripBottom-g['.yh6-why'].y)+'px');
          failed=true;
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

// Verify the exact laptop/tablet widths where the customer saw the portrait
// covering benefit pills; normal homepage smoke viewports cannot catch this.
const authVisualContext = await browser.newContext({deviceScaleFactor:1});
const authVisualPage = await authVisualContext.newPage();
for (const screen of [{w:1150,h:766,name:'laptop-1150'},{w:1024,h:768,name:'laptop-1024'}]){
  await authVisualPage.setViewportSize({width:screen.w,height:screen.h});
  for (const route of ['/giris','/kayit']){
    try {
      const response = await authVisualPage.goto(origin+route,{waitUntil:'domcontentloaded'});
      await authVisualPage.evaluate(() => document.fonts?.ready);
      const result = await authVisualPage.evaluate(() => {
        const showcase = document.querySelector('.auth25-showcase');
        const artwork = showcase?.querySelector('.auth25-art');
        const visual = artwork?.getBoundingClientRect();
        const r = selector => showcase?.querySelector(selector)?.getBoundingClientRect();
        const features = [...(showcase?.querySelectorAll('.auth25-benefits>span') || [])];
        const targets = [
          ['headline',r('.auth25-showcase-main h2')],
          ['description',r('.auth25-showcase-main p')],
          ...features.map((el,i) => ['benefit-'+i,el.getBoundingClientRect()]),
          ['trust-card',r('.auth25-float')],
        ];
        const overlaps = (visual && getComputedStyle(artwork).display!=='none')
          ? targets.filter(([name, box]) => {
              if(!box)return false;
              return Math.min(visual.right,box.right)-Math.max(visual.left,box.left)>4 &&
                     Math.min(visual.bottom,box.bottom)-Math.max(visual.top,box.top)>4;
            }).map(([name])=>name):[];
        const panel = document.querySelector('.auth25-panel')?.getBoundingClientRect();
        const left = showcase?.getBoundingClientRect();
        const portrait = showcase?.querySelector('.auth25-art img');
        return {overlaps,portraitLoaded:!!portrait?.naturalWidth,portraitVisible:!!visual,
          panelAligned:!!panel&&!!left&&panel.left>=left.right-2,
          horizontalOverflow:document.documentElement.scrollWidth>innerWidth+2};
      });
      if(response.status()>=400 || result.overlaps.length || !result.portraitLoaded ||
         !result.panelAligned || result.horizontalOverflow){
        throw new Error('Auth layout defect '+JSON.stringify({status:response.status(),...result}));
      }
      await authVisualPage.screenshot({
        path:path.join(output,'auth-'+screen.name+'-'+(route==='/giris'?'login':'register')+'.png'),
        fullPage:true,animations:'disabled'
      });
      results.push({route,screen:screen.name,status:response.status(),errors:[]});
      console.log('PASS auth visual composition:',screen.name,route,'portrait clear of copy and badges');
    } catch(e){
      failed=true;
      results.push({route,screen:screen.name,status:0,errors:[String(e)]});
      console.error('FAIL auth visual composition:',screen.name,route,String(e).slice(0,390));
    }
  }
}
await authVisualContext.close();

// Exercise real form submissions against isolated CI MySQL (never production).
const formsContext=await browser.newContext({viewport:{width:1440,height:900}});
const formsPage=await formsContext.newPage();
for(const test of ['contact','newsletter']){
  try{
    if(test==='contact'){
      await formsPage.goto(origin+'/iletisim',{waitUntil:'domcontentloaded'});
      const form=formsPage.locator('.yv-contact-form form');
      await form.locator('[name=name]').fill('Test Kullanıcısı');
      await form.locator('[name=email]').fill('ci-kontrol@example.test');
      await form.locator('[name=subject]').fill('Otomatik iletişim formu testi');
      await form.locator('[name=message]').fill('Bu mesaj CI otomatik testlerinde kayıt işleminin gerçekten çalıştığını doğrular.');
      await form.locator('[name=privacy_consent]').check();
      await Promise.all([formsPage.waitForURL('**/iletisim',{waitUntil:'domcontentloaded'}),form.locator('button[type=submit]').click()]);
      const ok=await formsPage.locator('.yv-form-feedback.success').filter({hasText:'kaydedildi'}).count()>0;
      if(!ok)throw new Error('Contact message was not persisted or success confirmation missing');
    }else{
      await formsPage.goto(origin+'/',{waitUntil:'domcontentloaded'});
      const form=formsPage.locator('.footer-newsletter-signup');
      await form.locator('[name=email]').fill('ci-bulten@example.test');
      await form.locator('[name=newsletter_consent]').check();
      await Promise.all([formsPage.waitForURL('**/#newsletter',{waitUntil:'domcontentloaded'}),form.locator('button[type=submit]').click()]);
      const ok=await formsPage.locator('.footer-subscribe-v9 .yv-form-feedback.success').count()>0;
      if(!ok)throw new Error('Newsletter was not stored or success confirmation missing');
    }
    results.push({route:'POST '+test,screen:'forms',status:200,errors:[]});
    console.log('PASS functional form:',test);
  }catch(e){
    failed=true;results.push({route:'POST '+test,screen:'forms',status:0,errors:[String(e)]});
    console.error('FAIL functional form:',test,String(e).slice(0,320));
  }
}
await formsContext.close();

// Validate the real admin-to-front-end menu connection against the isolated CI database.
const adminContext = await browser.newContext({viewport:{width:1440,height:900}});
const adminPage = await adminContext.newPage();
try {
  await adminPage.goto(origin + '/admin/giris', {waitUntil:'domcontentloaded'});
  await adminPage.locator('input[name=email]').fill('admin@yorumhizmeti.tr');
  await adminPage.locator('input[name=password]').fill('qa-test-menu-only');
  await Promise.all([
    adminPage.waitForURL('**/admin', {waitUntil:'domcontentloaded'}),
    adminPage.locator('form button[type=submit]').click()
  ]);
  await adminPage.goto(origin + '/admin/menu', {waitUntil:'domcontentloaded'});
  if ((await adminPage.locator('.adm-nav-item').count()) < 3) throw new Error('Menu management items not rendered');
  const toggle = adminPage.locator('input[name="enabled[]"][value="blog"]');
  await toggle.evaluate(input => { input.checked = false; input.dispatchEvent(new Event('change',{bubbles:true})); });
  await Promise.all([
    adminPage.waitForURL('**/admin/menu', {waitUntil:'domcontentloaded'}),
    adminPage.locator('#navMenuEditor button[type=submit]').click()
  ]);
  await adminPage.goto(origin + '/', {waitUntil:'domcontentloaded'});
  if (await adminPage.locator('.nv26-menu-shell .nv26-simple-link[href="/blog"]').count()) throw new Error('Hidden admin menu item still visible in header');

  await adminPage.goto(origin + '/admin/menu', {waitUntil:'domcontentloaded'});
  await adminPage.locator('input[name="enabled[]"][value="blog"]').evaluate(input => {
    input.checked = true; input.dispatchEvent(new Event('change',{bubbles:true}));
  });
  await Promise.all([
    adminPage.waitForURL('**/admin/menu', {waitUntil:'domcontentloaded'}),
    adminPage.locator('#navMenuEditor button[type=submit]').click()
  ]);
  await adminPage.goto(origin + '/', {waitUntil:'domcontentloaded'});
  if (!(await adminPage.locator('.nv26-menu-shell .nv26-simple-link[href="/blog"]').count())) throw new Error('Restored menu item did not return to storefront');
  results.push({route:'Admin menu hide/restore',screen:'integration',status:200,errors:[]});
  console.log('PASS admin menu hide/restore');
} catch(e) {
  failed=true;
  results.push({route:'Admin menu hide/restore',screen:'integration',status:0,errors:[String(e)]});
  console.error('FAIL admin menu hide/restore:',String(e).slice(0,350));
} finally { await adminContext.close(); }

// Regression: the admin catalog must use one valid bulk form on desktop and mobile.
const catalogContext = await browser.newContext({viewport:{width:1440,height:900}});
const catalogPage = await catalogContext.newPage();
catalogPage.on('dialog', dialog => dialog.accept());
try {
  await catalogPage.goto(origin + '/admin/giris', {waitUntil:'domcontentloaded'});
  await catalogPage.locator('input[name=email]').fill('admin@yorumhizmeti.tr');
  await catalogPage.locator('input[name=password]').fill('qa-test-menu-only');
  await Promise.all([
    catalogPage.waitForURL('**/admin', {waitUntil:'domcontentloaded'}),
    catalogPage.locator('form button[type=submit]').click()
  ]);
  await catalogPage.goto(origin + '/admin/paketler', {waitUntil:'domcontentloaded'});
  if ((await catalogPage.locator('#bulkPkgForm .adm-pkg-desktop').count()) !== 1 ||
      (await catalogPage.locator('#bulkPkgForm .adm-pkg-mobile').count()) !== 1) {
    throw new Error('Desktop and mobile catalog are not inside the same valid bulk form');
  }
  if ((await catalogPage.locator('#bulkPkgForm button[formaction*="/sil"]').count()) < 2) {
    throw new Error('Package delete buttons are not real POST form overrides');
  }
  const firstPkg = catalogPage.locator('.adm-pkg-desktop .pkg-checkbox').first();
  const packageId = await firstPkg.getAttribute('value');
  if (!packageId) throw new Error('No catalog package to test');
  await firstPkg.check();
  if ((await catalogPage.locator('#selectedPkgCount').innerText()).trim() !== '1') {
    throw new Error('One package selected was counted more than once');
  }
  if (!(await catalogPage.locator('.adm-pkg-mobile .pkg-checkbox[value="' + packageId + '"]').isChecked())) {
    throw new Error('Desktop package selection did not synchronize with mobile');
  }
  await catalogPage.locator('#bulkPkgActionSelect').selectOption('unfeatured');
  await Promise.all([
    catalogPage.waitForNavigation({waitUntil:'domcontentloaded'}),
    catalogPage.locator('#bulkPkgActions button[type=submit]').click()
  ]);
  const row = () => catalogPage.locator('.adm-pkg-desktop .pkg-checkbox[value="' + packageId + '"]').locator('xpath=ancestor::tr');
  if (await row().locator('.adm-featured-badge').count()) {
    throw new Error('Bulk unfeature did not update saved catalog state');
  }
  await catalogPage.locator('.adm-pkg-desktop .pkg-checkbox[value="' + packageId + '"]').check();
  await catalogPage.locator('#bulkPkgActionSelect').selectOption('featured');
  await Promise.all([
    catalogPage.waitForNavigation({waitUntil:'domcontentloaded'}),
    catalogPage.locator('#bulkPkgActions button[type=submit]').click()
  ]);
  if (!(await row().locator('.adm-featured-badge').count())) {
    throw new Error('Bulk feature did not restore featured package');
  }
  await catalogPage.goto(origin + '/admin/kategoriler', {waitUntil:'domcontentloaded'});
  if ((await catalogPage.locator('#bulkCatForm .adm-cat-desktop').count()) !== 1 ||
      (await catalogPage.locator('#bulkCatForm .adm-cat-mobile').count()) !== 1) {
    throw new Error('Category editor has broken/nested forms');
  }
  const firstCat = catalogPage.locator('.adm-cat-desktop .cat-checkbox').first();
  const catId = await firstCat.getAttribute('value');
  if (!catId) throw new Error('No category to test');
  await firstCat.check();
  if ((await catalogPage.locator('#selectedCatCount').innerText()).trim() !== '1' ||
      !(await catalogPage.locator('.adm-cat-mobile .cat-checkbox[value="' + catId + '"]').isChecked())) {
    throw new Error('Category selection double counted or not synchronized');
  }
  results.push({route:'Admin catalog/package/category bridge',screen:'integration',status:200,errors:[]});
  console.log('PASS valid admin bulk forms, featured package toggles and responsive selections');
} catch(e) {
  failed=true;
  results.push({route:'Admin catalog/package/category bridge',screen:'integration',status:0,errors:[String(e)]});
  console.error('FAIL admin catalog integration:',String(e).slice(0,400));
} finally { await catalogContext.close(); }

// End-to-end checkout guard: registering, selecting a live package and rejecting a forged gateway.
const checkoutContext = await browser.newContext({viewport:{width:1440,height:900}});
const shopper = await checkoutContext.newPage();
try {
  await shopper.goto(origin + '/kayit', {waitUntil:'domcontentloaded'});
  const uniqueEmail = 'qa-catalog-' + Date.now() + '@example.test';
  await shopper.locator('input[name=name]').fill('Katalog Test Kullanıcısı');
  await shopper.locator('input[name=email]').fill(uniqueEmail);
  await shopper.locator('input[name=password]').fill('qa-checkout-only');
  await shopper.locator('input[name=password_confirmation]').fill('qa-checkout-only');
  await Promise.all([
    shopper.waitForURL('**/hesabim', {waitUntil:'domcontentloaded'}),
    shopper.locator('form button[type=submit]').click()
  ]);
  await shopper.goto(origin + '/paket/google-harita-yorum-toplama-baslangic-paketi-10-davet', {waitUntil:'domcontentloaded'});
  if (!(await shopper.locator('#packageForm').count())) throw new Error('Live product purchase form missing');
  await shopper.locator('#packageForm').evaluate(async form => {
    const response = await fetch(form.action, {method:'POST', body:new FormData(form)});
    if (!response.ok) throw new Error('Cart add POST returned ' + response.status);
  });
  await shopper.goto(origin + '/sepet', {waitUntil:'domcontentloaded'});
  if (!(await shopper.locator('.yv-cart-row-v5').count())) throw new Error('Selected live package did not reach the cart');
  await shopper.goto(origin + '/odeme', {waitUntil:'domcontentloaded'});
  const checkout = shopper.locator('#checkoutForm');
  if (!(await checkout.count())) throw new Error('Cart did not reach checkout');
  const token = await checkout.locator('input[name="_csrf_token"]').inputValue();
  const invalid = await checkoutContext.request.post(origin + '/odeme/islem', {
    form: {_csrf_token:token, payment_gateway:'unknown_gateway_qa', terms_accepted:'1'},
    maxRedirects:0
  });
  const destination = invalid.headers()['location'] || '';
  if (invalid.status() !== 302 || !destination.endsWith('/odeme')) {
    throw new Error('Forged payment method was not rejected before creating an order: ' + invalid.status() + ' ' + destination);
  }
  results.push({route:'Storefront cart and checkout payment guard',screen:'integration',status:200,errors:[]});
  console.log('PASS customer registration, package-to-cart route and invalid payment rejection');
} catch (e) {
  failed=true;
  results.push({route:'Storefront cart and checkout payment guard',screen:'integration',status:0,errors:[String(e)]});
  console.error('FAIL checkout integration:',String(e).slice(0,400));
} finally { await checkoutContext.close(); }

// Validate the visible mobile package slider (not just presence of HTML controls).
const sliderContext=await browser.newContext({viewport:{width:390,height:844}});
const sliderPage=await sliderContext.newPage();
try {
  await sliderPage.goto(origin+'/',{waitUntil:'domcontentloaded'});
  const track=sliderPage.locator('[data-featured-pane]:not([hidden]) [data-featured-track]');
  const count=await track.locator('.yh18-featured-card').count();
  if(count<2)throw new Error('CI featured seed did not create a multi-card carousel');
  await track.scrollIntoViewIfNeeded();
  const initial=await track.evaluate(el=>({left:el.scrollLeft, max:el.scrollWidth-el.clientWidth}));
  if(initial.max<15)throw new Error('Multi-card carousel has no horizontal overflow on mobile');
  await sliderPage.locator('[data-featured-pane]:not([hidden]) [data-slide-next]').click();
  await sliderPage.waitForTimeout(450);
  const moved=await track.evaluate(el=>el.scrollLeft);
  if(moved<10)throw new Error('Carousel next button did not scroll the cards');
  results.push({route:'Featured package carousel',screen:'mobil',status:200,errors:[]});
  console.log('PASS featured package slider, card count:',count,'scrolled:',Math.round(moved));
} catch(e) {
  failed=true;
  results.push({route:'Featured package carousel',screen:'mobil',status:0,errors:[String(e)]});
  console.error('FAIL featured carousel:',String(e).slice(0,350));
} finally{await sliderContext.close();}

fs.writeFileSync(path.join(output,'report.json'),JSON.stringify({created:new Date().toISOString(),runs:results},null,2));
const bad=results.filter(x=>x.status>=500||x.status===0||x.report?.rootScroll>x.report?.viewport+3||x.errors?.some(e=>e.includes('did not switch')));
console.log(`\nREPORT: ${results.length} page/viewport combinations; critical failures: ${bad.length}; images and overflow details saved to report.json`);
await browser.close();
if(failed) process.exitCode=1;
