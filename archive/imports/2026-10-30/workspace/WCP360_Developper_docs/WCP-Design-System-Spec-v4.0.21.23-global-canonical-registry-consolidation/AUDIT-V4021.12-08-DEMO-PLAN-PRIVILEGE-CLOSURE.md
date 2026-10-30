# V4.0.21.12 --- DEMO / Plans / Privilege Closure

## DEMO

DEMO est un account/session mode et un policy input, pas un rôle
magique. Chaque module/action peut être `VISIBLE_READONLY`, `SIMULATED`,
`HIDDEN`, ou `DENIED`.

DEMO ne crée pas de vrai system user, cgroup, vhost, DB, mailbox, DNS
change, certificate, cron, worker, backup target or privileged job.

## Plans

L'admin peut créer plan par défaut ou personnalisé, activer/désactiver
modules, fixer limites et capabilities dans les ceilings. Un plan ne
remplace jamais RBAC : entitlement autorise la fonctionnalité
commerciale, permission autorise l'acteur.
