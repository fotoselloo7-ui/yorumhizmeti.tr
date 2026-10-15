/** Read-only geometry check for the two adjacent home sections. */
export function measureHomeSoftwareFrame() {
  const rect=s=>document.querySelector(s)?.getBoundingClientRect().toJSON();
  const section=document.querySelector('.nv31-software');
  const area=section.getBoundingClientRect();
  return {
    viewport:[innerWidth,innerHeight],client:document.documentElement.clientWidth,scrollWidth:document.documentElement.scrollWidth,
    header:rect('.site-header>.container'),footer:rect('.site-footer>.container'),
    expertise:rect('.nv29-services>.nv29-container'),software:rect('.nv31-software>.nv31-container'),
    section:{x:area.x,y:area.y+scrollY,width:area.width,height:area.height},
    clips:[...section.querySelectorAll('h2,h3,p,a')].filter(e=>e.scrollWidth>e.clientWidth+3).map(e=>e.className),
    cards:[...section.querySelectorAll('.nv31-software-card')].map(e=>{
      const cover=e.querySelector('.nv31-software-image'),image=cover.querySelector('img'),c=cover.getBoundingClientRect();
      return {box:e.getBoundingClientRect().toJSON(),cover:c.toJSON(),ratio:c.width/c.height,loaded:image?.complete&&image.naturalWidth>0};
    })
  };
}
export function validateHomeSoftwareFrame(r) {
  const problems=[];
  if(!r.expertise||!r.software) return ['Adjacent frame missing'];
  if(Math.abs(r.expertise.x-r.software.x)>1||Math.abs(r.expertise.width-r.software.width)>1)problems.push('Expertise/software alignment differs');
  if(r.scrollWidth>r.client+2)problems.push('Page horizontal overflow');
  if(r.viewport[0]>=1280&&(Math.abs(r.header.width-1200)>1||Math.abs(r.footer.width-1200)>1))problems.push('General page frame changed');
  if(r.clips.length)problems.push('Showcase text clips: '+r.clips.join(','));
  if(r.cards.some(c=>Math.abs(c.ratio-1.6)>.02))problems.push('Cover proportions lost');
  return problems;
}
