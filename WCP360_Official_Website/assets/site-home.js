(()=>{
  const toggle=document.querySelector('.client-logo-toggle');
  const viewport=document.querySelector('.client-logo-viewport');
  const track=document.querySelector('#client-logo-track');

  const prepareLoop=()=>{
    if(!track||track.dataset.loopReady==='true') return;
    const sourceGroup=track.querySelector('.client-logo-group');
    if(!sourceGroup) return;
    const clone=sourceGroup.cloneNode(true);
    clone.setAttribute('aria-hidden','true');
    clone.querySelectorAll('img').forEach(image=>{
      image.alt='';
      image.loading='lazy';
      image.fetchPriority='low';
    });
    track.append(clone);
    track.dataset.loopReady='true';
    requestAnimationFrame(()=>track.classList.add('is-ready'));
  };

  if(track){
    if('IntersectionObserver' in window){
      const observer=new IntersectionObserver(entries=>{
        if(!entries.some(entry=>entry.isIntersecting)) return;
        prepareLoop();
        observer.disconnect();
      },{rootMargin:'100px 0px'});
      observer.observe(viewport||track);
    }else{
      prepareLoop();
    }
  }

  toggle?.addEventListener('click',()=>{
    if(!viewport) return;
    const paused=!viewport.classList.contains('is-paused');
    viewport.classList.toggle('is-paused',paused);
    toggle.setAttribute('aria-pressed',paused?'true':'false');
    toggle.setAttribute('aria-label',paused?'Relancer le défilement des logos':'Mettre le défilement des logos en pause');
  });
})();
