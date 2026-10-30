
document.addEventListener('DOMContentLoaded',()=>{
  document.querySelectorAll('[data-nav]').forEach(a=>{
    if(location.pathname.endsWith(a.getAttribute('href'))) a.classList.add('active');
  });
  const search=document.getElementById('globalSearch');
  if(search){
    search.addEventListener('keydown',e=>{
      if(e.key==='Enter'){
        const q=search.value.trim();
        if(q){
          const commandInput=document.getElementById('wcpCommandInput')||document.getElementById('commandInput');
          if(commandInput){
            commandInput.value=q;
            commandInput.dispatchEvent(new Event('input',{bubbles:true}));
            document.querySelector('[data-open-command]')?.click();
          }else{
            document.dispatchEvent(new CustomEvent('wcp:notification',{detail:{title:'Search ready',detail:`Results for “${q}” are available in the command center.`}}));
          }
        }
      }
    });
  }
});
function openModal(id){document.getElementById(id)?.classList.add('open')}
function closeModal(id){document.getElementById(id)?.classList.remove('open')}
function toggleDrawer(id){document.getElementById(id)?.classList.toggle('open')}
document.addEventListener('keydown',e=>{
  if((e.metaKey||e.ctrlKey)&&e.key.toLowerCase()==='k'){
    // reference-ui.js owns the canonical command center when it is present.
    if(document.getElementById('wcpCommandBackdrop'))return;
    e.preventDefault();
    openModal('commandPalette');
    setTimeout(()=>document.getElementById('commandInput')?.focus(),50);
  }
  if(e.key==='Escape'){
    document.querySelectorAll('.modalback.open').forEach(x=>x.classList.remove('open'));
    document.querySelectorAll('.drawer.open').forEach(x=>x.classList.remove('open'));
  }
});

function applyTheme(theme){
  document.documentElement.setAttribute('data-theme',theme);
  try{localStorage.setItem('wcp360-theme',theme)}catch(e){}
  document.querySelectorAll('[data-theme-choice]').forEach(btn=>{
    btn.classList.toggle('active',btn.dataset.themeChoice===theme);
  });
}
(function(){
  let t='system';
  try{t=localStorage.getItem('wcp360-theme')||'system'}catch(e){}
  applyTheme(t);
})();
document.addEventListener('click',e=>{
  const b=e.target.closest('[data-theme-choice]');
  if(b) applyTheme(b.dataset.themeChoice);
});

// V2.5.3.7 — Cockpit interactions
function openCommandCenter(){
  const p=document.getElementById('commandCenter');
  if(!p)return;
  p.classList.add('open');
  const i=p.querySelector('input'); if(i)i.focus();
}
function closeCommandCenter(){
  const p=document.getElementById('commandCenter');
  if(p)p.classList.remove('open');
}
document.addEventListener('keydown',e=>{
  if((e.ctrlKey||e.metaKey)&&e.key.toLowerCase()==='k'&&!document.getElementById('wcpCommandBackdrop')){e.preventDefault();openCommandCenter()}
  if(e.key==='Escape')closeCommandCenter();
});
document.addEventListener('click',e=>{
  if(e.target.closest('[data-open-command]'))openCommandCenter();
  if(e.target.closest('[data-close-command]'))closeCommandCenter();
  const tab=e.target.closest('[data-ops-tab]');
  if(tab){
    document.querySelectorAll('[data-ops-tab]').forEach(x=>x.classList.remove('active'));
    tab.classList.add('active');
  }
  const dock=e.target.closest('[data-toggle-ops]');
  if(dock){
    const d=document.querySelector('.operations-dock');
    if(d)d.classList.toggle('collapsed');
  }
});
