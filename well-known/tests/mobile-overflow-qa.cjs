'use strict';
const fs=require('node:fs'),path=require('node:path'),vm=require('node:vm'),assert=require('node:assert/strict');
const root=path.resolve(__dirname,'..');
const read=p=>fs.readFileSync(path.join(root,p),'utf8');
function el(){return {hidden:false,attributes:{},handlers:{},value:'0',textContent:'',
  setAttribute(k,v){this.attributes[k]=v;},addEventListener(k,f){this.handlers[k]=f;},
  focus(){this.focused=true;},querySelector:()=>null,querySelectorAll:()=>[]};}
const desktop=el(),mobile=el(),panel=el(),close=el(),form=el(),id=el(),doc=el();
panel.hidden=true;
let narrow=true;
const nodes={'[data-nv-chat-toggle]':desktop,'.nv-chat-panel':panel,'[data-nv-chat-close]':close,
 '[data-nv-chat-form]':form,'[data-nv-chat-status]':el(),'[data-nv-chat-messages]':el(),
 '[data-nv-chat-identity]':el(),'[data-nv-chat-id]':id};
const widget={querySelector:s=>nodes[s]};
doc.querySelector=s=>s==='[data-nv-chat]'?widget:s==='[data-nv-chat-mobile-toggle]'?mobile:null;
doc.querySelectorAll=()=>[desktop,mobile];
vm.runInNewContext(read('public/assets/js/netvera-live-chat.js'),{document:doc,window:{matchMedia:()=>({matches:narrow})},setInterval(){}});
mobile.handlers.click();assert.equal(panel.hidden,false);assert.equal(desktop.hidden,true);assert.equal(mobile.hidden,false);
assert.equal(mobile.attributes['aria-expanded'],'true');assert.equal(desktop.attributes['aria-expanded'],'true');
mobile.handlers.click();assert.equal(panel.hidden,true);assert.equal(mobile.attributes['aria-expanded'],'false');
mobile.handlers.click();close.handlers.click();assert.equal(panel.hidden,true);assert.equal(mobile.focused,true);
narrow=false;desktop.handlers.click();assert.equal(panel.hidden,false);
doc.handlers.keydown({key:'Escape',preventDefault(){}});assert.equal(panel.hidden,true);assert.equal(desktop.focused,true);
assert.ok(!read('resources/views/frontend/partials/netvera-live-chat.php').includes('<script>'));
assert.ok(read('public/assets/js/netvera-live-chat.js').includes("fetch(form.action,{method:'POST',credentials:'same-origin',body:new FormData(form)})"));
assert.ok(read('public/assets/js/netvera-live-chat.js').includes('element.textContent=message'));
assert.ok(read('public/assets/css/responsive-repair-v19.css').includes('padding-bottom:calc(72px + env(safe-area-inset-bottom, 0px))'));
assert.ok(read('public/assets/css/featured-category-slider-v43.css').includes('.yh18-favorite{position:static;transform:none;'));
(async()=>{
 const {validateFeaturedFilters}=await import('./home-featured-mobile-probe.mjs');
 const r={viewport:[390,844],doc:375,arrows:0,groups:[{box:{top:0,height:44}}],navHeight:134,children:[],rails:[],icons:[],
  cards:[{box:{top:100,bottom:560,left:20,right:331,width:311},badge:{top:89,bottom:110,left:150,right:220},titleBox:{top:150},price:{bottom:500},cta:{top:520,bottom:550,left:38,right:313}}]};
 assert.ok(validateFeaturedFilters(r).includes('Package badge clipped or overlapping title'));
 r.cards[0].badge={top:120,bottom:138,left:235,right:313};assert.deepEqual(validateFeaturedFilters(r),[]);
 console.log('PASS: clipped badge regression caught; card-contained badge, protected CTA, reserved mobile dock, shared chat toggle/close/Escape and unchanged safe transport.');
})().catch(error=>{console.error(error);process.exitCode=1;});
