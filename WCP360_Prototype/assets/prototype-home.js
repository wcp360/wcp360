(() => {
  'use strict';
  const init = () => {
    const directory = document.querySelector('[data-prototype-directory]');
    if (!directory || !window.WCPNavigation) return;
    const nav = window.WCPNavigation;
    const query = document.querySelector('[data-prototype-search]');
    const categories = document.querySelector('[data-prototype-categories]');
    const entry = document.querySelector('[data-prototype-enter]');
    const portals = {user:'canonical-user-dashboard.html', reseller:'reseller.html', root:'canonical-admin-dashboard.html'};
    let saved; try { saved = localStorage.getItem('wcp360-home-scope'); } catch {}
    let scope = portals[location.hash.slice(1)] ? location.hash.slice(1) : portals[saved] ? saved : 'root';
    let category = '';
    query.value=new URLSearchParams(location.search).get('q')||'';
    const escape = value => String(value).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    function render() {
      const items = nav.entries(scope);
      const term = query.value.trim();
      const matches = item => !term || window.WCPPrototypeSearch.matches(item,term);
      const groups = [...new Set(items.map(item => item.category))];
      categories.innerHTML = ['', ...groups].map(group => `<button type="button" data-prototype-category="${escape(group)}" aria-pressed="${group===category}">${escape(group||'Toutes')}<span>${items.filter(item=>(!group||item.category===group)&&matches(item)).length}</span></button>`).join('');
      const shown = items.filter(item => (!category || item.category===category) && matches(item));
      directory.innerHTML = groups.map(group => {
        const links = shown.filter(item => item.category===group);
        return links.length ? `<section class="prototype-group"><h3>${escape(group)}</h3><div class="prototype-tools">${links.map(item=>`<a class="prototype-tool" href="${escape(item.href)}"><span class="material-symbols-rounded" aria-hidden="true">${item.icon}</span><div><strong>${escape(item.label)}</strong><small>${scope.toUpperCase()} · ${escape(group)}</small></div><span class="material-symbols-rounded" aria-hidden="true">arrow_outward</span></a>`).join('')}</div></section>` : '';
      }).join('');
      document.querySelector('[data-prototype-count]').textContent = `${shown.length} outil${shown.length===1?'':'s'} sur ${items.length} · espace ${scope.toUpperCase()}`;
      document.querySelector('[data-prototype-empty]').hidden = shown.length > 0;
      document.querySelectorAll('[data-prototype-scope]').forEach(button => button.setAttribute('aria-pressed', String(button.dataset.prototypeScope===scope)));
      entry.href = portals[scope];
      entry.innerHTML = `<span class="material-symbols-rounded">arrow_forward</span>Ouvrir ${scope.toUpperCase()}`;
    }
    document.addEventListener('click', event => {
      const portal = event.target.closest('[data-prototype-scope]');
      if (portal) { scope=portal.dataset.prototypeScope; category=''; try { localStorage.setItem('wcp360-home-scope',scope); } catch {} history.replaceState(null,'','#'+scope); render(); }
      const filter = event.target.closest('[data-prototype-category]');
      if (filter) { category=filter.dataset.prototypeCategory; render(); const selected=[...categories.querySelectorAll('button')].find(button=>button.dataset.prototypeCategory===category); selected?.focus(); }
      if (event.target.closest('[data-prototype-clear]')) { category=''; query.value=''; query.dispatchEvent(new Event('input')); query.focus(); }
    });
    query.addEventListener('input',()=>{const url=new URL(location.href);if(query.value.trim())url.searchParams.set('q',query.value.trim());else url.searchParams.delete('q');history.replaceState(null,'',url);render();});
    query.addEventListener('keydown',event=>{if(event.key==='Escape'){query.value='';category='';query.dispatchEvent(new Event('input'));}});
    document.addEventListener('keydown',event=>{if(event.key==='/'&&!event.ctrlKey&&!event.metaKey&&!event.altKey&&!event.target.closest('input,textarea,select,[contenteditable]')){event.preventDefault();query.focus();}});
    window.addEventListener('hashchange',()=>{ if(portals[location.hash.slice(1)]) { scope=location.hash.slice(1); category=''; render(); } });
    window.addEventListener('storage',event=>{if(event.key==='wcp360-platform-software-v1')render();});
    render();
  };
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);else init();
})();
