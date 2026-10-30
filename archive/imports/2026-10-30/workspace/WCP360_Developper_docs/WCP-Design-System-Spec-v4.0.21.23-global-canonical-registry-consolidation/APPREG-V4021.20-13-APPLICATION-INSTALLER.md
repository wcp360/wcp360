# V4.0.21.20 --- Application Installer / Catalog Contract

Catalog entry is versioned metadata: application/version/runtime
requirements, source checksum/signature metadata when available, install
recipe schema, required DB/cache/domain/TLS capabilities and lifecycle
support.

Install pipeline performs compatibility, entitlement, quota, source
integrity, staging, tenant-context install, secret generation, DB
provisioning through canonical operations, health and rollback/cleanup.

Recipes cannot embed unrestricted root shell. Privileged needs must map
to an existing closed action. Third-party catalog trust level is
explicit.
