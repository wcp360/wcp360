# 62 --- IA : approbation et sécurité d'exécution

## Principe

\[SOURCE\] Les missions IA peuvent être limitées et gouvernées ;
l'exemple `audit_firewall` est diagnostique et non destructif.

## Niveaux V2

-   L0 Explain : explication uniquement.
-   L1 Observe : collecte en lecture seule.
-   L2 Recommend : recommandation sans mutation.
-   L3 Prepare : prépare diff/commande/playbook.
-   L4 Execute with approval : exécution après approbation humaine.
-   L5 Automated : réservé à politiques explicitement autorisées,
    bornées et réversibles.

## UI

Chaque résultat affiche modèle/mission, scope, données utilisées,
timestamp, niveau d'autonomie, confiance si réellement calculée, limites
et actions disponibles.

## Interdits

Ne jamais présenter une sortie LLM comme un état système confirmé sans
vérification déterministe.
