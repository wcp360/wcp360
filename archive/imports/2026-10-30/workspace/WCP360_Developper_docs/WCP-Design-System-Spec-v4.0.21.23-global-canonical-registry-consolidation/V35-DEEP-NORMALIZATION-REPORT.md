# V3.5 DEEP NORMALIZATION REPORT

## Corpus analysé

319 documents Markdown au début de cette passe.

## Résultats mécaniques

-   Groupes de contenus exactement identiques après normalisation légère
    : **0**.
-   Marqueurs de décision ouverte (`à définir`, `à décider`, `TBD`,
    etc.) : **10**.
-   Familles d'architecture examinées lexicalement : **17**.

## Corrections structurelles de cette passe

-   Clarification de la contradiction `System/OS/Kernel API: No`.
-   Contrat transversal de resource state
    `desired/observed/effective/health/freshness`.
-   Contrat API d'implémentation.
-   Trust boundaries et exigences SSRF/supply-chain.
-   Matrice Failure/Recovery.
-   Gouvernance ADR.
-   Politiques à décider pour versions/support et repositories/packages.
-   Roadmap de normalisation famille par famille.

## Important

Aucun choix de backend de control plane, de base de données, de queue,
de KMS/HSM ou de versions OS n'a été inventé. Ces éléments restent
`DECISION REQUIRED` tant que le corpus ne les fixe pas.
