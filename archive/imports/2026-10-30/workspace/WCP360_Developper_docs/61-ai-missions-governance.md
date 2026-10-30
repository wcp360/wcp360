# 61 --- Missions IA & gouvernance

## Objectif

Formaliser la gouvernance des missions IA locales/cloud et leur
représentation dans l'interface.

## Règles obligatoires

-   Chaque mission déclare ID, version, objectif, sources lues,
    permissions, mode read/write, timeout et format de sortie.
-   Read-only par défaut.
-   Une recommandation n'est jamais présentée comme une vérité certaine
    ; afficher preuves/données utilisées.
-   Une mission write exige politique explicite + validation humaine
    selon risque.
-   Historiser prompt système/version modèle/résultat de façon
    compatible avec politique de confidentialité, sans secrets.
-   Les actions générées passent par les mêmes validations backend que
    les actions manuelles.

## Composants / anatomie

MissionCard, MissionRunner, Finding, Evidence, RiskBadge,
ProposedChange, ApprovalPanel, ExecutionResult, AIHistory, PolicyEditor.

## Exemples WC

`audit_firewall` : collecte règles → constats déterministes → rapport IA
→ recommandations. Aucun changement automatique dans le scénario source.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
