# Audit HTML / CSS / JavaScript

## 1. Pages et doublons

### Doublons HTML exacts détectés
- `dns-zone.html` == `canonical-dns.html`
- `security.html` == `canonical-security.html`
- `canonical-server-overview.html` == `server-overview.html`
- `v2.5.3.14-index.html` == `v2.5.3.12-index.html`
- `v2.5.3.11-index.html` == `v2.5.3.9-index.html`

Il faut décider laquelle est canonique et archiver l’autre.

### Familles avec variantes concurrentes
- `admin-dashboard.html` / `canonical-admin-dashboard.html`
- `admin-security.html` / `canonical-security.html` / `security.html`
- `canonical-user-dashboard.html` / `user-dashboard.html`
- `canonical-website-overview.html` / `website-overview.html`
- `canonical-dns.html` / `dns.html`
- `canonical-server-overview.html` / `server-overview.html`

## 2. Pages orphelines

41 pages n’ont aucun lien entrant dans le graphe HTML local.

Une partie correspond à des index historiques, ce qui est normal si elles sont archivées.

En revanche, plusieurs pages actives importantes sont orphelines :
- Approval Center
- Audit Explorer
- Help & Support
- Error Remediation
- Storage / Volumes
- Processes / Workers
- Worker Pools
- Profile / Account Settings
- System Settings
- Service Configuration Schemas
- SSL / TLS approfondi
- Support Bundle

### Conclusion
Les dernières familles ont été **créées**, mais pas encore **intégrées à l’Information Architecture globale**.

## 3. Navigation

### Liens placeholder
212 liens `href="#"`.

Répartition dominante :
- Deployments : 100
- Logs : 100
- Activity : 10

Ces entrées doivent devenir soit :
- de vrais liens contextuels ;
- des actions JavaScript explicites ;
- ou être retirées.

## 4. Accessibilité réelle du markup

### Langue/direction
- 187 pages `lang="fr"`
- 20 pages `lang="en"`
- 0 page définit explicitement `dir`.

Les 20 pages anglaises sont principalement les écrans V2.5.3.31–.32.

Le système est donc visuellement multilingue dans son corpus, mais pas encore normalisé.

### Structure
- 205/207 pages ont `<main>`.
- 203/207 ont `<header>`.
- 186/207 ont `<nav>`.
- 20/207 ont un skip link.
- 20/207 ont `#main-content`.

### Formulaires
555 contrôles de formulaire détectés.
Seulement 13 possèdent une association label/ARIA stricte.

Beaucoup utilisent des `<label>` visuels non associés par `for`/`id`.

### Tables
- 38 tables
- 0 `<caption>`
- 152 `<th>`
- 0 `scope`

### Navigation active
208 éléments `.active` dans nav/tabs.
0 utilise `aria-current`.

### Drawers / overlays
429 drawers détectés.
Le Command Center principal utilise `role="dialog"` / `aria-modal="true"`, ce qui est positif.

En revanche, les side drawers ne disposent généralement pas de rôle/nom accessibles complets.

### Focus
La spécification V2.5.3.31 exige :
- focus trap ;
- retour du focus au déclencheur.

`reference-ui.js` ne mémorise pas le déclencheur et ne restaure pas le focus.
Il n’implémente pas non plus de véritable focus trap pour les drawers/modales.

Il existe donc un écart entre **spécification d’accessibilité** et **prototype effectif**.

## 5. JavaScript

### Conflit legacy
21 pages chargent simultanément `app.js` et `reference-ui.js`.

`app.js` contient plusieurs générations historiques de Command Center et plusieurs listeners `keydown`.

`reference-ui.js` contient le système actuel.

Il faut supprimer `app.js` du corpus actif ou le fusionner proprement.

### Secret reveal prototype
`reference-ui.js` injecte une valeur factice codée en dur pour le composant Reveal Secret.

C’est acceptable uniquement comme mock visuel, mais ce comportement doit être clairement marqué prototype et ne jamais être repris dans l’implémentation.

## 6. CSS

### `reference-ui.css`
L’accumulation par versions a produit de nombreuses redéfinitions de composants.

Exemples fortement redéfinis :
- `.wcp-dashboard-content`
- `.wcp-action-btn`
- `.wcp-page-head`
- `.wcp-panel`
- `.wcp-health-strip`
- `.wcp-tab`
- `.wcp-filter-chip`

### `styles.css`
Ancien système fortement basé sur overrides :
- 347 `!important`
- plusieurs définitions successives de `.sidebar`, `.header`, `.main`, `.search`, etc.

### Recommandation
Ne pas modifier visuellement le système maintenant.

Faire plus tard un **CSS Architecture Refactor sous tests de non-régression visuelle** :
1. tokens ;
2. reset/base ;
3. shell ;
4. components ;
5. patterns ;
6. page-specific ;
7. utilities ;
8. responsive/a11y.

Le rendu V2.5.3.14 reste la référence.