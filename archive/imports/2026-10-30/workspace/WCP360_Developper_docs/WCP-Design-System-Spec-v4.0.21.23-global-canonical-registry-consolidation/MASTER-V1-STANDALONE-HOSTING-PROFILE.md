# MASTER V1 STANDALONE HOSTING PROFILE

## Objectif

Définir le premier profil réellement implémentable et testable sans
attendre HA/Kubernetes/datacenter.

## Inclus

WCP-ADMIN + WCP-USER, PostgreSQL control plane, DB-backed jobs/events,
local agent contract, Nginx/Apache, PHP-FPM, MariaDB/MySQL + PostgreSQL
workloads, BIND, Postfix/Dovecot/Rspamd, Redis/Memcached, ACME,
firewall/SSH, backup Restic/local/SFTP/S3, telemetry native + adapters,
WordPress/app tooling, reseller/plans, billing API/connectors, migration
center.

## Non bloquant / optionnel

Ceph, multi-control-plane HA, KVM cluster, SDN avancé, Kubernetes,
bare-metal, Terraform provider, AI providers, commercial license server.

## Definition of Ready V1

Chaque capability incluse doit avoir : canonical spec, permission
matrix, API resource/action, adapter capability, job/event/audit
semantics, error/recovery states, security checklist et tests sur chaque
OS Tier 1 applicable.
