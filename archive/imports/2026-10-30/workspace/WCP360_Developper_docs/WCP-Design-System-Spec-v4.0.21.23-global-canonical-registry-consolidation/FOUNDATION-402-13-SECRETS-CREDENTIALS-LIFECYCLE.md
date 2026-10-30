# V4.0.2 --- Secrets & Credentials Lifecycle

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Cycle

Generate/import → store → reference → deliver minimally → rotate →
revoke → expire/destroy → audit.

## Classes

User credentials, API/service tokens, DB passwords, mail credentials,
DNS/registrar tokens, backup credentials, ACME keys, SSH private
material et encryption keys.

## Principes

Jamais dans repository, URL, Job payload durable non chiffré, logs ou
audit. Séparation par consommateur et tenant. Secret affiché une seule
fois lorsque nécessaire.

## Rotation

Définir dual-validity ou staged rotation quand le service le permet. Une
rotation échouée ne doit pas supprimer le credential encore fonctionnel
avant vérification.

## V4.0.12 normative deepening

- `LIFE-4012-15-DEPRECATION-EOL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
