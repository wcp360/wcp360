# Lock / Concurrency / Exclusion Matrix

## Objective

Prevent conflicting jobs from corrupting state or producing ambiguous outcomes.

| Operation A | Operation B | Same resource? | Rule |
|---|---|---:|---|
| DB restore | DB restore | yes | mutually exclusive |
| DB restore | DB import | yes | mutually exclusive |
| DB restore | DB export | yes | export blocked during restore |
| App deploy | App deploy | same env | serialize |
| App deploy | Full restore | same env | mutually exclusive |
| App maintenance toggle | Deploy | same env | allowed only by workflow ordering |
| Firewall apply | Firewall apply | same server | mutually exclusive |
| Firewall apply | Server reboot | same server | reboot waits for stable policy |
| Server reboot | Package update requiring reboot | same server | one lifecycle job owns reboot |
| Server reboot | Node drain | same server | drain precedes reboot |
| Cluster failover | Cluster failover | same cluster | mutually exclusive |
| Cluster failover | DB primary promotion | same data plane | coordinated single orchestration |
| Volume expand | Filesystem expand | same volume | ordered dependency |
| Volume expand | Snapshot | same volume | provider-specific; default serialize |
| Service restart | Config apply | same service | config apply owns reload/restart |
| Process KILL | Service restart | same service | process action rejected if service orchestration owns state |
| Plan edit | Account provisioning | same plan | provisioning pins plan revision |
| Role edit | Permission-sensitive job | actor role | running job retains captured authorization context but execution re-check policy applies to privileged steps |
| Account suspension | Scheduled automation | same account | new executions blocked; running jobs follow policy |
| Secret rotation | Deploy | same app | deploy pins secret/config revision |
| DNS edit | DNS edit | same zone/revision | optimistic concurrency conflict |

## Lock scope types

- resource lock;
- environment lock;
- server lifecycle lock;
- service configuration lock;
- cluster orchestration lock;
- account governance lock.

## Lock metadata

Expose:
- lock owner job;
- operation;
- acquired time;
- resource;
- cancellability;
- blocking reason.

## Deadlock rule

Cross-resource workflows must acquire locks in a deterministic ordering.

Suggested logical ordering:
`account → project → application → service/resource → infrastructure target`.

Do not dynamically acquire lower-level then higher-level locks in reverse order.