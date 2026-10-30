# V4.0.4 --- Web/PHP Security & Abuse-case Matrix

## Statut

**NORMATIVE --- V4.0.4 HOSTING CORE DEEP SPECIFICATION --- TRANCHE 1 ---
NO CODE**

Cette spécification approfondit Accounts / Plans / Reseller → Domains →
Web Stack → Apache/Nginx/LiteSpeed → PHP/PHP-FPM. Elle instancie V4.0.2
et V4.0.3 sans produire de code.

## Invariants

-   WCP-ADMIN administre hôte, capacités, politiques, moteurs et limites
    maximales.
-   WCP-USER administre uniquement les ressources déléguées de son
    scope.
-   Hiérarchie commerciale et RBAC restent distincts.
-   Toute ressource possède desired/observed state, generation et
    ownership.
-   Toute mutation sensible suit validate → authorize → entitlement →
    preflight → job/lock → apply → verify → reconcile → audit.
-   Les fichiers web/PHP générés sont des projections, jamais la source
    de vérité.
-   Aucun tenant ne fournit de configuration root arbitraire.

## Cas

Path traversal document root ; symlink escape ; CRLF header injection ;
malicious domain/server_name ; arbitrary include ; config directive
injection ; socket collision ; cross-tenant pool mapping ; PHP
temp/session leakage ; malicious archive upload ; log path traversal ;
open redirect ; spoofed proxy headers ; oversized body; fork/memory
abuse.

## Pour chaque cas

Input boundary, validation, privilege boundary, expected rejection,
audit/log behavior et negative test.

## Principle

Échapper une chaîne pour shell/config n'est pas le modèle principal de
sécurité : préférer schemas, enums, registries et renderers contrôlés.
