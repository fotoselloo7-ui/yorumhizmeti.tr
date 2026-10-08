import { chromium } from 'playwright';
import fs from 'node:fs';
import path from 'node:path';

const origin = process.env.QA_ORIGIN || 'http://127.0.0.1:8006';
const output = process.env.QA_OUTPUT || 'qa-artifacts';
const runs = [
  { route:'/',slug:'anasayfa' },
  { route:'/kategoriler',slug:'tum-hizmetler' },
  { route:'/hazir-yazilimlar',slug:'hazir-yazilimlar' },
  { route:'/hazir-scriptler',slug:'netvera-yazilimlar' },
  { route:'/hazir-scriptler/haber-sitesi-scripti',slug:'netvera-haber-detay' },
  { route:'/kategori/instagram-hizmetleri',slug:'instagram' },
  { route:'/kategori/web-site-hizmetleri',slug:'web-site' },
  { route:'/paket/google-harita-yorum-toplama-baslangic-paketi-10-davet',slug:'paket-detay' },
  { route:'/blog',slug:'blog' },
  { route:'/blog/yapay-zeka-haber-yazilimi-otomatik-haber-sitesi',slug:'netvera-blog-markdown' },
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
    if ((screen.name==='tablet'||screen.name==='genis') && !['anasayfa','tum-hizmetler','hazir-yazilimlar','instagram','paket-detay','blog'].includes(p.slug)) continue;
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
      // NetVera staged heritage storefront: guard real SQL facets and mobile first fold.
      if (p.route === '/hazir-scriptler' && ['mobil','masaustu'].includes(screen.name)) {
        const catalogueCount = await page.locator('.nv40-product-card').count();
        if (catalogueCount !== 3) throw new Error('Netvera QA products missing: '+catalogueCount);
        const drawer = page.locator('.nv40-filter-disclosure');
        if (screen.name === 'mobil') {
          if (await drawer.getAttribute('open') !== null) {
            errors.push('Netvera mobile sidebar did not collapse'); failed = true;
          }
          await drawer.locator('summary').click();
          if (await drawer.getAttribute('open') === null)
            throw new Error('Netvera mobile filter disclosure cannot open');
          await drawer.locator('summary').click();
        } else {
          const sidebar = await page.locator('.nv40-categories').boundingBox();
          const grid = await page.locator('.nv40-product-grid').boundingBox();
          if (!sidebar || !grid || sidebar.x + sidebar.width > grid.x + 12)
            throw new Error('Netvera desktop catalogue not beside compact sidebar');
        }
        const filtered = await ctx.newPage();
        try {
          await filtered.goto(origin+'/hazir-scriptler?min_price=3500&sort=price_asc',{waitUntil:'domcontentloaded'});
          const cards = await filtered.locator('.nv40-product-card h3').allTextContents();
          if (cards.length !== 2 || !cards[0].includes('Emlak') || !cards[1].includes('Haber'))
            throw new Error('Netvera price SQL filter or numeric sort incorrect: '+cards.join('|'));
          await filtered.goto(origin+'/hazir-scriptler?min_rating=5',{waitUntil:'domcontentloaded'});
          const rated = await filtered.locator('.nv40-product-card h3').allTextContents();
          if (rated.length !== 1 || !rated[0].includes('Haber'))
            throw new Error('Netvera approved review filter incorrect: '+rated.join('|'));
        } finally {
          await filtered.close();
        }
      }
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
      // Regression: "Ajans & Yazılım" must expose the REAL Netvera scripts
      // as selectable panels, not just old service-package categories.
      if(p.route==='/' && ['mobil','masaustu'].includes(screen.name)){
        const agency = page.locator('[data-featured-group="agency"]');
        if(!(await agency.count()))throw new Error('Agency featured switcher group missing');
        await agency.click();
        const filters = page.locator('[data-featured-filter-panel="agency"]');
        const softwareTabs = filters.locator('[data-featured-tab][data-kind="software"]');
        const scriptTabCount = await softwareTabs.count();
        // CI fixture has two actual script categories plus the catalogue root;
        // an imported source DB will have three plus the root.
        if(scriptTabCount < 3) throw new Error('Netvera software and original categories missing from featured section: '+scriptTabCount);
        const rootSoftwareTab = softwareTabs.first();
        const rootId = await rootSoftwareTab.getAttribute('data-featured-tab');
        if((await rootSoftwareTab.getAttribute('data-url'))!=='/hazir-scriptler')
          throw new Error('Netvera tab missing original catalogue route');
        await rootSoftwareTab.click();
        const softwarePane = page.locator('[data-featured-pane="'+rootId+'"]:not([hidden])');
        if(!(await softwarePane.count()))throw new Error('Netvera featured software tab did not activate its real pane');
        if(!(await softwarePane.locator('a[href="/hazir-scriptler/haber-sitesi-scripti"]').count()))
          throw new Error('Original indexed software product is missing from featured cards');
        if(!(await page.locator('[data-featured-kind]').textContent())?.includes('Yazılımlar'))
          throw new Error('Software tab kept the package-only heading');
        if((await softwarePane.locator('[data-netvera-software-card]').count())<3)
          throw new Error('Real software cards did not render from CI product records');
        // All categories are selectable, even if one has no active products.
        const lastSoftwareTab = softwareTabs.last();
        const lastId=await lastSoftwareTab.getAttribute('data-featured-tab');
        await lastSoftwareTab.click();
        if(!(await page.locator('[data-featured-pane="'+lastId+'"]:not([hidden])').count()))
          throw new Error('Last original Netvera software category cannot be selected');
        const featuredOverflow=await page.locator('.yh24-featured-subfilters').evaluate(el=>el.scrollWidth>el.clientWidth+4);
        if(featuredOverflow) throw new Error('Netvera featured filters overflow '+screen.name);
        // Return to existing original service packages: software tabs must not
        // overwrite package cards, headings or links.
        const packageTab=filters.locator('[data-featured-tab][data-kind="package"]').first();
        await packageTab.click();
        if(!await page.locator('[data-featured-kind]').textContent().then(text=>text.includes('Paketler')))
          throw new Error('Switching back from Netvera software to packages failed');
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
      // New featured taxonomy follows mega-menu parent->child hierarchy.
      if(p.route==='/' && ['mobil','masaustu'].includes(screen.name)){
        const categoryPanel=page.locator('[data-featured-filter-panel="agency"]');
        const rootRail=categoryPanel.locator('.nv43-root-rail');
        const categoryRoots=await rootRail.locator('[data-featured-tab]').count();
        if(categoryRoots<2)throw new Error('Real service/software roots missing from featured rail');
        const boxes=await rootRail.locator('.nv43-category-card').evaluateAll(tiles=>
          tiles.map(el=>({y:Math.round(el.getBoundingClientRect().top),
                         x:Math.round(el.getBoundingClientRect().left)})));
        if(boxes.length>1 && boxes.some(box=>Math.abs(box.y-boxes[0].y)>3))
          throw new Error('Main featured category tiles wrapped into second row');
        const collapsed=await categoryPanel.locator('[data-featured-children-for="-100000"]').getAttribute('hidden');
        if(collapsed===null)throw new Error('Software subcategories shown before selecting software root');
        await page.locator('[data-featured-group="agency"]').click();
        await categoryPanel.locator('.nv43-root-rail [data-featured-tab][data-kind="software"]').click();
        const subMenu=categoryPanel.locator('[data-featured-children-for="-100000"]');
        if(await subMenu.getAttribute('hidden')!==null)
          throw new Error('Software menu nested child rail did not open');
        const softwareChildren=await subMenu.locator('.nv43-subcategory-tab').count();
        const softwareTypes=await subMenu.locator('a[href^="/hazir-yazilimlar?tur="]').count();
        if(softwareChildren<2||softwareTypes<30)
          throw new Error('Original subcategories or ready software menu items omitted: '+softwareChildren+' / '+softwareTypes);
        const panels=await page.locator('[data-featured-pane="-100000"]').count();
        if(panels!==1)throw new Error('Legacy software panes still duplicated inside package loop: '+panels);
        const rootStats=await rootRail.evaluate(el=>({
          wrap:getComputedStyle(el).flexWrap,
          scrollWidth:el.scrollWidth,viewport:el.clientWidth
        }));
        if(rootStats.wrap!=='nowrap')
          throw new Error('Main category rail unexpectedly wraps');
        const childRail=subMenu.locator('.nv43-child-rail');
        const childStats=await childRail.evaluate(el=>({
          width:el.scrollWidth,viewport:el.clientWidth,
          display:getComputedStyle(el).display,
          parentDisplay:getComputedStyle(el.parentElement).display,
          subMenuHidden:!!el.closest('[hidden]'),
          childCount:el.children.length
        }));
        if(childStats.width<=childStats.viewport+50)
          throw new Error('Nested software menu not scrollable: '+JSON.stringify(childStats));
        if(screen.name==='masaustu'){
          await childRail.evaluate(el=>{el.scrollLeft=0;});
          const rect=await childRail.boundingBox();
          await page.mouse.move(rect.x+rect.width*.73,rect.y+rect.height*.6);
          await page.mouse.down();
          await page.mouse.move(rect.x+rect.width*.31,rect.y+rect.height*.6,{steps:9});
          await page.mouse.up();
          const dragged=await childRail.evaluate(el=>el.scrollLeft);
          if(dragged<20)throw new Error('Mouse grab-and-drag did not move the nested software rail: '+dragged);
        }else{
          const next=categoryPanel.locator('.nv43-rail-shell').first().locator('[data-featured-rail-next]');
          await rootRail.evaluate(el=>{el.scrollLeft=0;});
          if(await next.isDisabled())throw new Error('Mobile horizontal featured navigation disabled');
          await next.click();
          await page.waitForTimeout(350);
          if((await rootRail.evaluate(el=>el.scrollLeft))<8)
            throw new Error('Mobile featured category carousel next arrow failed');
        }
      }
      if(p.slug==='netvera-blog-markdown'){
        const article=page.locator('.nv42-article-body');
        const headings=await article.locator('h2,h3').count();
        const figures=await article.locator('figure.nv-blog-figure img').count();
        if(headings<7||figures<1)
          throw new Error('Real Netvera Markdown article failed heading/image conversion: '+headings+'/'+figures);
        const raw=await article.innerText();
        if(raw.includes('## Yapay zeka haber yazılımı')||raw.includes('**Yapay zeka haber'))
          throw new Error('Markdown syntax still printed as raw blog text');
        if(!(await article.locator('blockquote').count()))
          throw new Error('Markdown quotation was not rendered');
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
              badHref:links.some(a=>{
                const href=a.getAttribute('href')||'';
                // Agency's ready-scripts tile routes to a dedicated live catalog;
                // legacy service cards still use their original ?alt= links.
                return !(href.startsWith('/kategori/')&&href.includes('?alt='))
                  && !(href.startsWith('/hazir-scriptler?category=')&&href.split('=')[1])
                  && href!=='/hazir-yazilimlar';
              }),
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
      // V28 regression: submenu glyphs must exist and be centered inside
      // their padded backgrounds, including the known-broken north-east arrow.
      if(p.route==='/' && (screen.name==='masaustu'||screen.name==='mobil')){
        if(screen.name==='mobil') await page.locator('#mobileMenuBtn').click();
        for(const group of ['agency','marketing']){
          const btn=page.locator('[data-mega-trigger][aria-controls="nv26-panel-'+group+'"]');
          if(!await btn.count()) continue;
          if(screen.name==='masaustu') await btn.hover();
          else await btn.click();
          const panel=page.locator('#nv26-panel-'+group);
          const state=await panel.evaluate(root=>{
            const centered=(wrapper,glyph)=>{
              const b=wrapper.getBoundingClientRect(),g=glyph.getBoundingClientRect();
              const dx=Math.abs((b.left+b.right)/2-(g.left+g.right)/2);
              const dy=Math.abs((b.top+b.bottom)/2-(g.top+g.bottom)/2);
              return dx<3.5 && dy<3.5 && g.width>4 && g.height>4;
            };
            const targets=[...root.querySelectorAll('.nv27-service-icon,.nv27-service-parent-arrow,.nv27-subcategory-symbol')];
            const blanks=[];
            for(const item of targets){
              const glyph=item.querySelector('.icon');
              if(!glyph){blanks.push('missing '+item.className);continue;}
              const isSvg=glyph.tagName.toLowerCase()==='svg';
              const pseudo=isSvg?'':getComputedStyle(glyph,'::before').content;
              if(!isSvg && (!pseudo || pseudo==='none'||pseudo==='normal')){
                blanks.push('unresolved '+glyph.className);
              }
              if(!centered(item,glyph))blanks.push('off-center '+item.className);
            }
            const sub=[...root.querySelectorAll('.nv27-subcategory-symbol .icon')];
            return {total:targets.length,subIcons:sub.length,blanks:blanks.slice(0,12)};
          });
          if(state.total===0||state.subIcons===0||state.blanks.length){
            errors.push(group+' missing or misaligned icons: '+JSON.stringify(state));failed=true;
          }
        }
        if(screen.name==='mobil') await page.locator('#mobileMenuBtn').click();
        else await page.keyboard.press('Escape');
      }
      // V29: all three headline service families, not Instagram/TikTok/Web Site.
      if(p.route==='/' && ['mobil','tablet','masaustu','genis'].includes(screen.name)){
        const promos=page.locator('.nv29-service-card');
        const keys=await promos.evaluateAll(cards=>cards.map(el=>el.getAttribute('data-promo-group')));
        if(JSON.stringify(keys)!==JSON.stringify(['social','agency','marketing'])){
          errors.push('Three main service groups missing or out of order: '+keys.join(','));failed=true;
        }
        const state=await page.locator('.nv29-services').evaluate(section=>{
          const all=[...section.querySelectorAll('.nv29-service-card')];
          const boxes=all.map(el=>el.getBoundingClientRect());
          const onePerRow=innerWidth<=700;
          const collisions=boxes.some((b,i)=>boxes.slice(i+1).some(other=>
            Math.min(b.right,other.right)-Math.max(b.left,other.left)>5 &&
            Math.min(b.bottom,other.bottom)-Math.max(b.top,other.top)>5));
          return {
            collisions,onePerRow,overflow:section.scrollWidth>section.clientWidth+3,
            cards:all.map(card=>{
              const key=card.getAttribute('data-promo-group');
              const cta=card.querySelector('.nv29-card-cta');
              const chips=[...card.querySelectorAll('.nv29-service-chip')];
              const art=card.querySelector('.nv29-visual');
              const area=card.getBoundingClientRect();
              const artwork=art?.getBoundingClientRect();
              return {
                key,
                title:card.querySelector('h3')?.textContent?.trim(),
                validCta:cta?.getAttribute('href')==='/kategoriler?grup='+key,
                badChips:chips.filter(a=>!a.getAttribute('href')?.startsWith('/kategori/')).length,
                tooManyChips:chips.length>4,
                artVisible:!!artwork && artwork.width>35 && artwork.height>65,
                narrow:area.width<270,
                // Intentional clipped decoration may enlarge scrollWidth.
                // Check the actual content and CTA against the card and chip row.
                contentOverflow:(()=>{
                  const content=card.querySelector('.nv29-card-content')?.getBoundingClientRect();
                  const footer=card.querySelector('.nv29-service-chips')?.getBoundingClientRect();
                  if(!content||!footer)return true;
                  return content.left<area.left-2 || content.right>area.right+2 ||
                    content.bottom>footer.top+4 || footer.bottom>area.bottom+3;
                })(),
              };
            }),
          };
        });
        if(state.collisions||state.overflow||state.cards.some(x=>!x.validCta||x.badChips||x.tooManyChips||!x.artVisible||x.narrow||x.contentOverflow)){
          errors.push('Premium service cards responsive/data defect: '+JSON.stringify(state));failed=true;
        }
        if(screen.name==='masaustu'){
          const image=page.locator('.nv29-social-person');
          if(!(await image.evaluate(el=>el.complete&&el.naturalWidth>0))){
            errors.push('Social media promo model image did not load');failed=true;
          }
          await page.locator('.nv29-services').screenshot({path:path.join(output,'home-three-service-groups.png'),animations:'disabled'});
        }
      }
      // V34: the public ready-software category is a working page on clean
      // installs, and the agency menu always exposes its own fixed entry.
      if(p.route==='/hazir-yazilimlar' && ['mobil','masaustu'].includes(screen.name)){
        if(!(await page.locator('.nv36-marketplace').count()) ||
           !(await page.locator('.nv36-sidebar').count()) ||
           !(await page.locator('.nv36-results-bar').count()) ||
           !(await page.locator('#nv33-products').count()) ||
           (await page.locator('.nv36-category-choice').count())!==35){
          errors.push('Sidebar marketplace or 34 category types missing');failed=true;
        }
        const link=page.locator('.nv36-category-choice[href*="tur=haber-sitesi-scripti"]');
        if(!await link.count())errors.push('Script category filter link missing');
        const geo=await page.evaluate(()=>{
          const sidebar=document.querySelector('.nv36-sidebar')?.getBoundingClientRect();
          const products=document.querySelector('.nv36-results')?.getBoundingClientRect();
          const hero=document.querySelector('.nv36-hero')?.getBoundingClientRect();
          return {sidebarX:sidebar?.left,sideRight:sidebar?.right,productX:products?.left,productY:products?.top,
            heroEnd:hero?.bottom,overflow:document.documentElement.scrollWidth>innerWidth+3};
        });
        if(geo.overflow||!(geo.productY<520) ||
           (screen.name==='masaustu' && !(geo.productX>geo.sideRight))){
          errors.push('Marketplace products below oversized filters or overflow: '+JSON.stringify(geo));failed=true;
        }
        if(screen.name==='mobil'){
          const toggle=page.locator('[data-software-filter-toggle]');
          if(await toggle.getAttribute('aria-expanded')!=='false'){
            errors.push('Mobile filters should be collapsed initially');failed=true;
          }
          await toggle.click();
          if(await toggle.getAttribute('aria-expanded')!=='true'){
            errors.push('Mobile filters did not expand');failed=true;
          }
        }
        await page.locator('.nv36-marketplace').screenshot({
          path:path.join(output,'software-marketplace-v36-'+screen.name+'.png'),
          animations:'disabled'
        });
      }
      if(p.route==='/' && ['mobil','masaustu'].includes(screen.name)){
        if(screen.name==='mobil') await page.locator('#mobileMenuBtn').click();
        const agency=page.locator('[data-mega-trigger][aria-controls="nv26-panel-agency"]');
        if(await agency.count()){
          if(screen.name==='mobil') await agency.click();
          else await agency.hover();
          const software=page.locator('#nv26-panel-agency .nv35-software-card');
          if((await software.count())!==1 ||
             !(await software.locator('.nv27-service-parent[href="/hazir-scriptler"]').count()) ||
             (await software.locator('.nv27-subcategory-link[href^="/hazir-scriptler?category="]').count()) < 2 ||
             !(await software.locator('.nv27-subcategory-link[href="/hazir-yazilimlar"]').count()) ||
             !(await software.locator('.nv27-category-footer[href="/hazir-scriptler"]').count())){
            errors.push('Real Netvera scripts are not the sixth agency category, or service-page link was lost');failed=true;
          }
          if(await page.locator('#nv26-panel-agency .nv33-software-feature').count()){
            errors.push('Obsolete wide software banner still occupies the menu top');failed=true;
          }
          if(screen.name==='masaustu'){
            const measured=await page.locator('#nv26-panel-agency').evaluate(panel=>{
              const grid=panel.querySelector('.nv27-service-grid');
              const cards=[...grid?.querySelectorAll(':scope > .nv27-service-card')||[]];
              const last=cards[cards.length-1]?.getBoundingClientRect();
              const previous=cards[cards.length-2]?.getBoundingClientRect();
              const firstRowThird=cards[2]?.getBoundingClientRect();
              const columns=getComputedStyle(grid).gridTemplateColumns.split(' ').length;
              return {
                cards:cards.length,columns,
                lastChildIsSoftware:cards[cards.length-1]?.matches('.nv35-software-card'),
                sharesLastRow:!!last&&!!previous&&Math.abs(last.top-previous.top)<4,
                alignsThirdColumn:!!last&&!!firstRowThird&&Math.abs(last.left-firstRowThird.left)<4,
                clipped:panel.scrollHeight>panel.clientHeight+6,
                viewportRight:last?.right,
              };
            });
            if(measured.cards!==6||measured.columns!==3||
               !measured.lastChildIsSoftware||!measured.sharesLastRow||
               !measured.alignsThirdColumn||measured.clipped){
              errors.push('Agency grid/card positioning defect: '+JSON.stringify(measured));failed=true;
            }
            await page.locator('#nv26-panel-agency').screenshot({
              path:path.join(output,'agency-software-as-sixth-card-v35.png'),
              animations:'disabled'
            });
          }
          if(screen.name==='masaustu') await page.keyboard.press('Escape');
        }
        if(screen.name==='mobil') await page.locator('#mobileMenuBtn').click();
      }
      // V32: service and portfolio sections exist even on a new, empty DB.
      if(p.route==='/' && ['mobil','tablet','masaustu','genis'].includes(screen.name)){
        const ready=await page.evaluate(()=>{
          const software=document.querySelector('#hazir-yazilimlar');
          const refs=document.querySelector('#referanslarimiz');
          const containerFit=root=>{
            const r=root.getBoundingClientRect();
            return r.width>260 && r.left>=-3 && r.right<=innerWidth+3;
          };
          return {
            softwarePresent:!!software,
            referencesPresent:!!refs,
            emptySoftware:!!software?.querySelector('.nv32-software-intro'),
            emptyReferences:!!refs?.querySelector('.nv32-portfolio-intro'),
            softwareProducts:software?.querySelectorAll('.nv31-software-card').length||0,
            references:refs?.querySelectorAll('.nv31-portfolio-card').length||0,
            badLinks:[...document.querySelectorAll('.nv32-software-intro a,.nv32-portfolio-intro a')]
              .filter(el=>!el.getAttribute('href') || el.getAttribute('href')==='#' ||
                (el.getAttribute('href')||'').includes('/kategori/hazir-yazilim-scriptleri') &&
                !el.closest('.nv32-service-list') && !document.querySelector('.nv32-service-list a')).length,
            aligned:software&&refs&&containerFit(software)&&containerFit(refs),
            overflow:[software,refs].filter(Boolean).some(el=>el.scrollWidth>el.clientWidth+6),
          };
        });
        if(!ready.softwarePresent||!ready.referencesPresent||!ready.aligned||
          ready.overflow||ready.badLinks||
          !(ready.softwareProducts||ready.emptySoftware)||
          !(ready.references||ready.emptyReferences)){
          failed=true;errors.push('V32 homepage sections missing on fresh database: '+JSON.stringify(ready));
        }
        if(screen.name==='masaustu'){
          await page.locator('#hazir-yazilimlar').screenshot({path:path.join(output,'software-empty-state-v32.png'),animations:'disabled'});
          await page.locator('#referanslarimiz').screenshot({path:path.join(output,'references-empty-state-v32.png'),animations:'disabled'});
        }
      }
      // V30: reviews and four-step service journey must be visible and usable
      // at all four viewport sizes, with no fake/inert review action.
      if(p.route==='/' && ['mobil','tablet','masaustu','genis'].includes(screen.name)){
        const v30=await page.evaluate(()=>{
          const reviewSection=document.querySelector('.nv30-feedback');
          const stepSection=document.querySelector('.nv30-process');
          const steps=[...(stepSection?.querySelectorAll('.nv30-step')||[])];
          const cards=[...(reviewSection?.querySelectorAll('.nv30-review-card')||[])];
          const check=sel=>{
            const el=document.querySelector(sel);
            if(!el)return null;
            const r=el.getBoundingClientRect();
            return {left:r.left,right:r.right,top:r.top,bottom:r.bottom};
          };
          const overlap=boxes=>boxes.some((r,i)=>boxes.slice(i+1).some(other=>
            Math.min(r.right,other.right)-Math.max(r.left,other.left)>3 &&
            Math.min(r.bottom,other.bottom)-Math.max(r.top,other.top)>3
          ));
          const rects=els=>els.map(el=>el.getBoundingClientRect());
          return {
            steps:steps.length,
            reviews:cards.length,
            oldModules:document.querySelectorAll('section.yh6-reviews,section.yh6-how').length,
            badLinks:[...document.querySelectorAll('.nv30-feedback a,.nv30-process a')]
              .filter(a=>!a.getAttribute('href') || a.getAttribute('href')==='#').length,
            stepOverlap:overlap(rects(steps)),
            reviewOverlap:overlap(rects(cards)),
            oldTooSmall:[...document.querySelectorAll('.nv30-review-card blockquote,.nv30-step p')]
              .some(el=>parseFloat(getComputedStyle(el).fontSize)<9.8),
            sectionOverflow:[reviewSection,stepSection]
              .filter(Boolean).some(el=>el.scrollWidth>el.clientWidth+8),
            stepCard:check('.nv30-step'),
          };
        });
        if(v30.steps!==4||v30.oldModules||v30.badLinks||v30.stepOverlap||
           v30.reviewOverlap||v30.oldTooSmall||v30.sectionOverflow||!v30.stepCard){
          errors.push('Premium reviews/steps failure: '+JSON.stringify(v30));failed=true;
        }
        if(screen.name==='masaustu'){
          await page.locator('.nv30-process').screenshot({path:path.join(output,'premium-process-v30.png'),animations:'disabled'});
          if(v30.reviews) await page.locator('.nv30-feedback').screenshot({path:path.join(output,'premium-reviews-v30.png'),animations:'disabled'});
        }
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

// Full integration: admin installs real script taxonomy, picks one real CI-only
// package, orders it separately, then publishes an authentic test reference.
// All writes are to disposable Actions MySQL; never the production site.
const showcaseCtx=await browser.newContext({viewport:{width:1440,height:900}});
const showcasePage=await showcaseCtx.newPage();
showcasePage.on('dialog',dialog=>dialog.accept());
try {
  await showcasePage.goto(origin+'/admin/giris',{waitUntil:'domcontentloaded'});
  await showcasePage.locator('input[name=email]').fill('admin@yorumhizmeti.tr');
  await showcasePage.locator('input[name=password]').fill('qa-test-menu-only');
  await Promise.all([
    showcasePage.waitForURL('**/admin',{waitUntil:'domcontentloaded'}),
    showcasePage.locator('form button[type=submit]').click()
  ]);
  // Netvera staging: new admin routes must load even before importing
  // external public catalog. Live PayTR must never be requested by this test.
  await showcasePage.goto(origin+'/admin/netvera-yazilimlar',{waitUntil:'domcontentloaded'});
  if(!(await showcasePage.getByText('Netvera Hazır Yazılımlar',{exact:false}).count()))
    throw new Error('Netvera admin catalog route not available');
  await showcasePage.goto(origin+'/admin/netvera-yazilimlar/ekle',{waitUntil:'domcontentloaded'});
  if(!(await showcasePage.locator('form[action="/admin/netvera-yazilimlar/kaydet"]').count()))
    throw new Error('Netvera rich script editor not available');
  await showcasePage.goto(origin+'/hazir-scriptler',{waitUntil:'domcontentloaded'});
  if(!(await showcasePage.locator('.nv40-store').count()))
    throw new Error('Legacy indexed scripts route missing');
  await showcasePage.goto(origin+'/admin/paket/ekle',{waitUntil:'domcontentloaded'});
  if(!(await showcasePage.locator('[name="nvseo_secondary_keywords"]').count()))
    throw new Error('Native service SEO extension missing');
  // Netvera canonical path parity — synthetic isolated CI products only.
  await showcasePage.goto(origin+'/hazir-scriptler/haber-sitesi-scripti',{waitUntil:'domcontentloaded'});
  if(!(await showcasePage.locator('.nv40-detail').count()) ||
     !(await showcasePage.locator('link[rel=canonical][href$="/hazir-scriptler/haber-sitesi-scripti"]').count())){
    throw new Error('Netvera indexed product URL or canonical missing');
  }
  if(!(await showcasePage.locator('h1').textContent()).includes('Haber Scripti')){
    throw new Error('Indexed Netvera script route does not load its own product');
  }
  const oldUrls=[
    '/haber-sitesi-scripti',
    '/paket/netvera-haber-sitesi-script-yazilimi'
  ];
  for(const oldUrl of oldUrls){
    const redirect=await showcasePage.request.get(origin+oldUrl,{maxRedirects:0});
    if(redirect.status()!==301||
       redirect.headers()['location']!=='/hazir-scriptler/haber-sitesi-scripti'){
      throw new Error('Missing 301 '+oldUrl+' to original Netvera canonical product');
    }
  }
  const sitemapResponse=await showcasePage.request.get(origin+'/sitemap.xml');
  const sitemapXml=await sitemapResponse.text();
  if(!sitemapXml.includes('/hazir-scriptler/haber-sitesi-scripti')||
     sitemapXml.includes('/paket/netvera-haber-sitesi-script-yazilimi')){
    throw new Error('Netvera software sitemap missing canonical or still lists redirected package alias');
  }
  for(const [oldUrl,target] of [
    ['/temizlik-firmasi-scripti-web-site-yazilimi','netvera-temizlik-firmasi-script-yazilimi-pro'],
    ['/emlak-scripti-hazir-emlak-sitesi-yazilimi','netvera-emlak-script-yazilimi-pro']
  ]){
    const redirect=await showcasePage.request.get(origin+oldUrl,{maxRedirects:0});
    if(redirect.status()!==301 ||
       redirect.headers()['location']!=='/hazir-scriptler/'+target){
      throw new Error('Indexed legacy alias redirects to wrong software: '+oldUrl);
    }
  }
  // Older scripts added under the existing Web Site category must show up
  // automatically, WITHOUT installing the 23 new categories or selecting a
  // special featured flag. This reproduces the user's local problem.
  await showcasePage.goto(origin+'/admin/paket/ekle',{waitUntil:'domcontentloaded'});
  const legacyName='CI Eski Kategori Yazılım Scripti';
  const legacySlug='ci-eski-kategori-yazilim-scripti';
  await showcasePage.locator('input[name=name]').fill(legacyName);
  await showcasePage.locator('input[name=slug]').fill(legacySlug);
  await showcasePage.locator('select[name=category_id]').selectOption({label:'Web Site Hizmetleri'});
  await showcasePage.locator('textarea[name=short_description]').fill('Geçici test yazılımı; eski kategoriden otomatik bulunmalıdır.');
  await showcasePage.locator('input[name=price]').fill('1800');
  await Promise.all([
    showcasePage.waitForURL('**/admin/paketler',{waitUntil:'domcontentloaded'}),
    showcasePage.locator('form button[type=submit]').filter({hasText:'Kaydet'}).first().click()
  ]);
  await showcasePage.goto(origin+'/',{waitUntil:'domcontentloaded'});
  if(!(await showcasePage.locator('.nv31-software-card a[href="/paket/'+legacySlug+'"]').count()))
    throw new Error('Legacy-category ready script was NOT automatically displayed');
  await showcasePage.goto(origin+'/hazir-yazilimlar',{waitUntil:'domcontentloaded'});
  if(!(await showcasePage.locator('.nv31-software-card a[href="/paket/'+legacySlug+'"]').count()))
    throw new Error('Legacy-category ready script missing from standalone software catalog');

  // On a fresh DB, the direct "Add Software" route shows a one-click setup
  // instead of silently redirecting to the showcase (the user's reported issue).
  await showcasePage.goto(origin+'/admin/yazilim/ekle',{waitUntil:'domcontentloaded'});
  const firstUse=showcasePage.locator('form[action="/admin/hazir-yazilimlar/kur-ve-ekle"]');
  if(!(await firstUse.count()) || !(await firstUse.locator('button[type=submit]').count()))
    throw new Error('First-use software creation page does not show its setup action');
  await Promise.all([
    showcasePage.waitForURL('**/admin/yazilim/ekle',{waitUntil:'domcontentloaded'}),
    firstUse.locator('button[type=submit]').click()
  ]);
  if(!(await showcasePage.locator('select[name=category_id]').count()))
    throw new Error('One-click category setup did not open ready software editor');
  await showcasePage.goto(origin+'/admin/hazir-yazilimlar',{waitUntil:'domcontentloaded'});
  if(!(await showcasePage.locator('a[href="/admin/yazilim/ekle"]').count()))
    throw new Error('Software showcase does not expose the create button after setup');
  await showcasePage.goto(origin+'/kategoriler?grup=agency',{waitUntil:'domcontentloaded'});
  if(!(await showcasePage.getByText('Hazır Yazılımlar & Scriptler',{exact:true}).count()))
    throw new Error('Installed script category not visible in agency catalog');

  await showcasePage.goto(origin+'/admin/yazilim/ekle',{waitUntil:'domcontentloaded'});
  const opts=await showcasePage.locator('select[name=category_id] option').allTextContents();
  if(!opts.includes('Haber Sitesi Yazılımı') || opts.includes('Instagram Hizmetleri') ||
     !opts.includes('WordPress Temaları')){
    throw new Error('Dedicated software form leaked services or lacks software types: '+opts.slice(0,6));
  }
  const productName='CI Yazılım Demo Paketi';
  const productSlug='ci-yazilim-demo-v31';
  await showcasePage.locator('input[name=name]').fill(productName);
  await showcasePage.locator('input[name=slug]').fill(productSlug);
  await showcasePage.locator('select[name=category_id]').selectOption({label:'Haber Sitesi Yazılımı'});
  await showcasePage.locator('textarea[name=short_description]').fill('CI için geçici yazılım vitrini testi, canlı satış ürünü değildir.');
  await showcasePage.locator('input[name=price]').fill('2500');
  await Promise.all([
    showcasePage.waitForURL('**/admin/hazir-yazilimlar',{waitUntil:'domcontentloaded'}),
    showcasePage.locator('form button[type=submit]').filter({hasText:'Kaydet'}).first().click()
  ]);
  // New catalog-category products also appear without marking featured.
  await showcasePage.goto(origin+'/',{waitUntil:'domcontentloaded'});
  if(!(await showcasePage.locator('.nv31-software-card a[href="/paket/'+productSlug+'"]').count()))
    throw new Error('New ready-script package requires an unnecessary manual feature selection');
  // Real functional search, filtering and price sorting on live CI catalog.
  await showcasePage.goto(origin+'/hazir-yazilimlar?q=Yaz%C4%B1l%C4%B1m',{waitUntil:'domcontentloaded'});
  if(!(await showcasePage.locator('.nv36-product-grid .nv31-software-card').count()))
    throw new Error('Catalog search failed to show the software test record');
  await showcasePage.goto(origin+'/hazir-yazilimlar?min=2000&max=2800&siralama=ucuz',{waitUntil:'domcontentloaded'});
  if(!(await showcasePage.locator('.nv36-product-grid a[href="/paket/'+productSlug+'"]').count()) ||
     await showcasePage.locator('.nv36-product-grid a[href="/paket/'+legacySlug+'"]').count()){
    throw new Error('Price filter did not isolate matching products');
  }
  await showcasePage.goto(origin+'/hazir-yazilimlar?tur=wordpress-temalari',{waitUntil:'domcontentloaded'});
  if(!(await showcasePage.locator('.nv33-catalog-empty').count()))
    throw new Error('Empty WordPress subcategory should show helpful empty state');
  await showcasePage.goto(origin+'/admin/hazir-yazilimlar',{waitUntil:'domcontentloaded'});
  const productRow=showcasePage.locator('.adm31-package-row').filter({hasText:productName});
  if(!(await productRow.count()))throw new Error('Newly saved software package absent from showcase admin');
  await productRow.locator('input[name="featured[]"]').check();
  await productRow.locator('input[type=number]').fill('1');
  await Promise.all([
    showcasePage.waitForURL('**/admin/hazir-yazilimlar',{waitUntil:'domcontentloaded'}),
    showcasePage.locator('.adm31-showcase-form button[type=submit]').click()
  ]);
  await showcasePage.goto(origin+'/',{waitUntil:'domcontentloaded'});
  if(await showcasePage.locator('.nv32-software-intro').count())throw new Error('Empty script presentation remained visible after selection');
  if(!(await showcasePage.locator('.nv31-software-card a[href="/paket/'+productSlug+'"]').count()))
    throw new Error('Saved script selection not displayed on storefront');

  // Explicit manual selection takes priority over automatic defaults.
  if(await showcasePage.locator('.nv31-software-card a[href="/paket/'+legacySlug+'"]').count())
    throw new Error('Manual software ordering did not replace automatic selection');
  await showcasePage.goto(origin+'/admin/referanslar',{waitUntil:'domcontentloaded'});
  const create=showcasePage.locator('form[action="/admin/referanslar/ekle"]');
  await create.locator('[name=title]').fill('CI Web Referans Testi');
  await create.locator('[name=category]').fill('Haber Sitesi');
  await create.locator('[name=description]').fill('Otomatik test sırasında oluşturulmuş örnek portföy kaydı.');
  await create.locator('[name=url]').fill('https://example.test/');
  await create.locator('[name=sort_order]').fill('1');
  await Promise.all([
    showcasePage.waitForURL('**/admin/referanslar',{waitUntil:'domcontentloaded'}),
    create.locator('button[type=submit]').click()
  ]);
  if(!(await showcasePage.locator('.adm31-ref-editor').filter({hasText:'CI Web Referans Testi'}).count()))
    throw new Error('Reference admin did not persist the project');
  await showcasePage.goto(origin+'/',{waitUntil:'domcontentloaded'});
  if(!(await showcasePage.locator('.nv31-portfolio-card').filter({hasText:'CI Web Referans Testi'}).count()))
    throw new Error('Published reference missing from storefront');
  if(await showcasePage.locator('.nv32-portfolio-intro').count())
    throw new Error('Empty reference presentation remained visible after publishing a project');

  await showcasePage.screenshot({path:path.join(output,'live-script-reference-showcase-desktop.png'),fullPage:true,animations:'disabled'});
  await showcasePage.setViewportSize({width:390,height:844});
  await showcasePage.reload({waitUntil:'domcontentloaded'});
  const geometry=await showcasePage.evaluate(()=>{
    const sections=['.nv31-software','.nv31-portfolio'].map(s=>document.querySelector(s));
    return sections.map(root=>{
      const cards=[...root.querySelectorAll('article')];
      const boxes=cards.map(el=>el.getBoundingClientRect());
      return {count:cards.length,width:root.clientWidth,scroll:root.scrollWidth,
        cross:boxes.some((r,i)=>boxes.slice(i+1).some(o=>
          Math.min(r.right,o.right)-Math.max(r.left,o.left)>4 &&
          Math.min(r.bottom,o.bottom)-Math.max(r.top,o.top)>4))};
    });
  });
  if(geometry.some(g=>!g.count||g.scroll>g.width+4||g.cross))
    throw new Error('Mobile software/reference layout defect '+JSON.stringify(geometry));
  await showcasePage.screenshot({path:path.join(output,'live-script-reference-showcase-mobile.png'),fullPage:true,animations:'disabled'});
  results.push({route:'Admin install -> software featured -> reference publish',screen:'integration',status:200,errors:[]});
  console.log('PASS real admin software and references end-to-end');
}catch(e){
  failed=true;
  results.push({route:'Admin install -> software featured -> reference publish',screen:'integration',status:0,errors:[String(e)]});
  console.error('FAIL software/reference integration:',String(e).slice(0,600));
}finally{await showcaseCtx.close();}

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
