const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const root = path.resolve(__dirname, '..');
const read = file => fs.readFileSync(path.join(root, file), 'utf8');
const app = read('public/assets/js/app.js');
const accordion = app.slice(app.indexOf('// ── FAQ Accordion'), app.indexOf("    document.querySelectorAll('.faq-question')"));
function classes(initial = []) {
  const values = new Set(initial);
  return {values, add: (...v) => v.forEach(x => values.add(x)), remove: v => values.delete(v), contains: v => values.has(v), toggle: (v, active) => active ? values.add(v) : values.delete(v)};
}
// Existing IDs and legacy panels without IDs share the same accessible handler.
const questions = Array.from({length: 3}, (_, i) => {
  const attrs = {};
  const panel = {id: i ? '' : 'nv-faq-0', hidden: false, classList: classes(['premium-faq-answer'])};
  return {nextElementSibling: panel, classList: classes(), setAttribute: (k,v) => attrs[k] = v, getAttribute: k => attrs[k], addEventListener: (type, fn) => {assert.equal(type, 'click'); panel.click = fn;}};
});
vm.runInNewContext(accordion, {document: {querySelectorAll: () => questions, getElementById: id => questions.map(q => q.nextElementSibling).find(p => p.id === id)}});
for (const q of questions) {
  assert.equal(q.getAttribute('aria-expanded'), 'false');
  assert.equal(q.getAttribute('aria-controls'), q.nextElementSibling.id);
  assert.equal(q.nextElementSibling.hidden, true);
  q.nextElementSibling.click();
  assert.equal(q.getAttribute('aria-expanded'), 'true');
  assert.equal(q.nextElementSibling.hidden, false);
  q.nextElementSibling.click();
  assert.equal(q.nextElementSibling.hidden, true);
}
const motion = app.slice(app.indexOf('// One entrance observer'), app.indexOf('// ── Checkout Pre-fill'));
for (const [reduce, supported] of [[false,true],[true,true],[false,false]]) {
  const elements = [{classList: classes()}, {classList: classes()}];
  let observer, listener;
  const preference = {matches: reduce, addEventListener: (type, fn) => {assert.equal(type, 'change'); listener = fn;}};
  class Observer {
    constructor(callback, options) {this.callback = callback; this.options = options; this.observed = []; observer = this;}
    observe(el) {this.observed.push(el);}
    unobserve(el) {this.observed = this.observed.filter(x => x !== el);}
    disconnect() {this.observed = [];}
  }
  const window = {matchMedia: () => preference};
  if (supported) window.IntersectionObserver = Observer;
  vm.runInNewContext(motion, {window, IntersectionObserver: Observer, document: {querySelectorAll: () => elements}});
  if (!reduce && supported) {
    assert.equal(observer.observed.length, 2);
    observer.callback([{isIntersecting: true, target: elements[0]}]);
    assert.equal(elements[0].classList.contains('nv-motion-enter'), true);
    assert.equal(observer.observed.length, 1);
    listener({matches: true});
    assert.equal(observer.observed.length, 0);
    assert.equal(elements[0].classList.contains('nv-motion-enter'), false);
  } else {
    assert.equal(observer, undefined);
    assert(elements.every(el => el.classList.contains('visible')));
  }
}
for (const file of ['premium.css','storefront-v4.css','approved-v9.css','visual-consistency-v10.css','visual-fidelity-v11.css','typography-readability-v16.css']) {
  assert(!/\.premium-faq-(?:item|question|answer)[^{]*\{[^}]*!important/.test(read('public/assets/css/' + file)), 'Retired FAQ override: ' + file);
}
for (const file of ['category-detail.php','blog/detail.php','package-detail.php']) {
  assert(!read('resources/views/frontend/' + file).includes("document.querySelectorAll('.premium-faq-question')"), 'Duplicate inline accordion: ' + file);
}
const fadeRule = read('public/assets/css/app.css').match(/\.fade-in-up\s*\{([^}]*)\}/)[1];
assert(fadeRule.includes('opacity: 1;') && !fadeRule.includes('opacity: 0') && !fadeRule.includes('translate'));
assert(read('public/assets/css/motion-v22.css').includes('.nv-motion-enter{animation:none}'));
assert(!read('resources/views/frontend/blog/detail.php').includes('font-size:9px'));
const blog = read('resources/views/frontend/blog/index.php');
assert(!blog.includes('24 yorum') && !blog.includes('yv-blog-featured-overlay-v12') && !blog.includes('yv-blog-post-brand-v12'));
assert(blog.includes('aria-label="Blog yazılarında ara"'));
assert(read('resources/views/frontend/partials/netvera-live-chat.php').includes('data-nv-chat-toggle aria-label="Canlı Destek"'));
const contact = read('resources/views/frontend/contact.php');
for (const name of ['name','email','subject','message']) {
  assert(contact.includes('for="contact-' + name + '"') && contact.includes('id="contact-' + name + '"'));
}
console.log('PASS: shared FAQ open/close and ARIA, existing/generated IDs, one-shot entrance, reduced motion/live preference, no-observer fallback, retired overrides and inline owners');
