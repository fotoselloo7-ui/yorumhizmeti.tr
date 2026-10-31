/* Isolated 10-theme Chromium layout QA: synthetic public markup, no live DB writes. */
const fs = require('node:fs');
const path = require('node:path');
const {chromium} = require('playwright');

const root=path.resolve(__dirname,'..');
const css=fs.readFileSync(path.join(root,'public/assets/css/landing-themes.css'),'utf8');
const variants=['timeline','dark-flow','steps','steps','steps','alternating','steps','timeline','steps','steps'];
const itemBody=[
  'Sosyal medya uzmanı hesap analizi ve iletişim planlaması oluşturur. '.repeat(3),
  'Müşteri davranışını anlama aşamasında gerçek sektörel ihtiyaçlar önemlidir. '.repeat(8),
  'Video içerikleri, Reels ve kampanya görselleri hedeflerle ilişkilendirilmelidir. '.repeat(5),
  'Bölgesel çalışma kapsamı ve farklı müşteri grupları için doğru yöntemler belirlenir. '.repeat(7),
  'Reklam yönetimi organik yönetimle aynı hizmet değildir. '.repeat(4),
  'İçerik üretimi yapılırken tasarım, video ve raporlama süreçleri değerlendirilir. '.repeat(6),
  'Görünürlük, etkileşim ve gerçek müşteri talepleri ayrı göstergeler olarak incelenir. '.repeat(5),
  'İşletmeye özel fiyatlandırma kapsam ve teslimler belirlendikten sonra yapılır. '.repeat(5),
];
const escapeHtml = s => String(s).replace(/[&<>"]/g,ch=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[ch]));
const mkPage=(index)=> {
 const variant=variants[index];
 const articles=itemBody.map((text,i)=>`<section class="nvl-article" id="landing-section-${i+1}">
  <div class="nvl-step-marker"><span class="nvl-step-number">${String(i+1).padStart(2,'0')}</span><span class="nvl-step-symbol">◉</span></div>
  <div class="nvl-article-copy"><h2>${i+1}. Alanya sosyal medya hizmetinde önemli bir başlık</h2><div class="nvl-prose">${escapeHtml(text)}</div><h3>Uygulama detayları</h3><div class="nvl-prose">İçerik ve teslim kapsamını birlikte belirleyin.</div></div>
 </section>`).join('');
 const cards=Array.from({length:5},(_,i)=>`<article class="nvl-card"><div class="nvl-card-media"></div><div class="nvl-card-copy"><h3>Hizmet kartı ${i+1}</h3><p>İşletmeniz için çözümler</p></div></article>`).join('');
 const services=Array.from({length:5},(_,i)=>`<a class="nvl-service-card" href="#"><div class="nvl-service-icon">●</div><h3>Hizmet ${i+1}</h3><p>Doğrulanabilir hizmet alanı ve müşteriyle iletişim bilgisi.</p><strong>İncele</strong></a>`).join('');
 return `<!DOCTYPE html><html lang="tr"><head><meta name="viewport" content="width=device-width, initial-scale=1"><style>
 *{box-sizing:border-box}body{margin:0} .nv-landing .nvl-hero{height:180px}
 </style><style>${css}</style></head><body><main class="nv-landing" data-theme="landing-${String(index+1).padStart(2,'0')}">
 <div class="nvl-hero"><div class="nvl-container"><h1>Alanya Sosyal Medya Yönetimi</h1></div></div>
 <section class="nvl-section" data-component="services" data-variant="tiles"><div class="nvl-container"><div class="nvl-service-grid">${services}</div></div></section>
 <section class="nvl-section nvl-section-content" data-component="content" data-variant="${variant}"><div class="nvl-container"><div class="nvl-articles">${articles}</div></div></section>
 <section class="nvl-section" data-component="scripts" data-variant="gallery"><div class="nvl-container"><div class="nvl-card-grid">${cards}</div></div></section>
 <section class="nvl-contact nvl-container" data-variant="wide"><div class="nvl-contact-shell"><h2>Teklif alın</h2></div></section>
 </main></body></html>`;
};
(async()=>{
 const browser=await chromium.launch({headless:true});
 const errors=[];
 for(const width of [2560,1920,1440,900,390]){
   const page=await browser.newPage({viewport:{width,height:820},deviceScaleFactor:1});
   for(let i=0;i<10;i++){
     await page.setContent(mkPage(i),{waitUntil:'load'});
     const data=await page.evaluate(()=>{
        const content=document.querySelector('[data-component="content"]');
        const articles=[...content.querySelectorAll('.nvl-article')];
        const wrapper=content.querySelector('.nvl-articles');
        const bounds=[...document.querySelectorAll('.nvl-card, .nvl-service-card, .nvl-article')].map(el=>el.getBoundingClientRect().toJSON());
        return {overflow:document.documentElement.scrollWidth-window.innerWidth,
          columns:getComputedStyle(wrapper).columnCount,
          columnMode:getComputedStyle(wrapper).display,
          width:window.innerWidth,
          rects:bounds, articleRects:articles.map(el=>el.getBoundingClientRect().toJSON()),
          wrapperRect:wrapper.getBoundingClientRect().toJSON(),
          contentContainers:[...document.querySelectorAll('.nvl-hero>.nvl-container,.nvl-section>.nvl-container,.nvl-contact.nvl-container')].map(el=>({component:el.parentElement.dataset.component||el.parentElement.className,rect:el.getBoundingClientRect().toJSON()})),
          cardGridRect:document.querySelector('[data-component="scripts"] .nvl-card-grid').getBoundingClientRect().toJSON(),
          cardRects:[...document.querySelectorAll('[data-component="scripts"] .nvl-card')].map(el=>el.getBoundingClientRect().toJSON())};
     });
     const tag=`theme ${i+1}, ${width}px`;
     if(data.overflow>2)errors.push(tag+`: horizontal overflow ${data.overflow}`);
     const expected=Math.min(1200,width-(width<=420?32:40));
     for (const item of data.contentContainers) {
       const rect=item.rect;
       if(Math.abs(rect.width-expected)>2) errors.push(tag+`: ${item.component} wrapper is ${rect.width}px, must be ${expected}px`);
       if(Math.abs((rect.left+rect.right)/2-width/2)>2) errors.push(tag+`: ${item.component} wrapper is not horizontally centered`);
     }
     if((['steps','timeline','dark-flow'].includes(variants[i])) && width>=768 && data.columns!=='2')errors.push(tag+`: expected 2 dense columns, got ${data.columns}`);
     if(width<768 && data.columns==='2')errors.push(tag+': expected one mobile column');
     for(const rect of data.rects) {
       if(rect.width<=0 || rect.x < -2 || rect.right>width+2)errors.push(tag+': misplaced card '+JSON.stringify(rect));
     }
     if(width>=768) {
       const bottomY=Math.max(...data.cardRects.map(r=>r.y));
       const bottomRow=data.cardRects.filter(r=>Math.abs(r.y-bottomY)<2);
       const rowLeft=Math.min(...bottomRow.map(r=>r.left));
       const rowRight=Math.max(...bottomRow.map(r=>r.right));
       const rowCenter=(rowLeft+rowRight)/2;
       const gridCenter=data.cardGridRect.x+data.cardGridRect.width/2;
       if(Math.abs(rowCenter-gridCenter)>12) errors.push(tag+`: incomplete desktop card row is not centered (${rowCenter} vs ${gridCenter})`);
     }
     if(i===2 && width===1920) await page.screenshot({path:process.env.RUNNER_TEMP+'/netvera-green-route-desktop.png',fullPage:true});
     if(i===2 && width===390) await page.screenshot({path:process.env.RUNNER_TEMP+'/netvera-green-route-mobile.png',fullPage:true});
   }
   await page.close();
 }
 await browser.close();
 if(errors.length){console.error(errors.slice(0,50).join('\n'));process.exit(1);}
 console.log('50 viewport/theme checks passed: 10 themes x 2560/1920/1440/900/390; exact 1200px centered containers and card alignment.');
})().catch(e=>{console.error(e);process.exit(1)});
