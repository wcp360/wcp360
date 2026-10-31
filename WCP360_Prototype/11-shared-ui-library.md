# WCP360 UI Foundation 1.1.1

Le socle réutilisable du prototype est constitué de deux fichiers autonomes :

- [assets/foundation.js](assets/foundation.js) : API `window.WCPUI`, moteurs de données et composants ;
- [assets/foundation.css](assets/foundation.css) : styles limités aux composants, basés sur les tokens WCP360.

La [galerie interactive](components.html) permet de manipuler 32 ressources fictives, de créer et modifier un enregistrement, de changer le rôle de démonstration et d’inspecter les états chargement/erreur. Les centres [Sites](websites.html), [Bases de données](databases.html), [Messagerie](email.html) et [Comptes](hosting-accounts.html) consomment les mêmes composants sur leurs données persistantes, ainsi que la récupération des comptes. La suite est décrite dans [12-mail-hosting-migration.md](12-mail-hosting-migration.md).

## Couverture

| Mécanisme | Priorité | État de cette passe |
|---|---|---|
| DataTable / DataGrid, pagination | P0 | Moteur unique, utilisé dans Sites, Bases, Mail, Comptes, Récupération et galerie |
| Tri multicritère | P0 | Boutons d’en-tête, Shift + clic et formulaire de tri accessible |
| Filtres avancés, recherche | P0 | Conditions combinées avec AND ; recherche et filtres appliqués ensemble |
| Form Builder, validation | P0 | Schémas communs pour Sites, Bases, Mail et Comptes ; erreurs liées aux champs, validation croisée et détection des doublons |
| Modales / dialogues | P0 | Dialogue natif, confirmation, saisie exacte facultative ; utilisé par la galerie et les actions groupées |
| Notifications / toasts | P0 | Un renderer commun utilisé par le shell et le Job Center ; intégration au runtime de notifications existant |
| Accessibilité | P0 | Table native, labels, `aria-sort`, sélection nommée, résumé d’erreurs, focus, dialogues natifs et régions de statut ; vérification visuelle à compléter |
| Responsive | P0 | Barres d’outils repliables, formulaires sur une colonne et défilement horizontal local ; vérification visuelle à compléter |
| Actions groupées | P1 | Sélection sur plusieurs pages, confirmation et callbacks métier ; sauvegardes et maintenance simulées |
| Export CSV / JSON | P1 | Résultats filtrés et triés, colonnes visibles exportables, neutralisation des formules CSV |
| Colonnes personnalisables | P1 | Masquer/afficher, déplacer et conserver au moins une colonne |
| Thèmes | P1 | Les shells historique et canonique délèguent au même service de thème |
| Vues enregistrées | P2 | Recherche, filtres, tri, taille de page et colonnes conservés par contexte |
| Command Palette | P2 | Palette existante conservée ; son moteur reste dans le shell, son extraction n’est pas incluse dans cette passe |

Les autres centres conservent leurs composants historiques. Leur migration se fait progressivement ; le socle n’est pas une réécriture de tous les modules ni une implémentation backend.

## Distribution vers Core, Internal et extensions

Charger le script avant le code des consommateurs :

```html
<script src="assets/foundation.js"></script>
```

La feuille de styles voisine est chargée automatiquement, à moins d’être déjà incluse. Il n’y a ni installation, ni framework, ni dépendance au runtime privé. Les deux fichiers peuvent être copiés ensemble dans les distributions Core, Internal et extension ; aucune donnée ni route INTERNAL ne doit être copiée dans le paquet public.

Chaque consommateur fournit explicitement son application, son périmètre, son propriétaire et son module :

```javascript
const preferences = WCPUI.createStore({
  app: 'core',       // 'internal' ou 'extension:<id>' dans les autres distributions
  scope: 'user',
  owner: 'account-42',
  module: 'sites-grid',
  version: 1
});
```

`read()`, `write(data)` et `clear()` n’accèdent qu’à cette clé. Une indisponibilité du stockage local conserve un fonctionnement en mémoire. Les secrets, identités réelles et autorisations serveur ne doivent jamais être stockés ici.

## DataGrid

