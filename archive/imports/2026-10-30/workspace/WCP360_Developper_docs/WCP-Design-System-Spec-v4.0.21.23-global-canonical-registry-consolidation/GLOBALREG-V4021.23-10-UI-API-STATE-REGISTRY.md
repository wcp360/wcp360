# V4.0.21.23 --- Global UI / API State Contract

For every resource/action surface, the UI and API must represent:
loading/unknown, empty/not-configured, active/healthy, degraded, blocked
by policy/entitlement/capability, pending Job, failed/retryable,
failed/manual recovery, suspended/locked, deleting and permission-denied
as applicable.

UI cannot infer success before server-side observed verification.
Disabled controls expose reason codes where disclosure is safe. DEMO
mode renders simulation state without privileged side effects.

REST/CLI/UI are projections of the same canonical Operation and resource
state.
