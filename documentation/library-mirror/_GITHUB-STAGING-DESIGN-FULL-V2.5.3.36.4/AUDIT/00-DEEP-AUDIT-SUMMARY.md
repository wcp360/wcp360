# WCP360 — Deep Audit HTML + Markdown
## Base auditée
`V2.5.3.36 — System Boundary & Installation Specification`

## Périmètre
- 207 pages HTML
- 88 fichiers Markdown
- 2 fichiers CSS
- 2 fichiers JavaScript
- 300 fichiers au total

## Verdict

**Architecture fonctionnelle : forte**  
**Documentation système : forte**  
**Cohérence du corpus : PARTIAL**  
**Intégration UX des dernières familles : PARTIAL**  
**Accessibilité du prototype réel : PARTIAL / non conforme aux exigences déclarées**  
**Dette CSS/JS : HIGH**  
**Prêt pour contrats exécutables : NON**

Aucune anomalie ne justifie de jeter le travail existant. En revanche, il faut une phase de **consolidation du corpus** avant toute nouvelle famille ou tout artefact exécutable.

## Résumé des anomalies majeures

### HIGH-01 — Namespace de permissions divergent
Le fichier canonique et le mapping d’actions ne décrivent pas exactement le même vocabulaire.

- 65 permissions trouvées dans `03-canonical-permission-namespace.md`.
- 59 identifiants dans `04-action-permission-mapping.md`.
- 17 permissions sont utilisées dans le mapping mais absentes du namespace canonique.
- 23 permissions canoniques ne sont pas représentées dans le mapping d’actions.

Cela doit être corrigé avant toute implémentation RBAC.

### HIGH-02 — State machine Job divergente
`02-job-task-contract.md` utilise l’état `BLOCKED`, alors que `04-canonical-state-machines.md` ne le définit pas dans la state machine Job.

Le corpus affirme pourtant que les états sont gelés/canoniques.

### HIGH-03 — Les pages récentes ne sont pas réellement intégrées à l’architecture de navigation
41 pages HTML n’ont aucun lien entrant local.

Parmi les pages métier importantes concernées :
- `approvals.html`
- `audit-explorer.html`
- `error-remediation.html`
- `help-support.html`
- `processes-workers.html`
- `profile-account-settings.html`
- `service-config-schemas.html`
- `ssl-tls.html`
- `storage-volumes.html`
- `support-bundle.html`
- `system-settings.html`
- `worker-pools.html`

Ces pages existent donc dans le corpus mais ne sont pas encore intégrées comme un système navigable cohérent.

### HIGH-04 — Navigation factice persistante
212 liens utilisent encore `href="#"`.

Les deux principaux cas :
- `Deployments` : 100 occurrences
- `Logs` : 100 occurrences

Cela signifie que la sidebar semble fonctionnelle mais contient deux destinations globales non résolues sur une grande partie du prototype.

### HIGH-05 — Conflit JavaScript hérité
21 pages chargent **à la fois** :
- `assets/app.js`
- `assets/reference-ui.js`

`app.js` contient plusieurs gestionnaires historiques de `Ctrl/Cmd+K`, et `reference-ui.js` en contient également un.

Risque :
- plusieurs handlers exécutés pour la même touche ;
- plusieurs Command Center/modals tentent de s’ouvrir ;
- focus incohérent ;
- comportement différent selon la page.

### HIGH-06 — Le prototype ne respecte pas encore les exigences d’accessibilité qu’il déclare
Constats statiques :
- 207 pages au total ;
- seulement 20 ont un skip link ;
- seulement 20 exposent `#main-content` ;
- 555 contrôles de formulaire ;
- seulement 13 disposent d’une association label/ARIA correcte au sens strict ;
- 38 tables ;
- 0 table avec `<caption>` ;
- 152 `<th>` ;
- 0 avec `scope` ;
- 208 onglets/navigation marqués actifs ;
- 0 avec `aria-current` ;
- 429 drawers ;
- la majorité n’a ni rôle sémantique adapté ni nom accessible ;
- 1733 boutons ;
- seulement 48 déclarent explicitement `type`.

Le document V2.5.3.31 définit de bonnes exigences, mais celles-ci ne sont pas encore appliquées à l’ensemble des pages héritées.

### HIGH-07 — Dette CSS accumulée
`assets/reference-ui.css` :
- ~1445 lignes ;
- 962 sélecteurs uniques ;
- 194 sélecteurs redéfinis ;
- 44 blocs media query ;
- 54 `!important`.

`assets/styles.css` :
- ~1530 lignes ;
- 427 sélecteurs uniques ;
- 185 sélecteurs redéfinis ;
- 347 `!important`.

23 pages chargent encore `assets/styles.css` en plus de `reference-ui.css`.

C’est le principal risque de “perte du style précédent” lors des futures modifications.

### HIGH-08 — Corpus actif et archive historique mélangés
Le dossier courant contient :
- pages canoniques ;
- anciennes variantes ;
- index historiques de nombreuses versions ;
- pages strictement dupliquées ;
- documents de plusieurs phases numérotés avec les mêmes préfixes `01-`, `02-`, etc.

Le corpus ne possède pas encore une séparation nette :
`ACTIVE / CANONICAL / ARCHIVE / DEPRECATED`.

## Points positifs importants
- aucun lien local réellement cassé n’a été détecté ;
- aucun vestige `WC-CLIENT` / `WC-ADMIN` détecté ;
- aucun vestige “Hestia x cPanel Fusion Engine” ou “CodingNepal” détecté ;
- aucune architecture générique de shell root n’a été retrouvée dans les contrats ;
- les chemins système récents sont cohérents autour de `/usr/local/wcp360`, `/etc/wcp360`, `/var/lib/wcp360`, `/run/wcp360`, `/srv/wcp360` ;
- les principes daemon allowlist, anti-lockout, SHOW_ONCE / NEVER_REVEAL et UNKNOWN_OUTCOME sont solides.