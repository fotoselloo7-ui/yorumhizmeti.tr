(()=>{
 'use strict';
 const primary=document.querySelector('[data-review-primary]');
 const queue=document.querySelectorAll('#nv68-review-queue template[data-review-template]');
 if(!primary||queue.length<2)return;
 let index=0,busy=false;
 const reduce=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
 const next=()=>{
   if(busy||document.hidden)return;
   busy=true;primary.classList.remove('nv68-fade-in');
   const render=()=>{
      index=(index+1)%queue.length;
      const item=queue[index].content.firstElementChild;
      primary.replaceChildren(...Array.from(item?.cloneNode(true).childNodes||[]));
      primary.classList.remove('nv68-fade-out');
      if(!reduce){primary.classList.add('nv68-fade-in');setTimeout(()=>primary.classList.remove('nv68-fade-in'),540);}
      busy=false;
   };
   if(reduce)render();else {primary.classList.add('nv68-fade-out');setTimeout(render,375)}
 };
 setInterval(next,6200);
})();