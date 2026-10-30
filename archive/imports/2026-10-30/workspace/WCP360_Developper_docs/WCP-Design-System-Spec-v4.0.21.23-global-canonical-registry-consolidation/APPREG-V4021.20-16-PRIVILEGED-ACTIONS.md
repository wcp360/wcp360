# V4.0.21.20 --- Files / Access / Runtime Privileged Actions

Closed actions include `MANAGED-FS-MUTATE`, `MANAGED-ARCHIVE-EXTRACT`,
`ACCESS-KEY-APPLY`, `ACCESS-POLICY-APPLY`, `APP-RUNTIME-CONFIG-APPLY`,
`APP-SERVICE-APPLY`, `APP-RELEASE-SWITCH`, `APP-SECRET-MATERIALIZE`.

All privileged inputs use typed IDs/relative managed paths/provider
enums. No tenant-originated arbitrary root path, raw systemd unit,
arbitrary privileged command, sudo rule or unrestricted daemon
environment is accepted.
