# 95 --- V3.4 Systems & Operations Completeness

> **V3.5 — statut : HISTORICAL.** Journal de la passe concernée ; ne constitue pas la source de vérité actuelle.


## Objectif

Fermer les lacunes du second audit V3.3 en profondeur système et
exploitation.

## P0 ajoutés

Linux networking/firewall adapters ; DNS authoritative avancé ; Mail
Deliverability ; Telemetry ; Backup engines/destinations ; SSH Manager ;
Service Accounts ; ACME/TLS avancé.

## P1 ajoutés

Shared/Object storage ; MFA Recovery ; SCIM ; `wcpctl`; SDK/IaC contract
; plugin trust ; audit/SIEM.

## P2 préparés

WireGuard/VPN, bare-metal Redfish/IPMI/PXE, Kubernetes/K3s,
Terraform/OpenTofu provider, Ansible collection, Marketplace.

## Garde-fou

Les modules P2 sont des points d'extension : ils ne doivent pas gonfler
le MVP hosting ni imposer leurs dépendances au cœur.
