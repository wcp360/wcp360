# V4.0.2 --- Audit & Traceability Contract

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## AuditEvent

Actor réel/effectif, action, target, tenant, timestamp, result,
request/correlation/causation, source, policy decision summary et
métadonnées redacted.

## Événements obligatoires

Login/logout/failure, MFA/step-up, session revoke, permission/role
change, impersonation, secret rotation, privileged action, plan/quota
change, create/update/suspend/delete, backup/restore, migration,
security remediation et configuration critique.

## Intégrité

Append-oriented, accès restreint, horodatage cohérent,
rotation/rétention, export contrôlé. L'application ne doit pas pouvoir
effacer discrètement l'historique d'une action sensible.

## Redaction

Jamais de password, private key, token, recovery code, full secret,
session cookie ou contenu sensible inutile.

## V4.0.10 normative deepening

- `OPS-4010-05-OPENTELEMETRY-TRACES.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
