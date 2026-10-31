(()=>{
  const forms=[...document.querySelectorAll('#contactForm,#quoteForm')];
  if(!forms.length) return;
  const detailedQuoteForm=document.querySelector('#quoteForm[action*="devis.php"]');
  if(detailedQuoteForm){
    try{
      const draftKey='nettoyage360-quote-draft';
      const draft=JSON.parse(sessionStorage.getItem(draftKey)||'null');
      if(draft && typeof draft==='object'){
        const normalize=value=>String(value||'')
          .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
          .toLowerCase().replace(/[’']/g,' ').replace(/[^a-z0-9]+/g,' ').trim();
        const selectByLabel=(selector,label,aliases={})=>{
          const select=detailedQuoteForm.querySelector(selector);
          if(!select || !label) return false;
          const source=normalize(label);
          const target=aliases[source]||source;
          const option=[...select.options].find(item=>normalize(item.textContent)===target);
          if(!option) return false;
          select.value=option.value;
          return true;
        };
        const setValue=(selector,value)=>{
          const field=detailedQuoteForm.querySelector(selector);
          if(field && value) field.value=String(value);
        };
        selectByLabel('#quote-profile',draft.profile,{
          'copropriete':'copropriete syndic'
        });
        const serviceMatched=selectByLabel('#quote-service',draft.service,{
          'nettoyage d appartements':'nettoyage residentiel',
          'nettoyage de maisons':'nettoyage residentiel',
          'commerces boutiques':'nettoyage commercial',
          'cabinets medicaux':'nettoyage locaux professionnels',
          'parties communes':'nettoyage de copropriete immeuble',
          'sortie rentree des poubelles':'poubelles conteneurs',
          'nettoyage de parkings':'parkings sous sols',
          'shampouinage moquette':'shampoing moquettes tapis',
          'fin de chantier':'nettoyage apres travaux',
          'canapes tapis et moquettes':'shampoing moquettes tapis',
          'vitres professionnelles':'nettoyage vitres facades',
          'halls et cages d escalier':'nettoyage de copropriete immeuble',
          'ascenseurs':'nettoyage de copropriete immeuble',
          'remise en etat':'remise en etat complete'
        });
        if(draft.service && !serviceMatched) selectByLabel('#quote-service','Autre prestation') || selectByLabel('#quote-service','À définir');
        selectByLabel('#quote-frequency',draft.frequency,{
          '1 fois semaine':'1 fois par semaine',
          '2 a 3 fois semaine':'2 a 3 fois par semaine'
        });
        setValue('#quote-surface',draft.surface);
        setValue('#quote-address',draft.zone);
        setValue('#quote-date',draft.date);
        setValue('#quote-name',draft.name);
        setValue('#quote-phone',draft.phone);
        setValue('#quote-email',draft.email);
        const details=[];
        if(draft.service && !serviceMatched) details.push(`Prestation demandée : ${draft.service}`);
        if(draft.details) details.push(String(draft.details));
        setValue('#quote-details',details.join('\n\n'));
        sessionStorage.removeItem(draftKey);
      }
    }catch(error){
      console.warn('Préremplissage du devis indisponible.',error);
    }
  }
  const status=(form,msg,kind='info')=>{
    const el=form.querySelector('.form-status'); if(!el) return;
    el.textContent=msg; el.classList.add('show'); el.dataset.kind=kind;
  };
  const clearStatus=form=>{
    const el=form.querySelector('.form-status'); if(!el) return;
    el.textContent=''; el.classList.remove('show'); delete el.dataset.kind;
  };
  const normalizeFrenchPhone=input=>{
    let digits=input.value.replace(/\D/g,'');
    if(digits.startsWith('0033')) digits=digits.slice(4);
    else if(digits.startsWith('33')) digits=digits.slice(2);
    else if(digits.startsWith('0')) digits=digits.slice(1);
    const valid=/^[1-9]\d{8}$/.test(digits);
    input.setCustomValidity(valid?'':"Saisissez un numéro français valide à 10 chiffres, par exemple 06 12 34 56 78.");
    if(valid) input.value=`+33 ${digits.replace(/(\d)(?=(\d{2})+$)/g,'$1 ')}`;
    return valid;
  };
  forms.forEach(form=>{
    const phone=form.querySelector('input[name="phone"]');
    form.querySelectorAll('input,textarea,select').forEach(field=>field.addEventListener('blur',()=>{
      if(String(field.value||'').trim()!=='') field.classList.add('touched');
    }));
    if(!phone) return;
    phone.addEventListener('input',()=>phone.setCustomValidity(''));
    phone.addEventListener('blur',()=>{if(phone.value.trim()) normalizeFrenchPhone(phone);});
  });
  let csrfRequest=null;
  const loadCsrf=()=>{
    if(csrfRequest) return csrfRequest;
    csrfRequest=fetch(new URL('api/csrf.php',document.baseURI),{
      credentials:'same-origin',cache:'no-store',headers:{'Accept':'application/json'}
    })
      .then(r=>{if(!r.ok) throw new Error(`csrf:${r.status}`); return r.json()})
      .then(data=>{
        if(!data.token) throw new Error('csrf:token');
        forms.forEach(form=>{
          const input=form.querySelector('[data-csrf]');
          if(input) input.value=data.token;
          clearStatus(form);
        });
        return data.token;
      })
      .finally(()=>{csrfRequest=null;});
    return csrfRequest;
  };
  // Préchargement silencieux : un incident réseau ne doit pas afficher une
  // erreur avant que l’utilisateur ait tenté d’envoyer le formulaire.
  // L’envoi relance cette requête et affiche alors une aide actionnable.
  loadCsrf().catch(()=>{});
  forms.forEach(form=>form.addEventListener('submit',async e=>{
    e.preventDefault();
    const phone=form.querySelector('input[name="phone"]');
    if(phone && !normalizeFrenchPhone(phone)){phone.reportValidity();return;}
    if(!form.checkValidity()){
      form.querySelectorAll(':invalid').forEach(field=>field.classList.add('touched'));
      form.reportValidity();return;
    }
    const csrf=form.querySelector('[data-csrf]');
    if(!csrf) return;
    if(!csrf.value){
      status(form,"Initialisation sécurisée du formulaire…",'info');
      try{await loadCsrf();}
      catch(error){status(form,"Le formulaire ne peut pas joindre le service d'envoi. Rechargez la page puis réessayez, ou appelez-nous au 01 88 61 05 90.",'error');return;}
    }
    const btn=form.querySelector('button[type="submit"]'); if(btn){btn.disabled=true;btn.dataset.old=btn.textContent;btn.textContent='Envoi en cours…';}
    HTMLFormElement.prototype.submit.call(form);
  }));
})();
