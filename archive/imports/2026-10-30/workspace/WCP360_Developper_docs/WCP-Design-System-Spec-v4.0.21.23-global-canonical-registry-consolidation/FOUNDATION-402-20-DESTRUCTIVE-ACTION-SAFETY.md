# V4.0.2 --- Destructive Action Safety

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Classification

Low, medium, high, critical/destructive.

## Pour high/critical

Permission spécifique, step-up éventuel, confirmation contextualisée,
dependency check, impact preview, backup/snapshot lorsque pertinent,
idempotency, lock, audit et recovery plan.

## Suppression

Ne jamais cascader silencieusement entre domaines fonctionnels majeurs.
Une suppression de HostingAccount doit présenter domaines, mail, DB,
backups et autres dépendances affectées.

## Break-glass

Les mécanismes d'urgence sont rares, temporaires, fortement audités et
ne deviennent pas un raccourci d'administration quotidienne.
