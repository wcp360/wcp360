# 367 --- Impersonation / Log in as User

## Origine

**\[V3.2\]** Support administrateur/revendeur en session utilisateur.

## Autorisation

Admin peut impersoner selon policy; revendeur uniquement ses propres
utilisateurs. Capability dédiée, éventuellement MFA/step-up.

## UX

Bandeau persistant impossible à confondre : initiateur, utilisateur
cible, scope, début de session, bouton `Exit impersonation`.

## Audit

Chaque action conserve **actor réel + actor impersonné**. Début/fin et
mutations sont audités.

## Restrictions

Actions ultra-sensibles peuvent être interdites en impersonation (ex.
changer MFA, révéler secrets, opérations de billing) selon policy.

## Sécurité

Pas de partage/copie du mot de passe utilisateur; l'impersonation est
une délégation de session contrôlée.

## Contrat WCP

-   **WCP-ADMIN** configure moteurs, catalogues, politiques, capacités
    et limites.
-   **WCP-USER** expose uniquement les fonctions attribuées au
    client/revendeur.
-   Toute mutation sensible est vérifiée côté backend, auditée et
    corrélée à un job/événement.
-   Les fonctionnalités sont pilotées par `capabilities`; aucune
    intégration tierce n'est supposée installée.
-   États obligatoires : loading, empty, denied, unavailable, partial,
    stale, pending, failed, success confirmé.
