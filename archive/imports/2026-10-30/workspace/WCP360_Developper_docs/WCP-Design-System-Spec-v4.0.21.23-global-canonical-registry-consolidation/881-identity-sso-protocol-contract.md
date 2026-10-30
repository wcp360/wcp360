# 881 --- Identity / SSO Protocol Contract

## Statut

**CANONICAL --- V3.8 CLOSURE / IMPLEMENTATION CONTRACT**

Ce document complète le corpus existant. Les règles de sécurité
`650–669` et les contrats `700–874` restent normatifs.

OIDC est le protocole federation prioritaire V1 lorsqu'un IdP externe
est activé. SAML/LDAP/SCIM restent adapters/capabilities lorsqu'ils sont
implémentés.

OIDC : state/nonce/PKCE selon flow, issuer/audience/signature/time
validation. SAML : signature, audience/destination, replay/time checks.
LDAP : TLS et bind secret via SecretStore.

Le provisioning SCIM ne contourne jamais RBAC/tenant scope.
