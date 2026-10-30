# 42 --- WCP-USER : Domains & DNS

## Objectif

Couvrir Domains, Redirects, Zone Editor, Dynamic DNS et Aliases.

## Règles obligatoires

-   UX simple et guidée par défaut ; Advanced explicite.
-   Toute ressource affiche quota/limite lorsqu'applicable.
-   Les suggestions IA ne masquent jamais la configuration réelle.
-   Les mutations importantes créent une tâche si elles ne sont pas
    instantanées.
-   Toutes les listes utilisent le Data Grid standard.

## Composants / anatomie

DNS editor en table structurée A/AAAA/CNAME/MX/TXT/SRV, validation
TTL/valeurs, preview diff, templates. Domaine = page ressource avec
DNS/SSL/email/logs. Détection collision et confirmation avant
suppression de zone.

## États à prévoir

loading, empty, quota warning, quota exceeded, provisioning, suspended,
failed, permission denied.

## Responsive

Prioriser consultation et actions urgentes sur mobile ; éditeurs
complexes peuvent proposer une vue simplifiée.

## RBAC et sécurité

Client limité à son scope ; revendeur peut gérer les comptes autorisés.
Les secrets ne traversent jamais les scopes.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-06-DOMAIN-CANONICAL-MODEL.md`
- `HOSTING-404-07-DOMAIN-LIFECYCLE-ORCHESTRATION.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.

## V4.0.5 normative deepening

- `HOSTING-405-01-DNS-ZONE-CANONICAL-MODEL.md`
- `HOSTING-405-02-DNS-RRSET-VALIDATION.md`

Ces contrats précisent la sémantique de cette surface et prévalent en cas d'ambiguïté.
