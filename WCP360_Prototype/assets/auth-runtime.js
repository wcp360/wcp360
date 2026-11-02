(()=>{
  'use strict';
  const page=(location.pathname.split('/').pop()||'').toLowerCase();
  if(!['login.html','mfa.html'].includes(page))return;
  const form=document.querySelector('[data-auth-form]');if(!form)return;
  const key='wcp360-auth-portal',targets={user:'canonical-user-dashboard.html',reseller:'reseller.html',root:'canonical-admin-dashboard.html'};
  const validPortal=value=>Object.hasOwn(targets,value)?value:'user';
  let selected='user';try{selected=validPortal(localStorage.getItem(key));}catch(e){}
  const query=new URLSearchParams(location.search);
  if(query.has('portal'))selected=validPortal(query.get('portal'));
  // Discard historical GET credentials when old bookmarked entry URLs are opened.
  if(location.search){try{history.replaceState(null,'',location.pathname+(query.has('portal')?'?portal='+selected:'')+location.hash);}catch(e){}}
  const feedback=form.querySelector('[data-auth-feedback]');
  const showError=(field,message)=>{feedback.textContent=message;feedback.hidden=false;field.setAttribute('aria-invalid','true');field.focus();};
  const clearError=()=>{feedback.hidden=true;feedback.textContent='';form.querySelectorAll('[aria-invalid]').forEach(field=>field.removeAttribute('aria-invalid'));};
  form.addEventListener('input',clearError);
  if(page==='login.html'){
    form.insertAdjacentHTML('beforebegin','<div class="auth-portals" role="group" aria-label="Choisir un portail de démonstration"><button class="auth-portal" type="button" data-portal="user"><strong>USER</strong><small>Sites et services</small></button><button class="auth-portal" type="button" data-portal="reseller"><strong>RESELLER</strong><small>Clients et offres</small></button><button class="auth-portal" type="button" data-portal="root"><strong>ROOT</strong><small>Infrastructure</small></button></div>');
    const select=portal=>{selected=validPortal(portal);document.querySelectorAll('[data-portal]').forEach(button=>{const active=button.dataset.portal===selected;button.classList.toggle('active',active);button.setAttribute('aria-pressed',String(active));});form.action='mfa.html?portal='+selected;try{localStorage.setItem(key,selected);}catch(e){}};
    document.querySelectorAll('[data-portal]').forEach(button=>button.addEventListener('click',()=>select(button.dataset.portal)));select(selected);
    form.addEventListener('submit',event=>{
      event.preventDefault();clearError();if(!form.reportValidity())return;
      const identity=document.querySelector('#login-identity'),password=document.querySelector('#login-password');
      if(identity.value.trim().toLowerCase()!=='admin@example.com'){showError(identity,'Utilisez l’adresse fictive admin@example.com.');return;}
      if(password.value!=='demo-wcp360'){showError(password,'Utilisez le mot de passe fictif demo-wcp360.');return;}
      password.value='';location.assign('mfa.html?portal='+selected);
    });
  }else{
    form.action=targets[selected];
    form.insertAdjacentHTML('beforebegin',`<p class="auth-context">Portail <strong>WCP360 ${selected.toUpperCase()}</strong> · étape simulée</p>`);
    const code=document.querySelector('#mfa-code'),toggle=document.querySelector('[data-auth-recovery]');let recovery=false;
    toggle.addEventListener('click',()=>{
      recovery=!recovery;clearError();code.value='';code.inputMode=recovery?'text':'numeric';code.pattern=recovery?'DEMO-RECOVERY':'[0-9]{6}';code.minLength=recovery?13:6;code.maxLength=recovery?13:6;code.placeholder=recovery?'DEMO-RECOVERY':'123456';
      document.querySelector('[data-auth-code-label]').textContent=recovery?'Code de récupération de démonstration':'Code de démonstration à 6 chiffres';
      document.querySelector('[data-auth-code-help]').textContent=recovery?'Saisissez DEMO-RECOVERY pour continuer.':'Saisissez 123456 pour continuer.';
      toggle.textContent=recovery?'Utiliser le code à 6 chiffres':'Utiliser le code de récupération';toggle.setAttribute('aria-pressed',String(recovery));code.focus();
    });
    form.addEventListener('submit',event=>{
      event.preventDefault();clearError();if(!form.reportValidity())return;
      if(code.value!==(recovery?'DEMO-RECOVERY':'123456')){showError(code,recovery?'Utilisez le code fictif DEMO-RECOVERY.':'Utilisez le code fictif 123456.');return;}
      code.value='';location.assign(targets[selected]);
    });
  }
  form.querySelector('[type="submit"]').disabled=false;
})();