```javascript
const grid = WCPUI.DataGrid.mount(document.querySelector('#sites'), {
  id: 'sites',
  title: 'Sites',
  rowKey: 'id',
  rows: sites,
  store: preferences,
  columns: [
    { key: 'domain', label: 'Domain' },
    { key: 'storage', label: 'Storage (GB)', type: 'number' },
    { key: 'status', label: 'Status' }
  ],
  rowActions: [{ label: 'Manage', run: site => openSite(site.id) }],
  bulkActions: [{
    label: 'Back up selected',
    confirm: true,
    run: selection => queueBackup(selection.map(site => site.id))
  }]
});
```

API d’instance : `setRows(rows)`, `setStatus('ready' | 'loading' | 'error', message)`, `refresh()` et `destroy()`. Un remontage sur le même hôte détruit les anciens écouteurs. Un module qui remplace son conteneur doit appeler `destroy()` avant le remplacement.

Une colonne accepte `type: 'number' | 'date'`, `value(row)`, `render(row, value)`, `hidden`, `sortable`, `filterable`, `searchable` et `exportable`. `render` renvoie du texte ou un nœud DOM, pas une chaîne HTML interprétée. L’export ne comprend que les colonnes visibles dont `exportable` n’est pas `false`.

Le moteur indépendant du DOM est disponible avec `WCPUI.createCollection({rows, columns, rowKey, pageSize})`. Il exige des identifiants de lignes uniques et stables. Il expose `setQuery`, `setFilters`, `setSort`, `setPageSize`, `setPage`, `select`, `selectedRows`, `ordered`, `snapshot` et `export`.

Les filtres combinent `contains`, `equals`, `notEquals`, `startsWith`, `gt`, `gte`, `lt`, `lte`, `empty` et `notEmpty`. La recherche ignore la casse et les accents. Les tris sont stables et les valeurs absentes restent en fin de liste. La sélection est conservée entre pages ; changer la recherche ou les filtres la vide pour éviter une action sur des lignes devenues invisibles.

## Formulaires

```javascript
WCPUI.Form.populate(form, {
  fields: [
    { name: 'domain', label: 'Domain', required: true, rule: 'domain' },
    { name: 'quota', label: 'Quota', type: 'number', min: 1, max: 200 }
  ],
  values: { quota: 10 },
  submitLabel: 'Create',
  onCancel: close,
  onSubmit: values => createResource(values)
});
```

Types : texte, email, nombre, mot de passe, textarea, select et checkbox. Les schémas acceptent les contraintes de longueur, les bornes, les options, l’aide, une valeur par défaut et `validate(value, values)` pour les règles croisées. Les règles nommées sont `domain`, `identifier`, `absolutePath` et `email`.

`Form.attach(form, {fields})` ajoute la validation à un formulaire existant sans remplacer son handler métier. Une soumission invalide est bloquée avant ce handler. Avec `onSubmit`, le contrôleur commun consomme la soumission ; un ancien handler ne doit pas exécuter l’opération une seconde fois. Les validateurs personnalisés s’exécutent aussi sur les valeurs facultatives vides et les cases décochées. `Form.detach(form)` retire la validation et restaure le comportement natif. `validateValues(fields, values)` permet de tester les schémas sans DOM. Les contrôles serveur restent nécessaires pour une future implémentation réelle.

## Services

- `WCPUI.Dialog.open({title, description, content, onClose})` renvoie `{element, close, closed}` ; `closed` est une promesse.
- `await WCPUI.Dialog.confirm({title, message, confirmLabel, typed})` renvoie un booléen. Un seul dialogue du service est actif à la fois.
- `WCPUI.toast({title, message, tone})` affiche un retour local. `WCPUI.notify(...)` utilise le runtime du produit lorsqu’il existe, sans afficher un second toast.
- `WCPUI.theme.apply('system' | 'light' | 'dark')` est le propriétaire commun du thème du prototype.
- `WCPUI.can(context, capability, target)` fournit un contrat d’affichage UI avec refus par défaut et correspondance exacte du périmètre/propriétaire. Il ne remplace pas une autorisation côté serveur.

## Vérification

Depuis la racine du workspace, avec Node disponible :

```powershell
node WCP360_Prototype/tests/audit_wcp360_web_rules.mjs
node WCP360_Prototype/tests/audit_wcp360_observability.mjs
node WCP360_Prototype/tests/audit_wcp360_applications.mjs
node WCP360_Prototype/tests/audit_wcp360_delivery.mjs
node WCP360_Prototype/tests/audit_wcp360_dns_tls.mjs
node WCP360_Prototype/tests/audit_wcp360_protection.mjs
node WCP360_Prototype/tests/audit_wcp360_workspace.mjs
node WCP360_Prototype/tests/audit_wcp360_hosting.mjs
node WCP360_Prototype/tests/audit_wcp360_foundation.mjs
node WCP360_Prototype/tests/audit_wcp360_integration.mjs
node WCP360_Prototype/tests/audit_wcp360_prototype.mjs WCP360_Prototype
```

