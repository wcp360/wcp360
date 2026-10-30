# V4.0.21.2 --- Plan & Trial Decision Registry

## `DEC-PLAN-IMPACT-001`

Input: current observed usage/resources + target effective
entitlements. - compatible → apply assignment/ceiling and reconcile; -
target below observed usage → mark `NONCOMPLIANT`, block new over-limit
creation, **do not delete**; - target disables module with resources →
`EXISTING_ONLY`; - security floor violation → reject.

## `DEC-TRIAL-PROVISION-001`

Input: TrialPolicy + selected allowed plan + anti-abuse policy. - valid
→ real provisioning with trial limits; - invalid/expired policy → reject
before Job; - any requested feature beyond trial ceiling → reject.

## `DEC-DEMO-ROUTE-001`

Input: `account_mode=DEMO` + DemoPolicy module rule. - HIDDEN/DISABLED →
deny; - READ_ONLY → read demo store only; - SIMULATED → simulation
router + demo store; - any real mutation classification → deny.
