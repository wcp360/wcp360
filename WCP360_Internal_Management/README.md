# WCP360 INTERNAL

This directory is the private vendor control plane. It is a separate product and deployment boundary, not a WCP-ADMIN role and not a Linux root session.

It must live in a private repository in production. Public builds use a positive allowlist rooted in WCP360 Design System; build jobs must fail if a path from this directory, an internal hostname, a private identity configuration or a vendor secret appears in a public artifact.

Access uses the dedicated workforce identity provider, phishing-resistant MFA and short sessions. Customer ROOT, RESELLER and USER identities are never valid here.

The optional com.wcp360.insights-connector extension is built by a separate worker and published through a separate signed extension index. Its package, SBOM, provenance, compatibility range and revocation record never share the Stable core manifest. It may send only customer-approved, minimized diagnostics. Removing or disabling it leaves WCP-ADMIN and all hosting workloads operational.
