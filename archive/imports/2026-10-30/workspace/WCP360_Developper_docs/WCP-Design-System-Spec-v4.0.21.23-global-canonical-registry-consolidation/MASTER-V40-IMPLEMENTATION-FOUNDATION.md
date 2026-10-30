# MASTER V4.0 --- Implementation Foundation

## Portée

V4.0 ferme le niveau de détail nécessaire pour commencer le Sprint 0
sans réinterpréter l'architecture.

``` text
Repository
  ↓
PostgreSQL bootstrap
  ↓
Identity / Session / RBAC
  ↓
HTTP/OpenAPI contract
  ↓
Jobs / Outbox / Audit
  ↓
wcpd
  ↓
IPC v1
  ↓
wcp-privd Action Registry
  ↓
systemd/package
  ↓
CI + VM security smoke
```

## Ce qui n'est volontairement pas codé dans cette spécification

Les choix de framework PHP concret, librairie de migrations, format
binaire exact IPC (JSON/protobuf/etc.) et fournisseur de secrets externe
ne sont pas imposés par les sources V3.9. Ils nécessitent une ADR si
l'équipe veut les figer avant le premier commit.
