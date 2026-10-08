(() => {
 'use strict';
 document.querySelectorAll('[data-auth-toggle]').forEach(button=>{
   const input=document.getElementById(button.dataset.authToggle);
   if(!input)return;
   button.addEventListener('click',()=>{
     const shown=input.type==='password';
     input.type=shown?'text':'password';
     button.setAttribute('aria-pressed',String(shown));
     button.setAttribute('aria-label',shown?'Şifreyi gizle':'Şifreyi göster');
     button.classList.toggle('is-visible',shown);
   });
 });
 const form=document.querySelector('[data-register-form]');
 if(!form)return;
 const pwd=form.querySelector('[name=password]'),confirm=form.querySelector('[name=password_confirmation]');
 if(!pwd||!confirm)return;
 const validate=()=>confirm.setCustomValidity(confirm.value!==''&&pwd.value!==confirm.value?'Şifreler eşleşmiyor.':'');
 pwd.addEventListener('input',validate);
 confirm.addEventListener('input',validate);
 form.addEventListener('submit',validate);
})();
