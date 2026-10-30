# 882 --- Authentication, Session & MFA Contract

## Statut

**CANONICAL --- V3.8 CLOSURE / IMPLEMENTATION CONTRACT**

Ce document complète le corpus existant. Les règles de sécurité
`650–669` et les contrats `700–874` restent normatifs.

MFA : TOTP et recovery codes constituent la baseline applicative ;
WebAuthn/passkeys peuvent être ajoutés via capability sans modifier le
modèle Identity.

Sessions WCP-ADMIN/WCP-USER restent isolées. Rotation d'ID après
login/step-up ; expiry idle/absolute configurable ; revoke all sessions
disponible.

Recovery est un workflow sensible, rate-limited, audité, et ne révèle
pas l'existence d'un compte au-delà de la policy.
