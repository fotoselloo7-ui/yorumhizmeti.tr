'use strict';
// Exercise the actual switcher script with synthetic categories; no DB writes.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const root = path.resolve(__dirname, '..');
const source = fs.readFileSync(path.join(root, 'public/assets/js/home-featured-tabs-v18.js'), 'utf8');
function element(dataset = {}, active = false) {
  const classes = new Set(active ? ['active'] : []);
  return {dataset, hidden:false, attributes:{}, handlers:{}, scrollLeft:0,
    classList:{contains:k=>classes.has(k), add:(...keys)=>keys.forEach(k=>classes.add(k)),
      remove:(...keys)=>keys.forEach(k=>classes.delete(k)),
      toggle(k,on){if(on)classes.add(k);else classes.delete(k);}},
    setAttribute(k,v){this.attributes[k]=v;},
    addEventListener(k,f){(this.handlers[k]??=[]).push(f);},
    emit(k,event={}){for(const f of this.handlers[k]||[])f(event);},
    click(){this.emit('click');}, focus(){}, blur(){},
    querySelector:()=>null, querySelectorAll:()=>[], matches:()=>false};
}
function boot({mobile=true, search='', hash='', initial='1', pathname='/', saved=null}={}) {
  const definitions=[['1','marketing','google'],['2','social','instagram'],['3','social','tiktok'],['4','agency','web'],['11','marketing','google-harita','1']];
  const tabs=definitions.map(([id,group,slug,parent])=>element({featuredTab:id,featuredParentGroup:group,
    featuredRootId:parent||id,...(parent?{featuredParentId:parent}:{}),title:slug,url:'/kategori/'+slug},id===initial));
  const panes=tabs.map(t=>{const p=element({featuredPane:t.dataset.featuredTab});p.hidden=t.dataset.featuredTab!==initial;p.querySelector=q=>q==='.yh18-featured-card'?{}:null;return p;});
  const groups=['social','agency','marketing'].map(key=>element({featuredGroup:key}));
  const panels=groups.map(g=>{const p=element({featuredFilterPanel:g.dataset.featuredGroup});p.querySelector=()=>tabs.find(t=>t.dataset.featuredParentGroup===g.dataset.featuredGroup);return p;});
  const children=[element({featuredChildrenFor:'1'})];
  children[0].closest=()=>panels[2];
  const all=[...groups.map(g=>element({featuredAll:g.dataset.featuredGroup})),element({featuredParentAll:'1'})];
  const switcher=element({featuredDefaultCategory:'1'});
  const resultTrack=element();
  switcher.querySelector=q=>q==='.yh18-featured-panes'?resultTrack:null;
  switcher.querySelectorAll=q=>({'[data-featured-tab]':tabs,'[data-featured-group]':groups,
    '[data-featured-filter-panel]':panels,'[data-featured-pane]':panes,
    '[data-featured-children-for]':children,'[data-featured-rail]':[],
    '[data-featured-all], [data-featured-parent-all]':all}[q]||[]);
  const mobileMedia={matches:mobile,addEventListener(k,f){this.change=f;}};
  const storage={value:saved, getItem(){return this.value;},setItem(k,v){this.value=v;}};
  const window={sessionStorage:storage,location:{search,hash,pathname},matchMedia:q=>q.includes('max-width')?mobileMedia:{matches:true,addEventListener(){}},addEventListener(){},setInterval(){}};
  vm.runInNewContext(source,{window,document:{querySelector:q=>q==='[data-featured-switcher]'?switcher:null},
    URLSearchParams,requestAnimationFrame:f=>f(),setTimeout:()=>0,clearTimeout(){},setInterval(){},clearInterval(){}});
  return {tabs,panes,groups,panels,children,all,switcher,mobileMedia,storage,
    visible:()=>panes.filter(p=>!p.hidden).map(p=>p.dataset.featuredPane)};
}
let s=boot();assert.deepEqual(s.visible(),['2','3']);assert.equal(s.all[0].attributes['aria-selected'],'true');
s.groups[1].emit('focus');assert.deepEqual(s.visible(),['2','3']);
s.groups[2].emit('pointerenter',{pointerType:'mouse'});assert.deepEqual(s.visible(),['2','3']);
s.groups[1].click();assert.deepEqual(s.visible(),['4']);assert.equal(s.switcher.classList.contains('is-group-results'),true);
s.tabs[3].click();assert.deepEqual(s.visible(),['4']);assert.equal(s.switcher.classList.contains('is-group-results'),false);assert.equal(s.children[0].hidden,true);
s=boot({search:'?subcategory=google-harita'});assert.deepEqual(s.visible(),['11']);assert.equal(s.children[0].hidden,false);
s.all[3].click();assert.deepEqual(s.visible(),['1']);assert.equal(s.all[3].attributes['aria-selected'],'true');
s.mobileMedia.matches=false;s.mobileMedia.change();assert.deepEqual(s.visible(),['1']);
s=boot({initial:'4'});assert.deepEqual(s.visible(),['4']);
s=boot({search:'?category_id=4'});assert.deepEqual(s.visible(),['4']);
s=boot({hash:'#google-harita'});assert.deepEqual(s.visible(),['11']);
s=boot({search:'?group=agency'});assert.deepEqual(s.visible(),['4']);
s=boot({search:'?category=unknown'});assert.deepEqual(s.visible(),['1']);
s=boot({mobile:false});assert.deepEqual(s.visible(),['1']);
s.groups[0].emit('pointerenter',{pointerType:'mouse'});assert.deepEqual(s.visible(),['2']);
s=boot();s.mobileMedia.matches=false;s.mobileMedia.change();assert.deepEqual(s.visible(),['2']);
s=boot();s.tabs[3].click();assert.deepEqual(boot({saved:s.storage.value}).visible(),['4']);
assert.deepEqual(boot({saved:s.storage.value,search:'?subcategory=google-harita'}).visible(),['11']);
assert.deepEqual(boot({saved:s.storage.value,mobile:false}).visible(),['1']);
assert.deepEqual(boot({saved:'invalid'}).visible(),['2','3']);
assert.deepEqual(boot({hash:'#%broken'}).visible(),['1']);
const css=fs.readFileSync(path.join(root,'public/assets/css/featured-category-slider-v43.css'),'utf8');
assert.ok(css.includes('@media(max-width:768px)'));
assert.ok(css.includes('scrollbar-width:none'));
assert.ok(!source.includes('cloneNode(true)') || source.includes('avatar.replaceChildren')); // only the existing header icon may be cloned
console.log('PASS: mobile defaults, explicit query/hash/server state, group/category/child/Tümü, hover isolation and desktop resize; synthetic data only.');
