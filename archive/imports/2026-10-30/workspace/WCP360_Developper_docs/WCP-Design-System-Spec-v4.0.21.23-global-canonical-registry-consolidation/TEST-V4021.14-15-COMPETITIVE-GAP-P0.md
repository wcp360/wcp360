# V4.0.21.14 --- Competitive Gap P0 Acceptance

-   IP in-use deletion denied; NAT/public mapping mismatch detected.
-   Dedicated/shared IP reassignment cannot cross tenant authorization.
-   CloudLinux absent → native provider remains coherent.
-   DB governor identity cannot map to wrong tenant.
-   APM trace cannot expose another tenant or secrets.
-   container privileged/host-root/socket escape denied.
-   mail trace tenant leakage denied; destructive queue action step-up
    required.
-   Smart Update failure cannot touch production before approval/policy
    gate.
-   migration conflict cannot silently overwrite destination.
-   single-node baseline remains operable without cluster services.
