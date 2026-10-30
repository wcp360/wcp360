# V4.0.3 --- PAM, sudo & Administrative Shell Access

## Statut

**NORMATIVE --- V4.0.3 SYSTEM & OS DEEP SPECIFICATION --- NO CODE**

Cette spécification instancie les fondations V4.0.2 pour WCP-ADMIN. Elle
décrit le comportement attendu sans imposer de framework ni produire
d'artefact exécutable.

## Invariants communs

-   WCP-USER n'obtient jamais de primitives d'administration hôte.
-   Toute mutation système est autorisée, bornée, auditable et exécutée
    via une action privilégiée fermée.
-   Aucun champ UI/API ne devient une commande shell, un chemin absolu
    arbitraire, un nom d'unité arbitraire ou une liste d'arguments root.
-   Toute mutation suit **preflight → plan/diff → lock → apply → verify
    → reconcile/rollback → audit**.
-   Timeout ou perte de réponse = état ambigu jusqu'à vérification.
-   L'état affiché provient de l'observation effective et porte un
    timestamp/freshness.

## Frontière

WCP ne devient pas un éditeur générique de PAM/sudoers/sshd_config.

## Sudo

Policies structurées, fichiers dédiés gérés par WCP si la fonction est
retenue, validation syntaxique avant activation, least privilege, aucune
règle `ALL=(ALL) NOPASSWD: ALL` générée pour un tenant.

## PAM

WCP observe provider/capabilities. Toute intégration MFA/PAM future
nécessite ADR sécurité séparée et recovery console documentée.

## SSH

Shell enable/disable, authorized keys et restrictions sont des
ressources. Clés publiques validées par type/taille/policy ; fingerprint
affiché ; commentaire non fiable comme identité.

## Root login

Policy explicite ; WCP ne doit pas exiger SSH root password pour
fonctionner.

## Lockout safety

Toute mutation d'accès admin doit avoir preflight, session de
secours/break-glass et mécanisme de rollback pour éviter un verrouillage
total du serveur.
