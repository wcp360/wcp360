# 831 --- WordPress Toolkit Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Fonctions : install, detect/import existing, core/plugin/theme
inventory, update, maintenance mode, clone/staging,
backup-before-risky-change, hardening, vulnerability findings.
Credentials WP/DB ne sont jamais affichés en clair après création. Mise
en quarantaine d'un plugin vulnérable est policy-driven et réversible ;
pas de suppression automatique silencieuse.
