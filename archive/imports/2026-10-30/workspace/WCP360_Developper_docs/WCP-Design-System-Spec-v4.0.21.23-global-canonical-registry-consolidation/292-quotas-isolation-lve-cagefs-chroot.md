# 292 --- Quotas & Isolation

## Origine

**\[V3.1 --- exigence ajoutée par le projet\]** Quotas disque/bande
passante et isolation des comptes, avec backends possibles
CloudLinux/LVE, CageFS, chroot ou mécanismes natifs.

## Objectif

Séparer clairement **allocation**, **consommation**, **limite**,
**throttling** et **isolation**.

## Pages

Overview; Resource Policies; Account Quotas; Domain Usage; Isolation;
Violations; History.

## Ressources

CPU, RAM, processus, I/O, IOPS, stockage, inodes, bande passante,
connexions/processus selon capacités du backend.

## Actions

Créer profil; assigner profil; override compte; modifier quotas;
activer/désactiver isolation; rétablir héritage.

## Héritage

Global → Package → Reseller → Account → Resource. L'UI affiche toujours
valeur effective et origine.

## Isolation

LVE/CageFS/chroot sont des adaptateurs distincts. WCP ne prétend pas
offrir la même isolation avec tous les mécanismes.

## Alertes

Near limit; limit reached; throttled; quota exceeded; isolation
degraded.

## États UI obligatoires

Loading, skeleton, empty, no-results, permission denied, partial data,
stale data, unavailable, maintenance, pending, failed et success
confirmé par le backend.

## Règles transversales

-   WCP-ADMIN administre la capacité globale, les services, politiques
    et limites.
-   WCP-USER n'accède qu'aux ressources et fonctions explicitement
    attribuées à son compte/revendeur.
-   Toute mutation sensible est autorisée côté backend, auditée et
    reliée à un correlation/job ID.
-   Toute opération longue ou massive passe par Task Center.
-   Les secrets ne sont jamais écrits dans les logs, notifications,
    audit ou sorties IA.
-   Les modules sont pilotés par capabilities : une technologie non
    installée n'est pas présentée comme disponible.

## V4.0.7 normative deepening

- `SECOPS-407-06-ARCHIVE-CREATE-EXTRACT.md`
- `SECOPS-407-09-SFTP-FTP-ACCOUNT-DEEP.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.
