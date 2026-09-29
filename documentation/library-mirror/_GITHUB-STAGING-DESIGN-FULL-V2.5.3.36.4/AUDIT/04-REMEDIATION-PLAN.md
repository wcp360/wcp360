# Plan de remédiation recommandé

## Étape A — Consolidation documentaire AVANT V2.5.3.37

### A1. Source-of-truth map
Créer un fichier :
`CANONICAL_SOURCES.md`

Il doit préciser pour chaque domaine :
- fichier canonique ;
- fichiers étendus ;
- fichiers historiques ;
- fichier superseded.

### A2. Permission namespace
Fusionner :
- `03-canonical-permission-namespace.md`
- `04-action-permission-mapping.md`
- `01-permission-action-contract.md`

sans supprimer l’historique.

Résultat attendu :
**1 permission registry canonique + matrices dérivées**.

### A3. State registry
Résoudre `BLOCKED`.

Créer un registre unique des états et reason codes.

## Étape B — Nettoyage du prototype HTML

### B1. Définir ACTIVE vs ARCHIVE
Ne pas supprimer immédiatement les anciennes pages.

Créer :
```text
/active/
/archive/
```

ou un manifeste équivalent.

### B2. Définir les pages canoniques
Exemple :
- garder `canonical-dns.html`, archiver alias exact ;
- garder un seul Server Overview ;
- garder un seul Security Center ;
- garder un seul User Dashboard.

### B3. Intégrer les dernières familles
Ajouter de vraies routes/navigation vers :
- Approval Center ;
- Notifications ;
- Storage ;
- Processes/Workers ;
- Help/Support ;
- Audit Explorer ;
- Settings ;
- Service Config Schemas ;
- SSL/TLS.

### B4. Remplacer tous les `href="#"`

## Étape C — JavaScript
Supprimer `assets/app.js` du corpus actif.

Conserver un seul runtime prototype :
`assets/reference-ui.js`.

Puis implémenter :
- focus trap ;
- focus restore ;
- aria-expanded ;
- aria-controls ;
- drawer semantics ;
- live-region toasts.

## Étape D — Accessibilité markup
Migrer progressivement les pages :
- `dir="ltr"` explicite par défaut ;
- skip link ;
- `#main-content` ;
- labels `for/id` ;
- `aria-current` ;
- `<caption>` ou description de table ;
- `scope="col"` / `scope="row"` ;
- types de boutons ;
- noms des drawers.

## Étape E — CSS
Ne pas faire de redesign.

Créer une refactor branch/artifact plus tard avec tests visuels.

Ordre proposé :
1. tokens ;
2. base ;
3. shell ;
4. components ;
5. technical surfaces ;
6. family styles ;
7. responsive ;
8. accessibility overrides.

## Gate avant V2.5.3.37

Je recommande de **ne pas continuer les adapters** avant de fermer au minimum :
- permission namespace ;
- job state registry ;
- active/archive manifest ;
- JS conflict ;
- navigation placeholder.

Ces cinq points sont transversaux et affecteraient tout futur travail.