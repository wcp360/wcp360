# V4.0.21.15 --- Baseline Product Decisions

## Décisions désormais fermées pour la baseline documentaire

-   WCP 1.0 : single-node production ; primitives multi-node conservées.
-   Isolation : native Linux obligatoire ; CloudLinux adapter optionnel.
-   Containers : optionnels, restricted/rootless preferred, privileged
    denied by default.
-   Groupware : optionnel, hors core hosting.
-   WAF commercial/scanner commercial : adapters optionnels.
-   API : REST canonique pour les opérations du panel ; GraphQL non
    requis pour 1.0.
-   WCP-ADMIN / WCP-USER : terminologie normative.
-   Code/repository : toujours bloqués jusqu'au Global READY Gate.

## Décisions qui restent liées au Host Profile

Distribution/version Linux, web/mail/DNS/database adapters certifiés et
leurs versions supportées sont définis par la future matrice de
certification, pas hardcodés dans le modèle métier.
