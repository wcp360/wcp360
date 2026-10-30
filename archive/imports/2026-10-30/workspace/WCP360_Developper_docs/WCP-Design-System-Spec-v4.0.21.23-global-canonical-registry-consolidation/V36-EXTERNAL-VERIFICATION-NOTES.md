# V3.6 EXTERNAL VERIFICATION NOTES

## Portée

Cette phase est principalement fondée sur le corpus WCP. Une
vérification externe ponctuelle a uniquement servi à vérifier que la
baseline OS proposée correspond à des branches encore maintenues en
septembre 2026.

## Vérification

-   Debian 13 est la stable courante ; Debian annonce un cycle de cinq
    ans avec support complet puis LTS.
-   Ubuntu 24.04 LTS reste sous maintenance standard jusqu'en 2029 ;
    Ubuntu 26.04 LTS existe mais est placé Tier 2 pour limiter la
    matrice initiale.
-   AlmaLinux 9 et Rocky Linux 9 restent dans leur cycle de support ;
    les branches 10 existent mais sont placées Tier 2 initialement.

Ces faits externes ne rendent pas ces OS automatiquement compatibles
avec WCP : **la certification WCP exige la matrice de tests WCP**.
