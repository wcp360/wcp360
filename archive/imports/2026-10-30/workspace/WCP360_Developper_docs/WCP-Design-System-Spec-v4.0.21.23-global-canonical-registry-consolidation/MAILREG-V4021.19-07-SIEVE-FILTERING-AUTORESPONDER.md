# V4.0.21.19 --- Sieve / Filtering / Vacation

Typed UI rules compile to provider filter representation. Advanced raw
Sieve, if supported, is explicitly permissioned and validated; it never
becomes a root/system configuration channel.

Rules cover sender/recipient/header/size/spam-score predicates and
move/copy/ discard/redirect/reject/vacation actions according to
provider capability.

Loop prevention, vacation rate limiting, redirect abuse controls and
syntax validation are mandatory.
