/* Progressive enhancement: keep desktop filters visible and mobile compact. */
document.addEventListener('DOMContentLoaded',function(){
  const sidebar=document.querySelector('.nv36-sidebar');
  if(!sidebar)return;
  const toggle=sidebar.querySelector('[data-software-filter-toggle]');
  toggle?.addEventListener('click',function(){
    const open=sidebar.classList.toggle('nv36-open');
    toggle.setAttribute('aria-expanded',String(open));
    toggle.lastElementChild?.classList?.toggle('nv36-arrow-open',open);
  });
  const sort=document.querySelector('[data-software-sort]');
  sort?.addEventListener('change',function(){
    if(sort.form?.requestSubmit) sort.form.requestSubmit();
    else sort.form?.submit();
  });
});
