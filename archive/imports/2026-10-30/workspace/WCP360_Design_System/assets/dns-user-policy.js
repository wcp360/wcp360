(()=>{
  'use strict';
  const DEFAULT_TYPES=['A','AAAA','CNAME','MX','TXT','SRV','CAA'];
  let rootPolicy=null;
  try{const saved=JSON.parse(localStorage.getItem('wcp360-dns-center-v1')||'null');if(saved?.schema===1)rootPolicy=saved}catch(error){}
  const allowed=(rootPolicy?.recordTypes||DEFAULT_TYPES.map(id=>({id,enabled:true}))).filter(item=>item.enabled).map(item=>item.id);
  const policies=rootPolicy?.policies||{};
  const typeSelect=document.querySelector('[data-dns-record-type]');
  if(typeSelect){
    const previous=typeSelect.value;
    typeSelect.innerHTML=allowed.map(type=>`<option value="${type}">${type}</option>`).join('');
    if(allowed.includes(previous))typeSelect.value=previous;
    const help=typeSelect.closest('.wcp-field')?.querySelector('.wcp-field-help');
    if(help)help.textContent=`Effective types: ${allowed.join(', ')}. ROOT sets the ceiling; reseller and plan policy may narrow it.`;
  }
  const zoneHeading=document.querySelector('.wcp-page-heading');
  if(zoneHeading&&/example\.com/i.test(zoneHeading.textContent||'')){
    const safeCallout=[...document.querySelectorAll('.wcp-callout')].find(item=>/Safe DNS changes/i.test(item.textContent||''));
    if(safeCallout&&!document.querySelector('[data-dns-effective-policy]')){
      const callout=document.createElement('div');callout.className='wcp-callout info';callout.dataset.dnsEffectivePolicy='';callout.style.marginTop='14px';
      callout.innerHTML=`<div class="wcp-callout-title">Effective account DNS policy</div><div class="wcp-callout-text">Allowed record types: <strong>${allowed.join(', ')}</strong>. Minimum TTL: <strong>${policies.minimumTtl||'60 seconds'}</strong>. DNSSEC: <strong>${policies.dnssec||'Platform policy'}</strong>. Infrastructure, signing keys and providers remain ROOT-owned.</div>`;
      safeCallout.before(callout);
    }
  }
})();
