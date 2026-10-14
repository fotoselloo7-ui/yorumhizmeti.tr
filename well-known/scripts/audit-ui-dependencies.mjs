import fs from 'node:fs';
import path from 'node:path';
import {fileURLToPath} from 'node:url';

const root=path.resolve(path.dirname(fileURLToPath(import.meta.url)),'..');
const walk=dir=>fs.readdirSync(dir,{withFileTypes:true}).flatMap(e=>e.name.includes('sync-conflict')?[]:e.isDirectory()?walk(path.join(dir,e.name)):[path.join(dir,e.name)]);
const relative=file=>path.relative(root,file).replaceAll('\\','/');
const views=walk(path.join(root,'resources/views'));
const source=walk(path.join(root,'app')).concat(views).map(file=>({file:relative(file),text:fs.readFileSync(file,'utf8')}));
const router=fs.readFileSync(path.join(root,'app/Core/App.php'),'utf8');
const routes=[...router.matchAll(/\$r->(get|post)\('([^']+)',\s*'([^']+)'\)/g)].map(m=>({method:m[1].toUpperCase(),path:m[2],action:m[3]}));
const layouts=views.filter(file=>relative(file).startsWith('resources/views/layouts/')).map(file=>({file:relative(file),css:[...fs.readFileSync(file,'utf8').matchAll(/asset\('css\/([^']+)'\)/g)].map(m=>m[1]),js:[...fs.readFileSync(file,'utf8').matchAll(/asset\('js\/([^']+)'\)/g)].map(m=>m[1])}));
const css=walk(path.join(root,'public/assets/css')).map(file=>{
 const text=fs.readFileSync(file,'utf8');
 const name=path.basename(file);
 const includes=source.filter(s=>s.text.includes(name)).map(s=>s.file);
 const selectors=[...text.matchAll(/([^{}]+)\{([^{}]*)\}/g)].filter(m=>!m[1].trim().startsWith('@')).map(m=>m[1].replace(/\/\*[\s\S]*?\*\//g,'').trim());
 const duplicates=[...new Set(selectors.filter((s,i)=>selectors.indexOf(s)!==i))];
 const authoritative=['app.css','responsive-fluid-v13.css','home-service-groups-v29.css','agency-navigation-v26.css','netvera-legacy-premium-v40.css','netvera-product-detail-v60.css','admin.css','admin-v4.css','marketing-popup-v97.css'];
 return {name,bytes:Buffer.byteLength(text),includes,classification:includes.length?(authoritative.includes(name)?'ACTIVE / AUTHORITATIVE':'LEGACY BUT REQUIRED'):'UNREFERENCED — verify before removal',important:(text.match(/!important/g)||[]).length,zIndex:[...text.matchAll(/z-index\s*:\s*(-?\d+)/g)].map(m=>Number(m[1])).filter(n=>n>=1000),duplicateSelectors:duplicates};
});
const js=walk(path.join(root,'public/assets/js')).map(file=>({file:relative(file),bytes:fs.statSync(file).size,referencedBy:source.filter(s=>s.text.includes(path.basename(file))).map(s=>s.file)}));
const report={routes,layouts,css,js,views:views.map(relative)};
const out=path.join(root,'../qa-artifacts/ui-audit');
fs.mkdirSync(out,{recursive:true});
fs.writeFileSync(path.join(out,'dependencies.json'),JSON.stringify(report,null,2));
console.log(JSON.stringify({routes:routes.length,getRoutes:routes.filter(r=>r.method==='GET').length,publicGet:routes.filter(r=>r.method==='GET'&&!r.path.startsWith('/admin')).length,adminGet:routes.filter(r=>r.method==='GET'&&r.path.startsWith('/admin')).length,templates:views.length,cssFiles:css.length,cssBytes:css.reduce((a,c)=>a+c.bytes,0),important:css.reduce((a,c)=>a+c.important,0),layouts:layouts.map(l=>({file:l.file,css:l.css.length,js:l.js.length})),unreferenced:css.filter(c=>!c.includes.length).map(c=>c.name),report:relative(path.join(out,'dependencies.json'))},null,2));