Les moteurs purs et les raccordements statiques sont testables sans navigateur. La validation réelle du focus, des dialogues natifs, des contrastes et du rendu responsive nécessite une passe navigateur distincte.


Lors de la passe 1.0, les documents et ressources ont répondu en HTTP 200. Computer Use a pu lire la fenêtre Chrome existante, puis a interrompu le pilotage à l’ouverture d’un nouvel onglet car son URL ne pouvait plus être déterminée avec certitude. La galerie, les dialogues natifs et les nouveaux formulaires n’ont donc pas été vérifiés visuellement.


La passe 1.1 a permis d’observer l’affichage de la messagerie dans Chrome. Les contrôleurs mail/comptes/récupération disposent d’une nouvelle suite de tests métier ; la validation visuelle complète des interactions reste à faire, la fenêtre étant utilisée en parallèle.


La passe fichiers/revendeur ajoute les dossiers, transferts, corbeille, clients, offres, brouillons et historique aux consommateurs de DataGrid. Ces deux centres utilisent les formulaires et dialogues communs et les contrats de `assets/workspace-schemas.js`. Les anciennes pages de recherche, upload, transferts, corbeille et préférences de fichiers redirigent vers File Manager. Voir [13-file-reseller-migration.md](13-file-reseller-migration.md).


Les sauvegardes USER et la planification USER/ROOT consomment désormais les grilles, formulaires et dialogues communs. Leurs règles de restauration, rétention, calendrier et plafonds restent dans `protection-schemas.js` ; `operations-core.js` mutualise leurs raccordements au runtime. Voir [14-protection-scheduling-migration.md](14-protection-scheduling-migration.md).


Les centres DNS et certificats USER réutilisent aussi DataGrid et les formulaires/dialogues communs. Les contrats de zones, SANs, politiques TTL et versions de déploiement restent dans `dns-tls-schemas.js`. Voir [15-account-dns-tls-migration.md](15-account-dns-tls-migration.md).


Git et Deployments utilisent désormais les grilles, formulaires et dialogues communs via un seul contrôleur de livraison. Les contrats de source, build, promotion et rollback résident dans `delivery-schemas.js` ; l’état métier est partagé avec le résumé des sites. Voir [16-account-delivery-migration.md](16-account-delivery-migration.md). La passe livraison comptait 172 consommateurs HTML complets de Foundation et 53 pages de compatibilité.


Les parcours applicatifs utilisent le même contrôleur pour les variables, l’inventaire des environnements, le runtime, la maintenance, les checks et leur activité. Les contraintes métier restent dans `application-schemas.js` et l’inventaire partagé des sites dans `website-model.js`. La passe applicative comptait 168 consommateurs HTML complets de Foundation et 57 pages de compatibilité. Voir [17-application-workspace-migration.md](17-application-workspace-migration.md).


Les journaux USER/ROOT partagent désormais DataGrid, les formulaires, dialogues et jobs via `observability-center.js`. Foundation 1.1.1 expose `WCPUI.download(content, filename, mimeType)`, le helper existant des exports de grille ; les rapports JSON réutilisent ce même chemin de téléchargement. `text/csv` ajoute le BOM UTF-8 déjà utilisé par les grilles ; le contenu CSV doit être produit par le moteur d’export, qui assure son échappement et la neutralisation des formules. Le téléchargement requiert les API Blob/URL du navigateur. Voir [18-scoped-observability-migration.md](18-scoped-observability-migration.md). Le dossier compte désormais 167 consommateurs HTML complets et 58 routes de compatibilité.


Web Compatibility et la politique ROOT utilisent le même contrôleur, DataGrid, Form et Dialog pour les brouillons, validations, publications et versions conservées. Leurs contrats sont dans `web-rules-schemas.js`. `bindingStatus` réutilise les règles TLS existantes pour Sites et le centre web ; les libellés des tableaux sont dérivés des champs communs. Les préférences concurrentes de Sites deviennent des liens vers leurs propriétaires. Voir [19-web-rules-policy-migration.md](19-web-rules-policy-migration.md). Les totaux restent de 167 consommateurs HTML complets et 58 routes de compatibilité.
