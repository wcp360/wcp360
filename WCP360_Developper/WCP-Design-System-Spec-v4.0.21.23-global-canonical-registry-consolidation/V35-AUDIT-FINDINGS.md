# V3.5 AUDIT FINDINGS

## Méthode

Audit local des **302 fichiers Markdown V3.4** : recherche de marqueurs
historiques, décisions ouvertes, doublons de noms, chevauchements et
absence de contrats du control plane.

## Constats confirmés

-   `78-coherence-report.md` était obsolète : il annonçait 81 fichiers
    et une cohérence V2.
-   Doublon exact de titre/fonction : `212-backup-destinations.md` /
    `531-backup-destinations.md`.
-   Chevauchements structurants : SSH (`190` vs `550–554`),
    observability (`65`, `260` vs `520–525`).
-   Le control plane WCP lui-même n'avait pas de série de contrats
    équivalente à la profondeur des systèmes administrés.
-   La rétention/export audit reste explicitement ouverte dans `293`.

## Action V3.5

Normalisation sans suppression destructive, création d'un canonical map,
classification historique des anciens rapports et ajout des propositions
control-plane `630–641`.

## QA

-   Anciennes appellations détectées : **aucune**.
-   Occurrences `à définir/TODO/TBD` restantes après normalisation :
    **3**. Elles sont conservées lorsqu'elles représentent une vraie
    décision ouverte plutôt que masquées.
