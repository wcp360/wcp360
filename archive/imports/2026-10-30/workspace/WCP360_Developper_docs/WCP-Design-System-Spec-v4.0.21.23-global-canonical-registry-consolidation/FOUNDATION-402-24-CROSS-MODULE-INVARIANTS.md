# V4.0.2 --- Cross-module Invariants

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Invariants obligatoires

-   UI/API n'exécute jamais root directement.
-   WCP-USER n'acquiert jamais un scope WCP-ADMIN par paramètre client.
-   Une ressource est autorisée avant chargement de secrets ou action
    privilégiée.
-   Les side effects privilégiés passent par contrats fermés.
-   Toute mutation critique est corrélée et auditable.
-   Les secrets ne transitent pas par events/logs/audit.
-   Un timeout ne prouve ni succès ni absence d'effet.
-   Les suppressions respectent dépendances et rétention.
-   Une capability non certifiée ne devient pas certifiée par simple
    détection.
-   Les fichiers de release sont immuables pour les comptes runtime.
-   Toute donnée tenant persistante possède un ownership/scope
    explicite.
-   Les backups sont inutiles tant qu'un restore vérifié n'est pas
    spécifié.
