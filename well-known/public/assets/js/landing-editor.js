(() => {
 'use strict';
 document.querySelectorAll('[data-landing-editor]').forEach(form => {
  form.addEventListener('click', event => {
   const button=event.target.closest('[data-move-row]');
   if(!button)return;
   const row=button.closest('[data-content-row]');
   const rows=[...form.querySelectorAll('[data-content-row]')];
   const index=rows.indexOf(row),other=rows[index+(button.dataset.moveRow==='up'?-1:1)];
   if(!other)return;
   const a=[...row.querySelectorAll('input,textarea')],b=[...other.querySelectorAll('input,textarea')];
   a.forEach((field,n)=>{const value=field.value;field.value=b[n].value;b[n].value=value;});
   other.open=true;button.blur();
   other.querySelector('input')?.focus();
   rows.forEach((item,n)=>{const title=item.querySelector('input')?.value||'';item.querySelector('summary').textContent='Bölüm '+(n+1)+(title?' — '+title:'');});
  });
 });
})();
