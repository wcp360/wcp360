# 35 --- Audit, historique, diff & rollback

## Objectif

Rendre les opérations administratives traçables et les changements
compréhensibles.

## Règles obligatoires

-   Audit : acteur, IP/session, action, cible, contexte, timestamp,
    résultat.
-   Pour configuration : before/after ou diff lisible.
-   Ne jamais journaliser mot de passe, token complet ou secret.
-   Rollback seulement si réellement supporté et avec preview des
    conséquences.

## Composants / anatomie

AuditTable, AuditDetail, DiffViewer, VersionHistory, RollbackDialog.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
