# 1002 --- Go Service Architecture

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

## `wcpd`

Packages : `cmd/wcpd`, `internal/controlplane`, `internal/jobs`,
`internal/policy`, `internal/adapters`, `internal/inventory`,
`internal/health`, `internal/ipc`.

## `wcp-privd`

Packages : `cmd/wcp-privd`, `internal/protocol`, `internal/actions`,
`internal/validation`, `internal/executor`, `internal/audit`.

## Règle

`wcp-privd` ne dépend pas des packages UI, billing, WordPress, support
ou orchestration générale. Il ne connaît que des actions privilégiées
fermées.
