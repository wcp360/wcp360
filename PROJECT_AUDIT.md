# Audit d'organisation WCP360

Date : 2026-09-27

## Resume

Le projet est coherent comme **corpus de specification + reference de design**, mais il n'est pas encore coherent comme repository de developpement applicatif. Les dossiers `WCP360 Official html website/` et `WCP360 Source Code/` sont vides, ce qui confirme le statut documentaire : `CODE-BASELINE = NONE`, `Source Code = BLOCKED`, `Implementation Readiness = NO-GO`.

La bonne organisation immediate consiste a clarifier les points d'entree, tester ce qui est testable en statique, et eviter les deplacements massifs qui casseraient la tracabilite documentaire.

## Inventaire observe

| Zone | Fichiers | Taille approx. | Interpretation |
|---|---:|---:|---|
| `docs/` | 1803 | 3.21 MB | Corpus documentaire principal, avec specs numerotees, matrices MASTER, snapshot consolide et archives V4. |
| `WCP360 Design System/` | 245 | 2.90 MB | Reference HTML statique du design system, pages WCP-USER/WCP-ADMIN, assets CSS/JS communs. |
| `dashboard-html-css-javascript/` | 4 | 0.16 MB | Prototype dashboard simple, probablement externe ou anterieur, non canonique WCP360. |
| `WCP360 Official html website/` | 0 | 0 MB | Emplacement reserve pour un site officiel, pas encore initialise. |
| `WCP360 Source Code/` | 0 | 0 MB | Emplacement reserve pour le code produit, actuellement bloque par la gouvernance. |

Extensions observees : Markdown majoritaire, puis HTML statique. Aucun `package.json`, `pyproject.toml`, solution `.sln`, Dockerfile ou configuration CI n'a ete detecte.

## Ce que chaque dossier signifie

`docs/` correspond au cerveau du projet : vision, architecture, RBAC, design tokens, modules systeme, mail, DNS, backup, tests, matrices de capacite, dependances et readiness. C'est la source la plus importante pour comprendre ce que WCP360 doit devenir.

`docs/WCP360_CONSOLIDATED_MD_2026-09-27/` correspond au snapshot de pilotage : statut des baselines, conflits ouverts, registres provisoires, gates de readiness et ordre des prochaines decisions.

`docs/WCP-Design-System-Spec-v4.0.21.23-global-canonical-registry-consolidation/` correspond a une archive documentaire large. Elle doit servir a la tracabilite et a la comparaison, pas a remplacer silencieusement le snapshot consolide.

`WCP360 Design System/` correspond a la reference UI locale. Elle est navigable via `index.html` et contient de nombreuses pages HTML statiques couvrant dashboard, sites, DNS, securite, serveurs, monitoring, backups, mail, users, jobs, etc.

`dashboard-html-css-javascript/` correspond a un prototype dashboard autonome. Son titre HTML indique encore `Dashboard - CodingNepal`, donc il doit etre traite comme brouillon ou inspiration, pas comme produit WCP360 officiel.

`WCP360 Official html website/` et `WCP360 Source Code/` correspondent a des intentions de structure, mais pas a des contenus actuels.

## Points de coherence

1. Le corpus documentaire et le prototype UI sont separes, ce qui est sain.
2. Les dossiers vides doivent rester marques comme reserves tant que leur contenu n'existe pas.
3. Le design local semble aller jusqu'a `V2.5.3.32`, tandis que le snapshot consolide cite `DESIGN-BASELINE = V2.5.3.36.4`. L'archive `V2.5.3.36.4` n'est pas presente localement dans ce workspace.
4. Le statut officiel interdit de demarrer une implementation produit tant que les gates H3-CM, H3-V2, Baseline Manifest et Implementation Readiness ne sont pas fermes.
5. La documentation de test existe au niveau exigences, mais il n'y a pas encore de tests executables produit.

## Organisation recommandee

Organisation logique actuelle :

```text
.
├── README.md
├── PROJECT_AUDIT.md
├── tools/
│   └── test-project.ps1
├── docs/
│   ├── README.md
│   ├── INDEX.md
│   ├── MASTER-*.md
│   ├── WCP360_CONSOLIDATED_MD_2026-09-27/
│   └── WCP-Design-System-Spec-v4.0.21.23-global-canonical-registry-consolidation/
├── WCP360 Design System/
├── dashboard-html-css-javascript/
├── WCP360 Official html website/
└── WCP360 Source Code/
```

Organisation cible apres GO implementation :

```text
.
├── apps/
│   ├── control-panel-ui/
│   ├── official-website/
│   └── admin-api/
├── packages/
│   ├── design-system/
│   └── contracts/
├── docs/
├── prototypes/
├── tests/
└── tools/
```

Cette structure cible ne doit pas etre appliquee maintenant sans decision de baseline, car elle melangerait specification, prototype et implementation.

## Tests effectues

Controle local effectue pendant l'audit :

- 4041 references locales HTML verifiees;
- 0 reference locale HTML manquante;
- 330 references d'index Markdown verifiees;
- 0 reference d'index Markdown manquante.

Le script `tools/test-project.ps1` rend ces controles rejouables.

## Prochaines actions

1. Recuperer ou confirmer l'archive design `V2.5.3.36.4`, ou ajuster le statut si `V2.5.3.32` est la seule reference locale disponible.
2. Ingerer le detail final `H3-CM-C05 / TESTS-QA`, indique comme termine mais absent du snapshot accessible.
3. Fermer les conflits H3-CM X001 a X013.
4. Produire un Baseline Manifest approuve.
5. Initialiser le vrai code seulement apres GO, dans `WCP360 Source Code/` ou dans une structure `apps/` decidee.
