/** Read-only browser probe. Pass measureUI to page.evaluate after page load.
 * Complements screenshots; intentional carousels/table scroll are not page overflow.
 * Absolute/fixed collisions and image sharpness still require visual inspection.
 */
export function measureUI() {
  const clipped = element => {
    for (let parent=element; parent && parent!==document.body && parent!==document.documentElement; parent=parent.parentElement) {
      const style=getComputedStyle(parent);
      if (parent!==element && ['auto','scroll'].includes(style.overflowX)) return true;
      if (parent.matches('[aria-hidden="true"],.yh49-marquee-row,.nv69-review-viewport,.nv26-menu-shell:not(.open),.admin-sidebar:not(.open)')) return true;
      if (style.opacity==='0') return true;
    }
    return false;
  };
  return {
    url:location.pathname,
    viewport:[innerWidth,innerHeight],
    doc:document.documentElement.scrollWidth,
    title:document.querySelector('h1')?.textContent.trim(),
    overflow:[...document.querySelectorAll('body *')].filter(element=>{
      const rect=element.getBoundingClientRect(),style=getComputedStyle(element);
      return rect.width>3 && rect.height>3 && style.visibility!=='hidden' && style.display!=='none' && style.opacity!=='0'
        && (rect.right>innerWidth+2 || rect.left < -2) && !clipped(element)
        && !['absolute','fixed'].includes(style.position) && !element.closest('[hidden]');
    }).slice(0,12).map(element=>({
      tag:element.tagName,cls:element.className,text:element.textContent.trim().slice(0,45),
      left:Math.round(element.getBoundingClientRect().left),right:Math.round(element.getBoundingClientRect().right)
    })),
    clippedButtons:[...document.querySelectorAll('button,.btn')].filter(element=>
      !element.matches('.btn-cart') && element.getBoundingClientRect().width>0 && element.scrollWidth>element.clientWidth+3
    ).slice(0,8).map(element=>({cls:element.className,text:element.textContent.trim().slice(0,45)})),
    clippedHeadings:[...document.querySelectorAll('h1,h2,h3')].filter(element=>
      element.getBoundingClientRect().width>0 && getComputedStyle(element).overflowY==='hidden'
      && getComputedStyle(element).getPropertyValue('-webkit-line-clamp')==='none' && element.scrollHeight>element.clientHeight+4
    ).map(element=>element.className),
    brokenImages:[...document.images].filter(element=>element.complete && element.currentSrc && element.naturalWidth===0).length
  };
}

export function hasLayoutIssues(result) {
  return result.doc>result.viewport[0]+1 || result.overflow.length>0 || result.clippedButtons.length>0
    || result.clippedHeadings.length>0 || result.brokenImages>0;
}
