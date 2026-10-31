# V4.0.21.5 — Isolation P0 Acceptance Contract

## Statut
**NORMATIVE — V1 STANDALONE — NO CODE — REPOSITORY BLOCKED**

Cette famille transforme les options d’isolation et de commandes de V4.0.21.4.1 en contrats B1/B2 explicites.

## P0
1. A ne peut lire/écrire/exécuter dans B via filesystem, socket, DB, cache, mail ou backup.
2. `..`, symlink, hardlink et archive traversal restent sous managed root.
3. fork bomb atteint `pids.max` sans affecter un autre compte au-delà du plafond parent.
4. memory pressure/OOM est attribuée au bon scope et auditée.
5. CPU saturation respecte effective limit.
6. reboot/service restart conserve ou réconcilie limites et placement.
7. controller absent => `UNAVAILABLE`, jamais `HARD`.
8. quota bytes/inodes est vérifié par readback backend.
9. downgrade sous usage => NONCOMPLIANT/EXISTING_ONLY, zéro suppression implicite.
10. enfant/orphelin ne s’échappe pas du cgroup attendu.
11. temp/session/socket n’est pas cross-tenant.
12. restore cross-tenant non autorisé est refusé avant mutation.
13. reseller ne dépasse jamais platform ceiling.
14. DEMO produit zéro cgroup/quota/process réel.
15. UI/API/CLI obtiennent la même effective decision.
16. un argument CLI malicieux ne devient jamais argv/path/unit root arbitraire.
17. drift manuel est détecté puis visible avant/pendant reconcile.
18. adapter LVE/CageFS/MAC absent n’est jamais déclaré actif.
