# 72 --- Definition of Done globale

## Objectif

Établir la checklist commune UI/UX, frontend, backend et QA.

## Règles obligatoires

Une page n'est pas terminée parce que le happy path est beau. Elle doit
satisfaire les critères ci-dessous.

## Composants / anatomie

### Design

-   Tokens uniquement ; Light/Dark/System.
-   Desktop/tablette/mobile documentés.
-   Loading/empty/error/permission/offline.
-   Copy et terminologie validés.

### Frontend

-   Routes stables, focus management, clavier.
-   Pas de secret dans logs/client storage.
-   Double-submit empêché ; erreurs API gérées.
-   i18n sans chaînes codées en dur.

### Backend / sécurité

-   Autorisation serveur sur chaque action.
-   Validation des entrées.
-   Audit des mutations sensibles.
-   Idempotence ou protection contre répétition pour jobs.
-   CSRF/session/token selon architecture retenue.

### QA

-   Tests unitaires composants critiques.
-   Tests intégration permissions.
-   E2E workflows P0.
-   WCAG 2.2 AA : automatisé + manuel.
-   Test texte long/RTL si langue activée.
-   Test erreurs réseau et concurrence.

## Critères d'acceptation

Aucun écran P0 ne peut être livré avec état loading/error/permission non
défini ou action destructive sans confirmation adaptée.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
