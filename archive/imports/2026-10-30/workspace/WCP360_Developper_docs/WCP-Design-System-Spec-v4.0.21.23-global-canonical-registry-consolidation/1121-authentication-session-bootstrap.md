# 1121 --- Authentication & Session Bootstrap

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

WCP-ADMIN local auth est la seule méthode nécessaire au Sprint 0. MFA
TOTP/recovery est prévue dans le modèle ; l'activation obligatoire peut
être policy-driven.

Session cookie admin distinct, Secure, HttpOnly, SameSite adapté ; ID
régénéré au login/step-up ; idle + absolute expiry ; logout et
revoke-all.

Le bootstrap du premier owner ne doit pas créer un mot de passe par
défaut. Utiliser un mécanisme d'initialisation local explicite et à
usage unique, supprimé/désactivé après succès.
