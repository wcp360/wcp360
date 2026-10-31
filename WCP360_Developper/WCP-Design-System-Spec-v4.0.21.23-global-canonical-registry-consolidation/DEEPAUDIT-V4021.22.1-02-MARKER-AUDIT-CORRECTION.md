# V4.0.21.22.1 --- Correction de la méthode PARTIAL / DECISION REQUIRED

## Problème identifié

Le scan V4.0.21.22 traite le lexème `PARTIAL` comme un signal
documentaire. Or une grande partie des occurrences décrit des états
normaux : `partial data`, `partial success`, réponse partielle,
restauration partielle, état UI partiel, etc.

Ces occurrences ne sont pas des spécifications incomplètes.

## Nouvelle règle

Un marqueur ne devient candidat à fermeture que s'il est : - un titre ou
statut explicite (`## PARTIAL`, `Status: PARTIAL`) ; - une décision
explicite (`DECISION REQUIRED`) ; - un TODO/TBD normatif ; - une ligne
de gate/readiness déclarant une couverture partielle.

Les occurrences métier/UI ne doivent pas être comptées comme dette
normative.

## Marqueurs réellement significatifs

Les audits V4.0.18 suivants contiennent encore un `## PARTIAL` explicite
: - `AUDIT-V4018-03-API-COVERAGE.md` -
`AUDIT-V4018-04-JOB-RPC-ADAPTER-COVERAGE.md` -
`AUDIT-V4018-05-RBAC-COVERAGE.md` -
`AUDIT-V4018-06-DATABASE-COVERAGE.md` -
`AUDIT-V4018-07-LIFECYCLE-COVERAGE.md` -
`AUDIT-V4018-08-AUDIT-EVENT-COVERAGE.md` -
`AUDIT-V4018-09-SECURITY-TEST-TRACEABILITY.md` -
`AUDIT-V4018-10-E2E-TRACEABILITY.md` -
`AUDIT-V4018-11-PRIVILEGE-BOUNDARY.md` -
`AUDIT-V4018-12-FAILURE-RECOVERY.md` -
`AUDIT-V4018-13-UI-STATE-COVERAGE.md` -
`AUDIT-V4018-15-MIGRATION-VERSIONING.md`

Deux décisions restent également volontairement non fermées au niveau
produit : - fournisseur/protocole de licensing commercial ; - licensing
commercial lui-même reste hors chemin critique V1 si non activé.

Les anciens `DECISION REQUIRED` sur package repository et
version/support doivent être évalués contre les ADR et contrats plus
récents, et non comptés automatiquement comme décisions encore ouvertes.
