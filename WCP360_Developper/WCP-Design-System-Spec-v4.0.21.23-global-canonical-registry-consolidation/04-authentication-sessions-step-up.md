# 04 --- Authentification, sessions et élévation

## Objectif

Définir les patterns d'accès communs sans présumer d'un fournisseur
d'identité unique.

## Exigences V2

-   Authentification primaire compatible mot de passe et, si backend
    disponible, passkeys/WebAuthn.
-   2FA pour les comptes privilégiés ; récupération documentée.
-   Sessions listables et révocables.
-   Affichage de la dernière activité, appareil approximatif et origine
    réseau lorsque disponible.
-   Rotation/révocation des tokens.
-   Réauthentification ou **step-up** avant opérations à très fort
    impact.
-   Verrouillage temporaire et rate limiting gérés côté serveur.
-   Aucune indication permettant d'énumérer les comptes dans les erreurs
    publiques.

## Step-up

Déclencheurs recommandés : destruction serveur/cluster, modification
d'authentification privilégiée, export de secret, changement de
politique de sécurité, rotation de clés critiques.

## Session UI

`Security > Sessions` : session courante, autres sessions, date,
activité, révocation. Une révocation produit un événement d'audit.

## Impersonation

Bandeau persistant, initiateur visible, cible visible, horodatage,
sortie immédiate. L'impersonation n'efface jamais l'identité de
l'initiateur dans l'audit.
