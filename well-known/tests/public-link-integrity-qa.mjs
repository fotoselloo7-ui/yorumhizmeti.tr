/**
 * Read-only internal public-link crawler for the temporary CI database.
 * Never contacts live NetVera, signs in, posts forms, follows off-site demos,
 * or rewrites any imported NetVera route/slug/canonical.
 */
import fs from 'node:fs';
import pathModule from 'node:path';
import { request } from 'playwright';

const origin=(process.env.QA_ORIGIN || 'http://127.0.0.1:8006').replace(/\/$/,'');
const out=process.env.QA_LINK_REPORT || '/tmp/netvera-link-integrity.json';
const maxPages=Number(process.env.QA_LINK_MAX_PAGES || 1600);
const safeOrigin=new URL(origin);
const siteHosts=new Set([safeOrigin.hostname,'netvera.tr','www.netvera.tr']);
const privatePath=/^\/(?:admin|hesabim|siparislerim|siparis|odeme|payment|sepet|destek|giris|kayit|cikis|sifre-sifirla|sifremi-unuttum|netvera\/canli-destek|bayilik|bayi)(?:\/|$)/i;
const excludedPath=/^\/(?:api|webhook|logout)(?:\/|$)/i;
const source=new Map(), discovered=new Set(), skipped=new Map(), failures=[], warnings=[], examined=[];
const roots=new Set(['/','/sitemap.xml','/kategoriler','/hazir-yazilimlar','/hazir-scriptler','/blog','/sss','/iletisim','/hizmet-bolgeleri']);
const importedAliases=new Map([
 ['/haber-sitesi-scripti','/hazir-scriptler/haber-sitesi-scripti'],
 ['/temizlik-firmasi-scripti-web-site-yazilimi','/hazir-scriptler/netvera-temizlik-firmasi-script-yazilimi-pro'],
 ['/emlak-scripti-hazir-emlak-sitesi-yazilimi','/hazir-scriptler/netvera-emlak-script-yazilimi-pro'],
 ['/yapay-zeka-haber-yazilimi','/hazir-scriptler/haber-sitesi-scripti'],
 ['/dijital-hizmetler/sosyal-medya-yonetimi','/kategoriler/grup/social'],
 ['/dijital-hizmetler/modern-web-tasarim','/kategori/web-tasarim'],
 ['/dijital-hizmetler/seo-hizmetleri','/kategori/seo-hizmetleri'],
 ['/dijital-hizmetler/google-meta-ads','/kategori/dijital-reklam'],
 ['/dijital-hizmetler','/kategoriler'],
 ['/teklif-al','/iletisim']
]);
const unescapeHtml=(v)=>v.replace(/&amp;/gi,'&').replace(/&quot;/gi,'"').replace(/&#39;|&apos;/gi,"'")
 .replace(/&#(\d+);/g,(_,n)=>String.fromCodePoint(Number(n)))
 .replace(/&#x([0-9a-f]+);/gi,(_,n)=>String.fromCodePoint(parseInt(n,16)));
function resolveHref(raw,base){
 const href=unescapeHtml(raw.trim());
 if(!href || href==='#' || href.startsWith('#'))return {kind:'fragment',href};
 if(/^(?:mailto:|tel:|sms:|geo:|whatsapp:)/i.test(href))return {kind:'external'};
 if(/^(?:javascript:|data:)/i.test(href))return {kind:'unsafe',href};
 let u;
 try {u=new URL(href,origin+base);}catch{return {kind:'invalid',href};}
 if(!['http:','https:'].includes(u.protocol))return {kind:'external'};
 if(!siteHosts.has(u.hostname.toLowerCase()))return {kind:'external'};
 const path=u.pathname+(u.search||'');
 if(privatePath.test(u.pathname)||excludedPath.test(u.pathname))return {kind:'private',path};
 return {kind:'public',path};
}
function register(path,from){
 if(!discovered.has(path)){discovered.add(path);source.set(path,from);}
}
for(const url of roots)register(url,'core navigation');
const errorsToReport=()=>({total:examined.length,queued:discovered.size,failures,warnings,skipped:Object.fromEntries(skipped),
 checked:examined,protectedAliases:[...importedAliases]});
const api=await request.newContext({ignoreHTTPSErrors:true,timeout:15000,extraHTTPHeaders:{'User-Agent':'NetVera-ReadOnly-CI-LinkAudit/1.0'}});
async function inspect(path){
 let res,body='',status=0,location='';
 try {res=await api.get(origin+path,{maxRedirects:0});status=res.status();
   location=res.headers()['location']||'';
 }catch(err){failures.push({path,from:source.get(path),reason:String(err)});return;}
 if(status>=400) {
   failures.push({path,status,from:source.get(path),reason:'Unreachable internal link'});
   return;
 }
 if(status>=300&&status<400){
   if(!location){failures.push({path,status,reason:'Redirect without Location'});return;}
   const resolved=resolveHref(location,path);
   if(resolved.kind==='public'){
     if(resolved.path===path){failures.push({path,status,reason:'Redirect loop'});return;}
     register(resolved.path,'redirect from '+path);
   } else if(resolved.kind==='unsafe'||resolved.kind==='invalid'){
     failures.push({path,status,location,reason:'Unsafe redirect'});
   } else if(resolved.kind==='external'){
     warnings.push({path,location,reason:'Offsite redirect not followed'});
   }
   examined.push({path,status,location});
   return;
 }
 const type=res.headers()['content-type']||'';
 if(/xml/.test(type)||path==='/sitemap.xml'){
   body=await res.text();
   const locs=[...body.matchAll(/<loc>([\s\S]*?)<\/loc>/g)].map(m=>unescapeHtml(m[1]));
   if(path==='/sitemap.xml'&&!locs.length)failures.push({path,reason:'Sitemap contains no URLs'});
   for(const loc of locs){
     const resolved=resolveHref(loc,'/');
     if(resolved.kind==='public')register(resolved.path,'sitemap');
     else if(resolved.kind!=='external')warnings.push({path,loc,reason:'Non-public sitemap location'});
   }
 } else if(/html/i.test(type)){
   body=await res.text();
   const refs=[...body.matchAll(/<a\b[^>]*\bhref\s*=\s*(?:"([^"]*)"|'([^']*)')/gi)];
   for(const match of refs){
     const target=resolveHref(match[1]??match[2]??'',path);
     if(target.kind==='public')register(target.path,path);
     if(target.kind==='private')skipped.set(target.path,'private/authenticated flow');
     if(target.kind==='unsafe'||target.kind==='invalid')failures.push({path,href:target.href,reason:'Unsafe/invalid anchor'});
   }
   if(/<title>\s*(?:404|Sayfa Bulunamadı)/i.test(body)&&status===200){
     failures.push({path,status,reason:'HTML not-found page incorrectly returned HTTP 200'});
   }
 }
 examined.push({path,status,from:source.get(path),anchors:(body.match(/<a\b/gi)||[]).length});
}
try {
 const pending=[...discovered],done=new Set();
 // New internal links are appended as pages are inspected, not silently ignored.
 while(pending.length){
   const batch=pending.splice(0,6).filter(x=>!done.has(x));
   await Promise.all(batch.map(async path=>{done.add(path);await inspect(path);}));
   for(const path of discovered)if(!done.has(path)&&!pending.includes(path))pending.push(path);
   if(done.size>maxPages){failures.push({reason:'Audit page bound exceeded',limit:maxPages});break;}
 }
 for(const [legacy,target] of importedAliases){
   const res=await api.get(origin+legacy,{maxRedirects:0});
   const redirect=resolveHref(res.headers()['location']||'',legacy);
   if(res.status()!==301||redirect.path!==target){
     failures.push({path:legacy,expected301:target,actualStatus:res.status(),actualLocation:res.headers()['location'],reason:'Imported NetVera 301 alias changed'});
   }
 }
 const report=errorsToReport();
 fs.mkdirSync(pathModule.dirname(out),{recursive:true});
 fs.writeFileSync(out,JSON.stringify(report,null,2));
 console.log('NetVera public internal links: '+examined.length+' inspected, '+failures.length+' failures, '+warnings.length+' notes; report '+out);
 for(const problem of failures.slice(0,30))console.error('BROKEN '+JSON.stringify(problem));
 if(failures.length)process.exitCode=1;
}finally{await api.dispose();}
