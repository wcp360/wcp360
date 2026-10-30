# V4.0.21.6 --- Authentication & Authorization Evaluation Order

## Canonical evaluation

1.  request integrity / CSRF where applicable;
2.  authentication;
3.  principal + session state;
4.  operation existence;
5.  permission;
6.  tenant/resource scope;
7.  reseller/platform ceiling;
8.  plan/entitlement;
9.  resource lifecycle state;
10. MFA/step-up requirement;
11. contextual policy and anti-abuse;
12. execution eligibility;
13. action;
14. audit outcome.

## Step-up classes

`NONE`, `RECENT_AUTH`, `MFA`, `STRONG_MFA`, `BREAK_GLASS`.

Step-up is time-bounded and operation-class aware. A prior low-risk MFA
event does not automatically authorize a later critical operation
indefinitely.

## Denials

Use stable internal reason codes. Public error text avoids
identity/resource enumeration. Authorization explain is itself
permission-protected.
