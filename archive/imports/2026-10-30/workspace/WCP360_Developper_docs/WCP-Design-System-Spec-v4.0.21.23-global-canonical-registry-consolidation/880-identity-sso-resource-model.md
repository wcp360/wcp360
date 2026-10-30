# 880 --- Identity / SSO Resource Model

## Statut

**CANONICAL --- V3.8 CLOSURE / IMPLEMENTATION CONTRACT**

Ce document complète le corpus existant. Les règles de sécurité
`650–669` et les contrats `700–874` restent normatifs.

Ressources : `Identity`, `Credential`, `MfaEnrollment`, `Session`,
`IdentityProvider`, `FederationMapping`, `RecoveryMethod`, `AuthPolicy`.

Local auth et federation sont deux méthodes d'authentification vers la
même Identity WCP. Un utilisateur fédéré n'obtient aucun rôle par simple
présence chez l'IdP : mapping/assignment explicite requis.
