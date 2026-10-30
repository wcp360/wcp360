# 773 --- Redis / Memcached Adapter Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

## Contrat

Inventory, instance/pool create when architecture supports it,
bind/socket/network policy, memory/quota, auth capability, health,
restart/reload through registry.

## Sécurité

Aucune exposition réseau publique par défaut ; attribution tenant
explicite ; credentials write-only lorsque le backend en utilise.
