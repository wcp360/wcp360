# 1000 --- Repository Topology

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

## Monorepo V1

``` text
wcp/
├── apps/
│   ├── wcp-admin/          # PHP Web/API administrative
│   └── wcp-user/           # PHP Web/API client/revendeur
├── services/
│   ├── wcpd/               # Go control-plane/agent service
│   ├── wcp-privd/          # Go broker root minimal
│   └── wcp-worker/         # Go/PHP worker selon job ownership
├── packages/
│   ├── php/
│   │   ├── domain/
│   │   ├── application/
│   │   ├── infrastructure/
│   │   └── contracts/
│   └── go/
│       ├── protocol/
│       ├── actionregistry/
│       ├── adapters/
│       └── observability/
├── api/
│   ├── openapi/
│   └── schemas/
├── database/
│   ├── migrations/
│   └── seeds/
├── deploy/
│   ├── systemd/
│   ├── packaging/
│   └── config/
├── tests/
│   ├── contract/
│   ├── integration/
│   ├── security/
│   └── e2e/
├── docs/
└── tools/
```

## Règles

Le repository de développement n'est pas le layout de production. Une
release construite est installée sous `/opt/wcp/releases/<version>`.
Aucun code runtime n'écrit dans son checkout/release.

## V4.0.10 normative deepening

- `OPS-4010-06-LOGGING-CANONICAL.md`
- `OPS-4010-07-LOG-PIPELINE-RETENTION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
