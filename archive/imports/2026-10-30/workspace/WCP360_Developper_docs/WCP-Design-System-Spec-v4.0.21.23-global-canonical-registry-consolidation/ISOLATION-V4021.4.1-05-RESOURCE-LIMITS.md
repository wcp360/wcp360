# V4.0.21.4.1 — Resource Limit Options

## Dimensions
CPU : weight + quota/période. RAM : `memory.high`, `memory.max`, swap policy. Process : `pids.max`, workers/process limits. I/O : weight/read/write/IOPS uniquement si fiable. Disque : bytes + inodes + grace. Bande passante : comptage séparé du hard-throttle. Runtime : PHP workers, Node/Python/Ruby/Go processes, cron/worker concurrency, DB connections, Redis memory, mailbox quota, backup concurrency, API rate limits.

## Sécurité
Prévoir limites de taille upload, archive/extraction, nombre de fichiers par opération, request body, timeout, export logs, backup/restore concurrency.

## Résolution
`platform ceiling ∩ reseller ceiling ∩ plan ∩ account override ∩ resource override ∩ backend capability ∩ security floor`.
