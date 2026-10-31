(()=>{
  'use strict';
  const page=(location.pathname.split('/').pop()||'').toLowerCase(),key='wcp360-auth-portal';
  if(page==='login.html'){
    const form=document.querySelector('form');if(!form)return;let selected='user';try{selected=localStorage.getItem(key)||'user'}catch(e){}
    form.insertAdjacentHTML('beforebegin','<div class="auth-portals" role="group" aria-label="Select WCP360 portal"><button class="auth-portal" type="button" data-portal="user"><span class="material-symbols-rounded">web</span><span><strong>WCP360 USER</strong><small>Sites, mail and applications</small></span></button><button class="auth-portal" type="button" data-portal="root"><span class="material-symbols-rounded">admin_panel_settings</span><span><strong>WCP360 ROOT</strong><small>Infrastructure administration</small></span></button></div>');
    const select=portal=>{selected=portal;document.querySelectorAll('[data-portal]').forEach(button=>button.classList.toggle('active',button.dataset.portal===portal));try{localStorage.setItem(key,portal)}catch(e){}};document.querySelectorAll('[data-portal]').forEach(button=>button.addEventListener('click',()=>select(button.dataset.portal)));select(selected);
  }
  if(page==='mfa.html'){
    const form=document.querySelector('form');if(!form)return;let portal='user';try{portal=localStorage.getItem(key)||'user'}catch(e){}form.action=portal==='root'?'canonical-admin-dashboard.html':'canonical-user-dashboard.html';form.insertAdjacentHTML('beforebegin',`<div class="auth-context">Signing in to <strong>WCP360 ${portal.toUpperCase()}</strong> · MFA policy enforced</div>`);
  }
})();
