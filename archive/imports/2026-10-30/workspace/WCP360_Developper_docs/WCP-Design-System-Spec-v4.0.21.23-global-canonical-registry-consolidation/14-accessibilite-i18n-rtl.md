# 14 --- Accessibilité, i18n & RTL

## Objectif

Fixer WCAG 2.2 AA et rendre l'interface traduisible sans reconstruction.

## Règles obligatoires

-   Navigation clavier complète, focus visible, skip links.
-   Labels persistants pour les formulaires ; placeholder jamais utilisé
    comme label.
-   Messages d'erreur reliés au champ et résumés en haut pour
    formulaires longs.
-   `prefers-reduced-motion` respecté.
-   Dates/heures affichent timezone ; nombres/unités localisés.
-   Aucune concaténation de chaînes traduites.
-   RTL via propriétés CSS logiques ; icônes directionnelles miroir si
    nécessaire.

## Composants / anatomie

LanguageSwitcher, LocaleDateTime, RelativeTime, UnitValue,
AccessibleErrorSummary.

## États à prévoir

LTR, RTL, texte long +50 %, zoom 200 %, high contrast, reduced motion.

## Accessibilité

Tests axe automatisés + tests manuels clavier et lecteur d'écran sur
workflows critiques.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
