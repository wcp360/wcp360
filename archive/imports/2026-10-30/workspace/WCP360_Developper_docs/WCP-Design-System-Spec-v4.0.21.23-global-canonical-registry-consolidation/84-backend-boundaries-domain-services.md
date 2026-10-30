# 84 --- Frontières backend et services de domaine

## Origine

\[SOURCE\] Les documents décrivent Laravel comme couche centrale et,
pour l'IA, un moteur séparé Node/Ollama.

## V2

Séparer orchestration métier, adaptateurs système et présentation API.
Les commandes système ne sont pas construites directement depuis des
chaînes UI.

## Adaptateurs

Web server, DNS, mail, DB, firewall, backup, runtime et compute exposent
des capacités normalisées tout en conservant leurs spécificités.

## Idempotence

Créer, suspendre, redémarrer, restaurer et migrer définissent leur
comportement en cas de répétition.

## Audit

L'audit est déclenché au niveau de l'action métier, pas seulement par le
clic frontend.
