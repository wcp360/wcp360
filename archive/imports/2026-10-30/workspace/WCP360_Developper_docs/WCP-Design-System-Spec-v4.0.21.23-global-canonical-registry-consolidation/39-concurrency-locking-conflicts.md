# 39 --- Concurrence, verrouillage et conflits

## Cas

Deux administrateurs modifient une configuration, ressource modifiée
pendant édition, job concurrent, version devenue obsolète.

## Pattern V2

-   Utiliser version/ETag ou mécanisme équivalent côté backend lorsque
    pertinent.
-   À conflit : ne pas écraser silencieusement.
-   Montrer `Votre version`, `Version actuelle`, diff et options
    autorisées.
-   Les opérations exclusives affichent le verrou et son propriétaire
    logique si disponible.
-   Les boutons restent idempotents ou protégés contre double
    soumission.

## États

stale, locked, conflict, retryable, non-retryable.
