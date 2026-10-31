(() => {
  'use strict';
  const normalize = value => String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
  const aliases = {
    sauvegarde:['backup'],sauvegardes:['backup'],restaurer:['restore'],restauration:['restore'],
    fichier:['file'],fichiers:['file'],messagerie:['mail','email'],courriel:['mail','email'],
    securite:['security'],domaine:['domain'],domaines:['domain'],tache:['job','task'],taches:['job','task'],
    compte:['account'],comptes:['account'],reseau:['network'],certificat:['certificate','tls','ssl'],certificats:['certificate','tls','ssl'],
    installation:['setup','installer'],installer:['setup','installer'],extension:['plugin','extension'],extensions:['plugin','extension'],
    utilisateur:['user'],utilisateurs:['user'],revendeur:['reseller'],revendeurs:['reseller'],journal:['log','audit'],journaux:['log','audit']
  };
  function matches(item, query) {
    const haystack = normalize([item.label,item.category,item.keywords,item.href].join(' '));
    return normalize(query).split(/\s+/).filter(Boolean).every(word => [word,...(Object.hasOwn(aliases,word) ? aliases[word] : [])].some(term => haystack.includes(term)));
  }
  window.WCPPrototypeSearch = Object.freeze({matches});
})();
