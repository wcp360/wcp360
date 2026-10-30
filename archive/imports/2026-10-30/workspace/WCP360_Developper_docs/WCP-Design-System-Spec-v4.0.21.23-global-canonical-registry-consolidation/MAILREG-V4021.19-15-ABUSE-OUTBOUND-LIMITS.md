# V4.0.21.19 --- Mail Abuse / Outbound Limits

Controls include per-account/domain/mailbox authenticated submission
rate, recipient/message burst limits, concurrent submission limits,
suspicious auth events and optional outbound volume anomaly signals.

Limits are policy/plan bounded but platform anti-abuse floor cannot be
weakened by reseller/user. Threshold events integrate with alerts/audit
and may trigger temporary containment according to explicit policy.

Containment must avoid irreversible deletion and supports operator
review.
