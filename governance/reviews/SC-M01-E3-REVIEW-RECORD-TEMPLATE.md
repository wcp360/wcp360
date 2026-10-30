# WCP360 — modèle de relevé de revue indépendante E3

**TEMPLATE NON REMPLI — AUCUNE REVUE NI APPROBATION ENREGISTRÉE.**

Copier une fiche par PR et SHA. Un même relecteur réellement indépendant peut couvrir #108 et #114, avec périmètres, constats et conclusions distincts. SOURCE CODE ne remplit pas cette fiche à la place du relecteur.

## Identité et indépendance

| Champ | À renseigner par le relecteur |
|---|---|
| PR / lien | À RENSEIGNER |
| SHA head effectivement relu | À RENSEIGNER |
| SHA base / merge de test examinés | À RENSEIGNER |
| Arbre / blobs relus | À RENSEIGNER |
| Identité du relecteur / compte ou identité vérifiable | À RENSEIGNER |
| Rôle et compétence sur le périmètre | À RENSEIGNER |
| Lien à l'auteur / participation à la production du changement | À RENSEIGNER |
| Explication vérifiable de l'indépendance technique | À RENSEIGNER |
| Date et référence publique dans le dépôt/PR de l'avis | À RENSEIGNER |
| Classe de preuve réellement satisfaite | À ÉTABLIR — ne pas préremplir E3 |

Un commentaire du même auteur/compte ou processus de génération reste E2. Une CI est E1. Un nom de persona, un sous-agent, CODEOWNERS ou un compte de façade ne prouve pas l'indépendance. Une revue extérieure doit conserver une attribution et une provenance vérifiables.

## Périmètre et méthode

- Autorisation MASTER et versions/blobs normatifs consommés : À RENSEIGNER.
- Fichiers et comportements relus : À RENSEIGNER.
- Checklist du dossier traitée, éléments non traités et motifs : À RENSEIGNER.
- Dépendances et domaines non examinés : À RENSEIGNER.
- Avis Engineering/DevOps/Security/QA utilisés, avec SHA et limites : À RENSEIGNER.
- CI consultées : run/job/attempt, head et commit réellement exécuté : À RENSEIGNER.
- Commandes personnellement exécutées, environnement exact et résultats : À RENSEIGNER, ou NON EXÉCUTÉ.
- Artifact/source archive et digests vérifiés ; vérification de métadonnées vs recalcul des octets : À DISTINGUER.
- Limites des conclusions, tests non exécutés et hypothèses : À RENSEIGNER.

## Constats

Aucun constat n'est prérempli. Ajouter une ligne par constat, ou écrire explicitement « aucun constat identifié dans le périmètre relu » après la revue.

| ID local de revue | Fichier / lignes au SHA | Observation et scénario | Impact / priorité motivée | Correction requise ou réserve | Preuve de vérification / statut |
|---|---|---|---|---|---|
| À RENSEIGNER | À RENSEIGNER | À RENSEIGNER | À RENSEIGNER | À RENSEIGNER | À RENSEIGNER |

Les IDs de cette fiche sont des labels de revue ; ils ne créent aucun TEST-ID ni identifiant de contrat canonique.

## Verdict du relecteur

```text
REVIEWED PR =
À RENSEIGNER

FINAL REVIEWED SHA =
À RENSEIGNER

REVIEWER / INDEPENDENCE EVIDENCE =
À RENSEIGNER

VERDICT =
À CHOISIR ET MOTIVER : APPROVED / REQUEST_CHANGES / RESERVATIONS

REQUIRED CORRECTIONS / OPEN BLOCKERS =
À RENSEIGNER

DEPENDENCIES NOT REVIEWED / LIMITATIONS =
À RENSEIGNER

REVIEW RECORD URL / SIGNED PROVENANCE =
À RENSEIGNER
```

Une réserve non résolue n'est pas transformée en approbation. Le verdict technique n'autorise ni la production, ni la prochaine tranche, ni une CODE-BASELINE. MASTER vérifie ensuite les conditions d'intégration et de poursuite.

## Après changement de SHA ou de base

Consigner ancien/nouveau SHA, delta de blobs/base, nouvelles preuves E1 et décision explicite du relecteur sur l'applicabilité. Un changement substantiel des fichiers relus exige leur nouvelle revue. Une synchronisation documentaire sans modification des blobs peut être explicitement revalidée selon la politique ; cette revalidation n'est jamais automatique. Conserver les anciens constats, résultats et tentatives.
