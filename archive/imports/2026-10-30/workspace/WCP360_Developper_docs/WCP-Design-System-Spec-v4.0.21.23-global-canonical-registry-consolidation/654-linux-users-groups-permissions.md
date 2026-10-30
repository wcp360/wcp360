# 654 --- Linux Users, Groups & Permissions

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

## Comptes de service

`wcp-admin`, `wcp-user`, `wcp-daemon`, `wcp-worker`, `wcp-update` sont
des system users, sans mot de passe interactif et avec shell `nologin`
sauf exception documentée.

`wcp-privd` est le nom du service privilégié ; il s'exécute root
uniquement lorsque ses capabilities l'exigent.

## Ownership

Code/releases : `root:root`; runtime users lecture seulement. Les
sous-répertoires de `/var/lib/wcp` reçoivent l'ownership minimal par
composant, jamais un droit d'écriture global par commodité.

## MUST NOT

`0777`, compte `www-data` partagé pour tout WCP, SSH interactif pour
comptes de service, ou groupe unique donnant accès à tous les
secrets/sockets.
