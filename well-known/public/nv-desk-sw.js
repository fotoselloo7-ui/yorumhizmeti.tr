/* Offline assets only. NEVER cache authenticated pages, private API data, replies or orders. */
const VERSION='nv-desk-shell-v1';
const ASSETS=['/assets/css/nv-desk.css?v=1','/assets/js/nv-desk.js?v=1','/assets/js/nv-desk-alerts.js?v=1','/assets/img/nv-desk-icon.svg'];
self.addEventListener('install',event=>{
  event.waitUntil(caches.open(VERSION).then(cache=>cache.addAll(ASSETS)).then(()=>self.skipWaiting()));
});
self.addEventListener('activate',event=>{
  event.waitUntil(caches.keys().then(keys=>Promise.all(keys.filter(k=>k!==VERSION).map(k=>caches.delete(k)))).then(()=>self.clients.claim()));
});
self.addEventListener('fetch',event=>{
  const url=new URL(event.request.url);
  if(event.request.method!=='GET'||url.origin!==self.location.origin)return;
  if(!url.pathname.startsWith('/assets/'))return;
  if(!ASSETS.some(asset=>asset.split('?')[0]===url.pathname))return;
  event.respondWith(caches.match(event.request).then(hit=>hit||fetch(event.request)));
});
