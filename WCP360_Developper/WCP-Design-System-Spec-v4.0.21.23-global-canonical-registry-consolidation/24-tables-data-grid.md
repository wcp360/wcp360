# 24 --- Tables et Data Grid

Les tables constituent un composant central du panneau.

## Capacités standard

-   recherche ;
-   tri ;
-   filtres ;
-   pagination ;
-   sélection de lignes ;
-   bulk actions ;
-   choix des colonnes ;
-   densité ;
-   refresh ;
-   export lorsque pertinent ;
-   vues enregistrées lorsque pertinent.

## États obligatoires

-   loading / skeleton ;
-   vide ;
-   aucun résultat de recherche ;
-   erreur ;
-   données partielles ;
-   permission insuffisante.

## Actions

Les actions fréquentes peuvent être visibles. Les actions secondaires
utilisent un menu contextuel. Une action destructive ne doit jamais être
placée à proximité immédiate d'une action fréquente sans distinction
claire.

## Responsive

Ne jamais réduire aveuglément une table complexe. Prioriser les
colonnes, permettre l'ouverture d'une ligne en fiche ou utiliser une
présentation adaptée au mobile.

## Spécification détaillée

-   Header sticky autorisé pour longues listes.
-   Colonnes : identité à gauche, statut proche du nom, actions à
    droite.
-   Sélection multi-lignes affiche une Bulk Action Bar avec nombre
    sélectionné.
-   Filtres actifs sont visibles sous forme de chips et
    réinitialisables.
-   Les préférences de colonnes peuvent être persistées par utilisateur.
-   Virtualisation requise pour très grands datasets ; pagination
    serveur privilégiée.
-   Tri/filtre côté serveur pour listes opérationnelles volumineuses.
-   Une ligne entière ne doit pas être cliquable si elle contient
    plusieurs contrôles ambigus.

## Responsive

Conserver les colonnes identité/statut/action. Les autres passent dans
un panneau de détails ; ne jamais cacher silencieusement une donnée
critique.

## Accessibilité

Utiliser table sémantique quand le contenu est tabulaire. Data grid ARIA
seulement si interactions de grille réelles. Tri annoncé, sélection
accessible, focus clavier déterministe.
