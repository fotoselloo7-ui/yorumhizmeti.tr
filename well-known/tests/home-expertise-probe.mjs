/** Read-only browser check. Use with page.evaluate(measureExpertise).
 * Screenshots remain necessary for visual quality and illustration framing.
 */
export function measureExpertise() {
  const rect = element => element?.getBoundingClientRect().toJSON();
  const section = document.querySelector('.nv29-services');
  if (!section) return { missing: true };
  const area = rect(section);
  return {
    viewport: [innerWidth, innerHeight],
    doc: document.documentElement.scrollWidth,
    section: { x: area.left, y: area.top + scrollY, width: area.width, height: area.height },
    links: [...section.querySelectorAll('a')].map(a => ({ text: a.textContent.trim(), href: a.getAttribute('href') })),
    cards: [...section.querySelectorAll('.nv29-service-card')].map(card => ({
      key: card.dataset.promoGroup, box: rect(card),
      art: rect(card.querySelector('.nv29-visual')),
      copy: rect(card.querySelector('.nv29-card-content')),
      title: rect(card.querySelector('h3')), desc: rect(card.querySelector('p')),
      cta: rect(card.querySelector('.nv29-card-cta')), footer: rect(card.querySelector('.nv29-service-chips')),
      chips: [...card.querySelectorAll('.nv29-service-chip')].map(a => ({ rect: rect(a), clipped: a.scrollWidth > a.clientWidth + 2 })),
      image: {
        loaded: card.querySelector('img').complete,
        natural: [card.querySelector('img').naturalWidth, card.querySelector('img').naturalHeight],
        fit: getComputedStyle(card.querySelector('img')).objectFit
      },
      overflow: card.scrollWidth > card.clientWidth + 2
    })),
    issues: [...section.querySelectorAll('h2,h3,p,a')].filter(e => e.scrollWidth > e.clientWidth + 3).map(e => e.className)
  };
}

export function validateExpertise(result, expectedLinks) {
  if (result.missing) return ['Missing expertise section'];
  const problems = [];
  if (result.doc > result.viewport[0] + 1) problems.push('Document overflow');
  if (JSON.stringify(result.cards.map(c => c.key)) !== JSON.stringify(['social','agency','marketing'])) problems.push('Service family order changed');
  if (expectedLinks && JSON.stringify(result.links) !== JSON.stringify(expectedLinks)) problems.push('Category links/content changed');
  problems.push(...result.issues.map(x => 'Clipped text: ' + x));
  const rows = [];
  for (const c of result.cards) {
    if (!c.art || !c.copy || !c.title || !c.desc || !c.cta || !c.footer) { problems.push(c.key + ': missing geometry'); continue; }
    // Transparent art and copy share the same body; only the chip/footer region
    // is disjoint. Mobile additionally reserves a clear CTA lane below artwork.
    if (c.art.bottom > c.footer.top + 2 || c.copy.bottom > c.footer.top + 2) problems.push(c.key + ': footer overlap');
    if (result.viewport[0] <= 700 && c.art.bottom > c.cta.top + 2) problems.push(c.key + ': artwork enters mobile CTA lane');
    if (c.title.bottom > c.desc.top + 2 || c.desc.bottom > c.cta.top + 2 || c.cta.bottom > c.footer.top - 10) problems.push(c.key + ': text/CTA overlap');
    if (c.overflow || c.chips.some(a => a.clipped)) problems.push(c.key + ': card/chip overflow');
    if (!c.image.loaded || c.image.fit !== 'contain' || Math.min(...c.image.natural) < 1000) problems.push(c.key + ': incomplete/cropped artwork');
    for (const child of [c.art,c.copy,c.footer,...c.chips.map(a => a.rect)]) {
      if (child.left < c.box.left - 2 || child.right > c.box.right + 2 || child.bottom > c.box.bottom + 2) problems.push(c.key + ': child outside card');
    }
    if (result.viewport[0] <= 700 && c.box.width < result.doc - 42) problems.push(c.key + ': mobile did not reflow');
    let row = rows.find(r => Math.abs(r[0].box.top - c.box.top) < 3);
    if (!row) rows.push(row = []);
    row.push(c);
  }
  for (const row of rows) {
    for (const get of [c => c.cta.top,c => c.footer.top,c => c.box.height]) {
      const values = row.map(get);
      if (Math.max(...values) - Math.min(...values) > 2.5) problems.push('Row CTA/footer/height baseline mismatch');
    }
  }
  return [...new Set(problems)];
}
