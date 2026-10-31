(()=>{
  'use strict';
  const cards=[...document.querySelectorAll('[data-app]')];
  const filters=[...document.querySelectorAll('[data-app-filter]')];
  const search=document.querySelector('[data-app-search]');
  const empty=document.querySelector('[data-app-empty]');
  const plan=document.querySelector('[data-app-plan]');
  const result=document.querySelector('[data-plan-result]');
  let active='all';
  const applyFilters=()=>{
    const query=(search?.value||'').trim().toLowerCase();
    let visible=0;
    cards.forEach(card=>{
      const categories=(card.dataset.category||'').split(' ');
      const haystack=(card.dataset.name+' '+card.textContent).toLowerCase();
      const show=(active==='all'||categories.includes(active))&&(!query||haystack.includes(query));
      card.hidden=!show;
      if(show)visible+=1;
    });
    if(empty)empty.hidden=visible!==0;
  };
  filters.forEach(button=>button.addEventListener('click',()=>{
    active=button.dataset.appFilter||'all';
    filters.forEach(item=>item.classList.toggle('is-active',item===button));
    applyFilters();
  }));
  search?.addEventListener('input',applyFilters);
  cards.forEach(card=>{
    card.querySelector('[data-app-select]')?.addEventListener('click',()=>{
      document.querySelector('[data-selected-app]').textContent=card.dataset.name||'Application';
      document.querySelector('[data-plan-version]').textContent=card.dataset.version||'Selected recipe';
      document.querySelector('[data-plan-runtime]').textContent=card.dataset.runtime||'Declared runtime';
      document.querySelector('[data-plan-size]').textContent=card.dataset.size||'Calculated at preflight';
      cards.forEach(item=>item.classList.toggle('is-selected',item===card));
      if(result)result.hidden=true;
      document.querySelector('#planner')?.scrollIntoView({behavior:matchMedia('(prefers-reduced-motion: reduce)').matches?'auto':'smooth'});
    });
  });
  plan?.addEventListener('submit',event=>{
    event.preventDefault();
    if(!plan.reportValidity()){
      if(result)result.hidden=true;
      return;
    }
    if(result)result.hidden=false;
  });
  plan?.addEventListener('input',()=>{if(result)result.hidden=true});
})();
