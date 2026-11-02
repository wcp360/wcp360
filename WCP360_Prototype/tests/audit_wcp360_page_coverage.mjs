import {readFileSync, readdirSync, existsSync, writeFileSync} from 'node:fs';
import {resolve, join, relative} from 'node:path';
import {fileURLToPath} from 'node:url';
import vm from 'node:vm';

// This inventory deliberately does not infer working UI from an existing script.
// --write refreshes the two review artifacts; the normal test runner is read-only.
const root=fileURLToPath(new URL('../',import.meta.url));
const read=name=>readFileSync(join(root,name),'utf8');
const pages=readdirSync(root).filter(name=>name.endsWith('.html')).sort();
const tests=readdirSync(join(root,'tests')).filter(name=>/^audit_wcp360_.*\.mjs$/.test(name)&&!name.includes('page_coverage')).sort();
const testSources=new Map(tests.map(name=>[name,read('tests/'+name)]));
const htmlCache=new Map(pages.map(page=>[page,read(page)]));
const decode=value=>value.replace(/&(?:amp|quot|apos|lt|gt|#(\d+)|#x([0-9a-f]+));/gi,(entity,decimal,hex)=>decimal?String.fromCodePoint(Number(decimal)):hex?String.fromCodePoint(parseInt(hex,16)):({'&amp;':'&','&quot;':'"','&apos;':"'",'&lt;':'<','&gt;':'>'}[entity.toLowerCase()]||entity));
function attributes(text){const result={};for(const match of text.matchAll(/([^\s=/>]+)(?:\s*=\s*(?:"([^"]*)"|'([^']*)'|([^\s>]+)))?/g))result[match[1].toLowerCase()]=decode(match[2]??match[3]??match[4]??'');return result;}
const nodesOf=html=>[...html.matchAll(/<([a-z][\w:-]*)\b([^<>]*?)>/gi)].map(match=>({tag:match[1].toLowerCase(),attrs:attributes(match[2]),at:match.index,raw:match[0]}));
const lineOf=(source,index)=>source.slice(0,index).split('\n').length;
const findings=[];
const add=(page,severity,rule,detail,at=0)=>findings.push({page,line:lineOf(htmlCache.get(page),at),severity,rule,detail});
const escape=value=>value.replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
const shared=new Set(['foundation.js','reference-ui.js','app.js','product-runtime.js','experience-runtime.js','operations-core.js','extension-guard.js','prototype-search.js']);
const scriptNames=html=>nodesOf(html).filter(node=>node.tag==='script'&&node.attrs.src).map(node=>node.attrs.src);

// Resolve ownership with the actual canonical registry, without mounting a DOM.
const navContext={document:{documentElement:{setAttribute(){}},querySelectorAll:()=>[],addEventListener(){},body:{dataset:{}}},window:{addEventListener(){}},localStorage:{getItem:()=>null},location:{pathname:'/index.html',search:'',hash:''},URLSearchParams};
vm.runInNewContext(read('assets/foundation.js'),navContext);navContext.window.WCPUI=navContext.WCPUI;
vm.runInNewContext(read('assets/reference-ui.js'),navContext);
const nav=navContext.window.WCPNavigation;

// Evidence reviewed against executable VM adapter loads, not merely route mentions.
const targeted={
  'auth-runtime.js':['auth_html'],
  'application-center.js':['applications'],
  'account-dns.js':['dns_tls'], 'account-certificates.js':['dns_tls'],
  'delivery-center.js':['delivery'], 'website-center.js':['delivery'],
  'hosting-lifecycle.js':['hosting'], 'root-capacity.js':['hosting'],
  'email-center.js':['hosting'], 'account-recovery.js':['hosting'],
  'mail-rules-center.js':['mail_rules','hosting'],
  'observability-center.js':['observability'],
  'backup-center.js':['protection'], 'schedule-center.js':['protection'],
  'setup-wizard.js':['setup'], 'web-rules-center.js':['web_rules'],
  'file-manager.js':['workspace'], 'reseller-center.js':['workspace'],
  'governance-centers.js':['governance'],
  'root-control.js':['root_control'],
  'application-catalog.js':['root_centers'], 'platform-software.js':['root_centers'], 'mail-infrastructure.js':['root_centers'],
  'system-settings.js':['settings','system_settings','settings_controller']
};
const directPages={
  'auth_html':['login.html','mfa.html'],
  applications:['app-lifecycle.html','app-health.html'],
  dns_tls:['canonical-dns.html','ssl.html'],
  delivery:['git-repositories.html','deployments.html','websites.html'],
  hosting:['hosting-accounts.html','email.html'],
  observability:['logs-diagnostics.html','site-analytics.html'],
  protection:['backups.html','cron.html','scheduled-tasks.html'],
  setup:['setup-wizard.html'],
  web_rules:['web-compatibility.html','web-routing.html'],
  workspace:['file-manager.html','reseller.html'],
  governance:['continuity-control.html','privacy-governance.html','delivery-platform.html'],
  root_control:['service-manager.html','ip-management.html','api-automation.html','transfers.html'],
  root_centers:['application-catalog.html','software-manager.html','admin-mail.html'],
  settings:['system-settings.html'],system_settings:['system-settings.html'],settings_controller:['system-settings.html']
};
const suiteFor=(script)=> (targeted[script]||[]).map(suffix=>'audit_wcp360_'+suffix+'.mjs').filter(name=>testSources.has(name)&&testSources.get(name).includes(script)&&/vm\.(?:runInNewContext|runInContext)|new vm\.Script/.test(testSources.get(name)));
const rows=[];let references=0,fragmentIds=0,fragmentControllerCandidates=0,redirects=0;
for(const page of pages){
  const html=htmlCache.get(page),nodes=nodesOf(html),ids=new Set(nodes.filter(node=>'id' in node.attrs).map(node=>node.attrs.id)),scripts=scriptNames(html);
  const body=nodes.find(node=>node.tag==='body')?.attrs||{};
  const ownership=nav.owner(page),canonical=nav.resolve(page);
  const declared=body['data-access-scope']||body['data-product-scope']||body['data-auth-page']&&'auth'||body['data-portal']||'non déclaré';
  const controllers=scripts.filter(name=>/^assets\//.test(name)&&!shared.has(name.split('/').pop())&&!/(?:schemas|model|store)\.js$/.test(name)).map(name=>name.split('/').pop());
  const modules=scripts.filter(name=>/(?:schemas|model|store)\.js$/.test(name));
  const moduleSuites=page==='system-settings.html'?[...testSources].filter(([name,source])=>/^audit_wcp360_settings_(?:model|store)\.mjs$/.test(name)&&modules.some(module=>source.includes(module.split('/').pop()))&&/vm\.(?:runInNewContext|runInContext)|new vm\.Script/.test(source)).map(([name])=>name):[];
  const forms=nodes.filter(node=>node.tag==='form').length,fields=nodes.filter(node=>['input','select','textarea'].includes(node.tag)&&node.attrs.type!=='hidden').length,buttons=nodes.filter(node=>node.tag==='button').length;
  let runtimeNamedButtons=0;
  const actionNodes=nodes.filter(node=>Object.keys(node.attrs).some(name=>/^on(?:click|submit)$|^data-.*(?:action|primary|secondary|manage|submit)$/.test(name))).length;
  const redirect=html.match(/<script>\s*(location\.replace[\s\S]*?)<\/script>/);
  let redirectDestination='';
  if(redirect){redirects++;try{vm.runInNewContext(redirect[1],{location:{search:'?audit=1',hash:'#review',replace:value=>redirectDestination=value}});if(!redirectDestination.includes('audit=1')||!redirectDestination.endsWith('#review'))add(page,'error','redirect-state','La redirection perd la recherche ou le fragment.');}catch(error){add(page,'error','redirect-execution',error.message);}}
  const nature=redirect?'redirection':/^(?:index|login|mfa|setup-wizard)\.html$/.test(page)?'entrée':/(?:detail|create|record-add|record-edit|domain-add|resource)\.html$/.test(page)?'fiche / opération':controllers.length?'centre avec contrôleur':'maquette statique / shell';
  const suites=[...new Set(controllers.flatMap(suiteFor))].sort();
  const direct=suites.filter(suite=>(directPages[suite.replace(/^audit_wcp360_|\.mjs$/g,'')]||[]).includes(page));
  const partial=suites.filter(suite=>!direct.includes(suite));
  if(new Set(nodes.filter(node=>'id' in node.attrs).map(node=>node.attrs.id)).size!==nodes.filter(node=>'id' in node.attrs).length)add(page,'error','duplicate-id','Identifiants HTML dupliqués.');
  for(const [tag,count] of ['main','h1'].map(tag=>[tag,nodes.filter(node=>node.tag===tag).length]))if(count!==1)add(page,'error','landmark',`${count} élément(s) ${tag}, attendu 1.`);
  for(const node of nodes){
    for(const attr of ['href','src','action']){
      const ref=node.attrs[attr];if(!ref||/^(?:https?:|mailto:|tel:|javascript:|data:|blob:)/i.test(ref))continue;
      let url;try{url=new URL(ref,'http://localhost/'+page);}catch{add(page,'error','malformed-url',ref,node.at);continue;}
      const target=decodeURIComponent(url.pathname.slice(1))||page,absolute=resolve(root,target);references++;
      if(relative(root,absolute).startsWith('..')){add(page,'error','outside-root',ref,node.at);continue;}
      if(!existsSync(absolute)){add(page,'error','missing-reference',`${attr}=${ref}`,node.at);continue;}
      if(!url.hash||!target.endsWith('.html')||url.hash==='#')continue;
      const destination=htmlCache.get(target),fragment=decodeURIComponent(url.hash.slice(1));
      if(!destination)continue;
      if(nodesOf(destination).some(candidate=>candidate.attrs.id===fragment)){fragmentIds++;continue;}
      const controllerSources=scriptNames(destination).filter(name=>/^assets\//.test(name)&&existsSync(join(root,name))).map(read).join('\n');
      if(controllerSources.includes('location.hash')&&new RegExp('["\']'+escape(fragment)+'["\']|\\b'+escape(fragment)+'\\s*:').test(controllerSources)){fragmentControllerCandidates++;continue;}
      add(page,'review','fragment-unresolved',`${ref} : aucun id HTML ni candidat de fragment dans le contrôleur.`,node.at);
    }
    if(node.tag==='script'&&node.attrs.src){const name=node.attrs.src;if(/^assets\//.test(name)&&existsSync(join(root,name))){const source=read(name);if((name==='assets/reference-ui.js'||/\bWCPUI\.Overlay\b/.test(source))&&(scripts.indexOf('assets/foundation.js')<0||scripts.indexOf('assets/foundation.js')>scripts.indexOf(name)))add(page,'error','foundation-order',name,node.at);}}
    if(node.tag==='label'&&node.attrs.for&&!ids.has(node.attrs.for))add(page,'error','label-target',`label for=${node.attrs.for} sans contrôle associé.`,node.at);
    for(const attr of ['aria-labelledby','aria-describedby','aria-controls'])if(node.attrs[attr])for(const id of node.attrs[attr].split(/\s+/))if(!ids.has(id))add(page,'review','aria-target-initial',`${attr}=${id} absent du HTML initial ; montage dynamique à contrôler.`,node.at);
    if(node.tag==='img'&&!('alt' in node.attrs))add(page,'error','image-alt','Image sans attribut alt.',node.at);
  }
  // The initial-source button check excludes decorative icon text from the name.
  for(const match of html.matchAll(/<button\b([^>]*)>([\s\S]*?)<\/button>/gi)){
    const attrs=attributes(match[1]),content=match[2].replace(/<span\b[^>]*(?:material-symbols[^"']*|aria-hidden=["']true["'])[^>]*>[\s\S]*?<\/span>/gi,'').replace(/<[^>]*>/g,'').trim();
    if(!content&&!attrs['aria-label']&&!attrs['aria-labelledby']&&!attrs.title){
      // reference-ui.js names these documented shell controls during boot.
      const icon=match[2].match(/<span\b[^>]*class=["'][^"']*material-symbols[^"']*["'][^>]*>\s*([^<]+)<\/span>/i)?.[1]?.trim();
      const repaired=!!icon&&scripts.includes('assets/reference-ui.js');
      if(repaired)runtimeNamedButtons++;
      else add(page,'review','button-name-initial','Bouton sans texte ni nom accessible dans le HTML initial.',match.index);
    }
  }
  if(!redirect&&declared!=='non déclaré'&&!['auth'].includes(declared)&&ownership?.scope&&declared!==ownership.scope)add(page,'error','portal-mismatch',`HTML=${declared}, registre=${ownership.scope}.`);
  rows.push({page,nature,declared,resolvedScope:ownership?.scope||'auth',canonical,owner:ownership?.href||'auth',controllers,modules,moduleSuites,forms,fields,buttons,actionNodes,runtimeNamedButtons,direct,partial,redirectDestination});
}
const counts=key=>Object.fromEntries([...new Set(rows.map(row=>row[key]))].map(value=>[value,rows.filter(row=>row[key]===value).length]));
const uniqueFindings=[...new Map(findings.map(finding=>[JSON.stringify(finding),finding])).values()];
const summary={suite:'WCP360 page-by-page verification inventory',htmlPages:rows.length,compatibilityRedirects:redirects,nature:counts('nature'),portals:counts('resolvedScope'),directControllerAdapterPages:rows.filter(row=>row.direct.length).length,partialControllerAdapterPages:rows.filter(row=>!row.direct.length&&row.partial.length).length,withoutTargetedControllerAdapter:rows.filter(row=>!row.direct.length&&!row.partial.length).length,localReferences:references,htmlFragmentReferences:fragmentIds,controllerFragmentCandidates:fragmentControllerCandidates,errors:uniqueFindings.filter(item=>item.severity==='error').length,manualReviews:uniqueFindings.filter(item=>item.severity==='review').length,browserVerified:0,boundary:'Analyse du HTML initial et références de suites existantes. Aucun test métier ni navigateur exécuté par cet inventaire. Les mentions adaptateur sont des preuves de portée, pas des résultats.'};
if(process.argv.includes('--write')){
  const md=value=>String(value).replace(/\|/g,'\\|').replace(/\n/g,' ');
  const suiteLink=name=>`[${name.replace('audit_wcp360_','').replace('.mjs','')}](tests/${name})`;
  const report=[
    '# Inventaire de vérification page par page — 1er novembre 2026',
    '',
    `Cet inventaire complète [24-verification-record.md](24-verification-record.md) et [25-ui-ux-remediation.md](25-ui-ux-remediation.md). Il ne remplace ni les décisions de périmètre ni les preuves historiques. Les ${rows.length} pages présentes sont examinées individuellement dans leur HTML initial. Une URL répondant en HTTP, un fichier JavaScript présent et un test de composant partagé ne prouvent pas le fonctionnement de la page.`,
    '',
    `Sur ${summary.htmlPages} pages : **${summary.directControllerAdapterPages} ont une portée de contrôleur explicitement exercée par un adaptateur**, ${summary.partialControllerAdapterPages} chargent un contrôleur partiellement exercé ailleurs et **${summary.withoutTargetedControllerAdapter} n'ont pas de preuve de contrôleur ciblé dans les suites référencées**. **0 page vérifiée visuellement ou dans un navigateur par cet inventaire**.`,
    '',
    `Nature : ${Object.entries(summary.nature).map(([key,value])=>`${value} ${key}`).join(' ; ')}. ${summary.compatibilityRedirects} redirections de compatibilité restent distinctes des pages métier.`,
    '',
    'La colonne « structure » signifie uniquement analyse automatisée du HTML : dépendances locales (paramètres séparés des chemins), présence main/h1, identifiants, ordre Foundation, labels explicites et cibles ARIA initiales. Les fragments avec un id réel sont résolus ; ceux seulement reconnus comme littéraux par un contrôleur restent des candidats. Les paramètres métier, le montage dynamique, la validation native, le clavier, les lecteurs d’écran, le responsive et les contrastes rendus ne sont pas certifiés.',
    '',
    'Les suites liées sont recensées par leur exécution explicite du contrôleur dans un adaptateur VM ; cet outil ne les exécute pas. « Partiel » couvre un module présent mais ne prouve pas le parcours de cette page. Les suites partagées Foundation, shell et moteur de tâches ne sont jamais comptées comme validation métier de toutes leurs pages consommatrices. Les grilles et formulaires injectés par JavaScript ne figurent pas dans les nombres initiaux F/C/B/A (formulaires/champs/boutons/éléments à marqueur d’action). Les boutons avec icône que reference-ui.js nomme explicitement au montage ne sont pas signalés à nouveau comme défaut certain ; leur nombre figure dans le CSV sous runtimeNamedButtons.',
    '',
    'Pour System Settings, les preuves modèle et stockage sont mentionnées séparément du contrôleur : validation des valeurs et migrations de données, persistance/révisions/concurrence, puis interactions du contrôleur avec un DOM adapté. Un test de modèle ou de stockage ne permet pas de marquer le contrôleur ni le rendu comme vérifié. Les suites de modèles partagés des autres familles sont conservées dans leurs rapports historiques et ne sont pas réattribuées aux pages sans preuve de contrôleur.',
    '',
    '## Corrections fonctionnelles complémentaires',
    '',
    'Les quatre modules [Service Manager](service-manager.html), [IP Management](ip-management.html), [API & Automation](api-automation.html) et [Transfers](transfers.html) avaient des onglets qui ne modifiaient que leur résumé, des formulaires ignorant les saisies et une cible Manage reprenant le premier exemple. [root-control.js](assets/root-control.js) fournit désormais quatre panneaux distincts, des cibles réelles de ligne, un filtre excluant les états inactifs des résultats sains et un historique de brouillons locaux avec les champs saisis. Les valeurs persistent séparément par module et cible dans `wcp360-root-control-drafts-v1:<module>`. Une erreur de stockage garde l’éditeur ouvert et ne crée ni message de réussite ni tâche. Ces comportements sont exercés par [audit_wcp360_root_control.mjs](tests/audit_wcp360_root_control.mjs). Les inventaires restent fictifs et les tâches simulées.',
    '',
    'Les centres [Continuité](continuity-control.html), [Gouvernance des données](privacy-governance.html) et [Plateforme de livraison](delivery-platform.html) récupèrent désormais des collections locales nulles ou malformées sans interrompre leur rendu. Les adresses IPv4/IPv6 sont validées et normalisées avant vérification des doublons ; noms et sujets sont validés, les valeurs de tableau sont rendues comme texte et les requêtes absentes ou déjà soumises sont bloquées. Un échec de persistance annule la mutation et ne crée pas de tâche. [audit_wcp360_governance.mjs](tests/audit_wcp360_governance.mjs) exerce ces régressions avec le véritable contrôleur et des frontières DOM/stockage adaptées.',
    '',
    'Le [catalogue d’applications](application-catalog.html), le [gestionnaire de logiciels](software-manager.html) et la [messagerie ROOT](admin-mail.html) disposent aussi d’une suite ciblée : [audit_wcp360_root_centers.mjs](tests/audit_wcp360_root_centers.mjs). Les nouveaux éléments du catalogue restent non publiés jusqu’à une revue locale explicite ; les versions, politiques, profils et demandes conservent les saisies. La recherche avancée filtre la file de courrier locale. Les échecs de stockage ne doivent ni fermer le formulaire ni annoncer une réussite ou créer une tâche. Les contrôles des sources, installations et services restent simulés. Les repères main imbriqués des vues dynamiques Catalogue, DNS ROOT et Services de transfert ont été remplacés par des conteneurs ordinaires ; cette correction HTML ne certifie pas les parcours DNS ou de transfert.',
    '',
    '## Constats reproductibles dans le HTML initial',
    '',
    `Résultat : ${summary.errors} erreur(s) structurelle(s) certaine(s), ${summary.manualReviews} point(s) à revoir (une cible ARIA ou un nom peut être créé au montage). Les lignes concernent les sources, souvent minifiées ; ce ne sont pas des observations de rendu.`,
    '',
    '| Page : ligne | Niveau | Règle | Constat |',
    '| :--- | :--- | :--- | :--- |',
    ...uniqueFindings.map(item=>`| [${item.page}:${item.line}](${item.page}) | ${item.severity} | ${item.rule} | ${md(item.detail)} |`),
    ...(uniqueFindings.length?[]:['| Toutes | — | — | Aucun défaut détecté par ces règles limitées. |']),
    '',
    '## Couverture de chaque page',
    '',
    '| Page | Nature | Portail déclaré → registre ; destination canonique | Contrôleurs HTML initiaux | F/C/B/A | Structure | Adaptateur ciblé existant (portée) | Navigateur / visuel |',
    '| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |',
    ...rows.map(row=>{const issues=uniqueFindings.filter(item=>item.page===row.page);let coverage=row.direct.length?row.direct.map(suiteLink).join(', '):row.partial.length?'Partiel : '+row.partial.map(suiteLink).join(', '):'Aucun';if(row.moduleSuites.length)coverage+=' ; modèle/stockage uniquement : '+row.moduleSuites.map(suiteLink).join(', ');return `| [${row.page}](${row.page}) | ${row.nature} | ${md(row.declared)} → ${row.resolvedScope} ; ${md(row.canonical)} | ${row.controllers.join(', ')||'—'} | ${row.forms}/${row.fields}/${row.buttons}/${row.actionNodes} | automatisée${issues.length?` ; ${issues.length} constat(s)`:''} | ${coverage} | **NON vérifié** |`;}),
    '',
    '## Reproduction',
    '',
    '`node tests/audit_wcp360_page_coverage.mjs` affiche le résumé et les constats sans modifier les artefacts. `node tests/audit_wcp360_page_coverage.mjs --write` régénère ce document et [26-page-verification.csv](26-page-verification.csv). `node tests/run.mjs` exécute séparément les suites statiques et les adaptateurs recensés. Le CSV expose les valeurs techniques pour trier les pages et préparer une revue navigateur suivie.',
    ''
  ].join('\n');
  writeFileSync(join(root,'26-page-verification-inventory.md'),report,'utf8');
  const columns=['page','nature','declared','resolvedScope','canonical','owner','controllers','modules','moduleSuites','forms','fields','buttons','actionNodes','runtimeNamedButtons','direct','partial','redirectDestination'];
  const csv=value=>'"'+String(Array.isArray(value)?value.join('; '):value??'').replace(/"/g,'""')+'"';
  writeFileSync(join(root,'26-page-verification.csv'),'\uFEFF'+[columns.map(csv).join(','),...rows.map(row=>columns.map(key=>csv(row[key])).join(','))].join('\r\n')+'\r\n','utf8');
}
console.log(JSON.stringify({...summary,findings:uniqueFindings.slice(0,30),additionalFindings:Math.max(0,uniqueFindings.length-30)},null,2));
if(summary.errors)process.exitCode=1;
