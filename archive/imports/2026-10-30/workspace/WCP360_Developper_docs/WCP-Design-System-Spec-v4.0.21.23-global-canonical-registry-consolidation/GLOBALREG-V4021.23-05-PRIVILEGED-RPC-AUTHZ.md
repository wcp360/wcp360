# V4.0.21.23 --- Privileged RPC / Closed Action Authorization Registry

The privileged daemon exposes closed, typed actions only.

Each action contract MUST declare: caller service identity; originating
Operation IDs; typed resource IDs; allowed managed roots/targets;
accepted enums/options; preconditions; timeout; lock; idempotency;
postcondition evidence; compensation/recovery; audit receipt.

Forbidden inputs from tenant-facing surfaces: arbitrary shell, arbitrary
root path, raw privileged SQL, arbitrary service/unit name, raw firewall
command, unrestricted package install, raw root mail/web config.

The daemon independently validates action schema and target constraints;
it does not trust WCP-USER authorization merely because the web control
plane called it.
