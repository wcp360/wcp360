# AUDIT-WCP-0100 — Index consolidé du rapport

**Publication mise à jour :** 2026-10-02 — Europe/Paris  
**Statut :** REVIEW — 18 constats OPEN, avis propriétaires attendus  
**Main examiné :** `aab5033f09a9b9d459d5c723344d7629306fd5ae`  
**PR de publication :** [#40](https://github.com/yyogas/wcp360/pull/40)  
**Suivi des constats :** [#41](https://github.com/yyogas/wcp360/issues/41)  
**Gate d'implémentation :** [#13](https://github.com/yyogas/wcp360/issues/13)

## Lire le dossier complet

| Document | Contenu |
|---|---|
| [Rapport principal](README.md) | 16 constats détaillés, preuves, impacts, responsables, critères de clôture, scénarios adverses, structure proposée et ordre de correction. |
| [Complément du 2 octobre 2026](COMPLEMENT-2026-10-02.md) | Publication de l'analyse complémentaire, état actualisé, deux constats de sécurité supplémentaires, handoffs ciblés et table de couverture de tous les sujets de la réponse conversationnelle. |
| [Preuve locale du gitignore](gitignore-reproduction.json) | Reproduction déjà présente dans la PR avant ce complément ; ce n'est pas un résultat de tests applicatifs WCP360. |

Le rapport principal et sa preuve locale sont conservés sans modification. Ce dossier complète l'audit existant : aucune seconde branche ou seconde autorité d'audit n'est créée. Les sources normatives sont liées à des commits précis dans les rapports.

## Registre des 18 constats

Tous les statuts ci-dessous sont **OPEN**. Les impacts sur SC-M01 sont à examiner selon le composant concerné ; les recommandations ne deviennent pas approuvées par leur simple publication.

| ID | Objet | Propriétaire principal / coopération | Fiche |
|---|---|---|---|
| AUDIT-WCP-0101 | Présence des artefacts et aptitude vérifiée à l'implémentation | MASTER / Audit | [Principal](README.md) |
| AUDIT-WCP-0102 | Exports DB, QA final et Engineering manquants | Database / QA / Engineering — #6, #7, #9 | [Principal](README.md) |
| AUDIT-WCP-0103 | Contrats API de transport, schémas, réponses et profils | API / IPC / Contracts | [Principal](README.md) |
| AUDIT-WCP-0104 | Mappings permission/action/parent/primitive | API / Security / Audit | [Principal](README.md) |
| AUDIT-WCP-0105 | Frontière backend/worker vers wcpd et contexte de confiance | API / Security / DevOps | [Principal](README.md) |
| AUDIT-WCP-0106 | Idempotence persistante et règles de clé | API / Database / Security | [Principal](README.md) |
| AUDIT-WCP-0107 | Phases des timeouts et deadlines IPC | API / DevOps / QA | [Principal](README.md) |
| AUDIT-WCP-0108 | Durabilité, concurrence et récupération après effet incertain | Database / API / Security / SRE | [Principal](README.md) |
| AUDIT-WCP-0109 | Enforcement des protections du dépôt et CI | DevOps / Engineering | [Principal](README.md) |
| AUDIT-WCP-0110 | Preuve de revue technique indépendante | Engineering / Audit / MASTER | [Principal](README.md) |
| AUDIT-WCP-0111 | Exclusions gitignore trop larges | Engineering / DevOps | [Principal](README.md) |
| AUDIT-WCP-0112 | Miroir 147 Markdown et livraison du corpus DOC | MASTER / Documentation / Design — #16 | [Principal](README.md) |
| AUDIT-WCP-0113 | Entrées de build, toolchain, droits et secrets applicables | DevOps / Engineering / Security | [Principal](README.md) |
| AUDIT-WCP-0114 | Assertions QA et preuves d'exécution | QA — #7 | [Principal](README.md) |
| AUDIT-WCP-0115 | Autorité métier et hiérarchie documentaire | MASTER / Audit / propriétaires | [Principal](README.md) |
| AUDIT-WCP-0116 | Périmètre de la fondation et modules futurs | Modules / Engineering / MASTER / QA | [Principal](README.md) |
| AUDIT-WCP-0117 | Isolation des cookies et topologie USER/ADMIN | Security / DevOps / QA | [Complément §6](COMPLEMENT-2026-10-02.md) |
| AUDIT-WCP-0118 | Priorités de permissions et intersection des contraintes | Security / API / Database / QA | [Complément §7](COMPLEMENT-2026-10-02.md) |

Les fiches 0117 et 0118 qualifient explicitement des risques conditionnels ou ambiguïtés à vérifier, pas des vulnérabilités exploitées. Les handoffs du complément §9 donnent les demandes bornées et les preuves attendues ; leur publication ne démontre pas une réception ou une exécution automatique par les autres discussions.

## Décisions et suivis préservés

```text
WCP360-BM-0001 = FINAL — statut officiel inchangé
DOC-BASELINE = v4.0.21.23
DESIGN-BASELINE = V2.5.3.36.4
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

Le ratio 5/8 porte sur la présence des familles d'artefacts requises ; il n'est pas un pourcentage d'avancement du produit. Les statuts officiels restent portés par les documents du MASTER. Ce dossier n'ajoute pas silencieusement un neuvième artefact au gate.

Les publications propriétaires restent suivies par #6, #7 et #9 ; le corpus DOC par #16. #41 suit les constats d'audit et #13 reste le gate parent. Aucun de ces tickets n'est fermé par ce rapport. Une fusion de la PR publierait les documents, sans valider automatiquement leurs recommandations.

## Portée de la présente publication

Deux fichiers Markdown sont ajoutés au dossier existant : ce fichier et `COMPLEMENT-2026-10-02.md`. Aucune spécification canonique, baseline, règle GitHub, migration, unité systemd, application ou politique de production n'est modifiée. Aucun code du Sprint 0 annulé ni V4.1 « Executable Contracts » n'est réintroduit.
