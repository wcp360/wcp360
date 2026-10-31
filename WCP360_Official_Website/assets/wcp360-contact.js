(()=>{
  'use strict';
  const form=document.querySelector('[data-contact-form]');
  const topic=document.querySelector('[data-contact-topic]');
  const result=document.querySelector('[data-contact-result]');
  document.querySelectorAll('[data-contact-route]').forEach(link=>{
    link.addEventListener('click',()=>{
      if(topic)topic.value=link.dataset.contactRoute||'';
    });
  });
  if(!form)return;
  form.addEventListener('submit',event=>{
    event.preventDefault();
    if(!form.reportValidity()){
      if(result)result.hidden=true;
      return;
    }
    if(result){
      result.hidden=false;
      result.scrollIntoView({behavior:matchMedia('(prefers-reduced-motion: reduce)').matches?'auto':'smooth',block:'nearest'});
    }
  });
  form.addEventListener('input',()=>{if(result)result.hidden=true});
})();
