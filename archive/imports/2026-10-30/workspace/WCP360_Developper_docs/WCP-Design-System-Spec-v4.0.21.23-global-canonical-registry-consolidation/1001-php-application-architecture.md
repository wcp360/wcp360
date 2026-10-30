# 1001 --- PHP Application Architecture

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

## Couches

``` text
HTTP/UI
  ↓
Controller / Request DTO
  ↓
Application Service / Use Case
  ↓
Authorization + Domain
  ↓
Repository / Daemon Client / Adapter Port
```

## Namespace indicatif

`Wcp\Admin\`, `Wcp\User\`, `Wcp\Domain\`, `Wcp\Application\`,
`Wcp\Infrastructure\`, `Wcp\Contracts\`.

## Interdictions

Controller → SQL direct ; Controller → shell ; Controller → `wcp-privd`;
template → service système.

Les deux apps partagent les contrats/domain packages, mais conservent
bootstrap, sessions, routes et policies de surface distincts.
