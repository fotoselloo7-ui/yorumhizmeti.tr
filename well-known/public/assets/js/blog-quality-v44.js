(() => {
'use strict';
const form=document.querySelector('form[action^="/admin/blog/"]');
if(!form || !document.querySelector('.nv44-quality-card'))return;
const get=id=>document.getElementById(id)?.value?.trim()||'';
const field=n=>form.querySelector('[name="'+n+'"]')?.value?.trim()||'';
const len=s=>[...s].length;
const lower=s=>s.toLocaleLowerCase('tr-TR');
const clean=s=>s.replace(/<[^>]+>/g,' ').replace(/!\[[^\]]*\]\([^)]+\)/g,' ');
const item=(label,ok,points)=>({label,ok:!!ok,points});
const score=items=>Math.round(items.reduce((a,x)=>a+(x.ok?x.points:0),0)/items.reduce((a,x)=>a+x.points,0)*100);
let active='seo',last={};
const checkList=document.getElementById('nv44-quality-checks');
function render(){
  const frag=document.createDocumentFragment();
  (last[active]||[]).forEach(check=>{
    const li=document.createElement('li'),sym=document.createElement('span');
    sym.className=check.ok?'nv44-check-ok':'nv44-check-missing';
    sym.textContent=check.ok?'✓':'○';
    li.append(sym,document.createTextNode(' '+check.label));
    frag.append(li);
  });
  checkList.replaceChildren(frag);
}
function calculate(){
  const body=get('raw_content');
  const bodyText=clean(body).replace(/[#*_]/g,' ');
  const words=bodyText.match(/[\p{L}\p{N}]+(?:['’][\p{L}\p{N}]+)*/gu)||[];
  const n=words.length;
  const title=get('title'),seoTitle=get('seo_title'),meta=get('seo_description'),focus=get('seo_focus_keyword');
  const slug=get('slug')||lower(title).replace(/\s+/g,'-');
  const first=lower(bodyText.slice(0,500));
  const mentions=focus?lower(bodyText).split(lower(focus)).length-1:0;
  const h2=/(^|\n)\s*##\s+\S|<h2\b/i.test(body);
  const h3=/(^|\n)\s*###\s+\S|<h3\b/i.test(body);
  const images=/!\[[^\]]+\]\([^)]+\)|<img\b[^>]*alt\s*=/i.test(body)||get('image_alt')!=='';
  const internal=/\[[^\]]+\]\((\/|https?:\/\/(?:www\.)?netvera\.tr\/)[^)]+\)|href=["']\//i.test(body);
  const external=/\[[^\]]+\]\(https?:\/\/(?!www\.netvera\.tr|netvera\.tr)[^)]+\)/i.test(body);
  const faqCount=[...form.querySelectorAll('[name="faq_questions[]"]')].filter(i=>
    i.value.trim()&&i.closest('.faq-row')?.querySelector('[name="faq_answers[]"]')?.value?.trim()).length;
  const seo=[
    item('SEO başlığı 35–65 karakter',len(seoTitle)>=35&&len(seoTitle)<=65,12),
    item('Meta açıklaması 110–165 karakter',len(meta)>=110&&len(meta)<=165,12),
    item('Odak anahtar kelime',!!focus,12),
    item('Odak kelime başlıkta',!!focus&&lower(seoTitle||title).includes(lower(focus)),10),
    item('Anlamlı URL slug',!!slug&&slug.length<=100,10),
    item('Makale kategorisi',!!get('blog_category_id'),10),
    item('H2 alt başlık',h2,10),
    item('Görsel ALT metni',images,10),
    item('Canonical adresi',!!slug||!!get('canonical_url'),7),
    item('SERP ve sosyal paylaşım metni',!!seoTitle&&!!meta,7)
  ];
  const geo=[
    item('Ana kullanıcı sorusu',!!field('nvseo_main_question'),17),
    item('Doğrudan cevap (40+ karakter)',len(field('nvseo_direct_answer'))>=40,18),
    item('GEO özeti (40+ karakter)',len(field('nvseo_geo_summary'))>=40,13),
    item('İlgili konu / entity',!!field('nvseo_entity_topics'),10),
    item('Güvenilir kaynak bağlantıları',!!field('nvseo_sources'),14),
    item('Yazar / kurum',!!field('nvseo_author_name'),10),
    item('Kullanıcı arama niyeti',!!field('nvseo_content_intent'),8),
    item('Görünür soru-cevap',faqCount>0,10)
  ];
  const stuffing=!!focus&&mentions>Math.max(7,Math.ceil(n/100)*4);
  const quality=[
    item('600+ kelime',n>=600,21),
    item('1200+ kelime',n>=1200,10),
    item('H2 ve H3 başlık yapısı',h2&&h3,13),
    item('Liste / adımlar',/(^|\n)\s*(?:[-*+]|\d+\.)\s+\S/m.test(body),10),
    item('İç bağlantı',internal,12),
    item('Dış kaynak bağlantısı',external,10),
    item('Odak kelime ilk paragrafta',!!focus&&first.includes(lower(focus)),9),
    item('80+ karakter özet',len(get('excerpt'))>=80,7),
    item('Anahtar kelime aşırı tekrar edilmiyor',!stuffing,8)
  ];
  last={seo,geo,content:quality};
  const scores={seo:score(seo),geo:score(geo),content:score(quality)};
  const overall=Math.round(scores.seo*.4+scores.geo*.3+scores.content*.3);
  document.getElementById('nv44-overall-score').textContent=overall+' / 100';
  document.getElementById('nv44-word-count').textContent=n.toLocaleString('tr-TR');
  Object.keys(scores).forEach(k=>{
    const card=form.querySelector('[data-quality-meter="'+k+'"]');
    if(!card)return;
    card.querySelector('[data-quality-value]').textContent=scores[k]+' / 100';
    const bar=card.querySelector('[data-quality-bar]');
    bar.style.width=scores[k]+'%';
    bar.dataset.grade=scores[k]>=80?'good':scores[k]>=50?'medium':'low';
  });
  document.getElementById('nv44-serp-url').textContent=(location.hostname||'localhost')+'/blog/'+slug;
  document.getElementById('nv44-serp-title').textContent=seoTitle||title||'Makale SEO başlığı';
  document.getElementById('nv44-serp-description').textContent=meta||get('excerpt')||'Meta açıklaması burada görüntülenir.';
  render();
}
form.querySelectorAll('.nv44-quality-tab').forEach(button=>button.addEventListener('click',()=>{
  active=button.dataset.qualityFilter;
  form.querySelectorAll('.nv44-quality-tab').forEach(b=>{
    const yes=b===button;b.classList.toggle('active',yes);b.setAttribute('aria-pressed',String(yes));
  });
  render();
}));
let wait=0;const refresh=()=>{clearTimeout(wait);wait=setTimeout(calculate,150);};
form.addEventListener('input',refresh);
form.addEventListener('change',refresh);
new MutationObserver(refresh).observe(document.getElementById('faq_container')||form,{childList:true});
calculate();
})();