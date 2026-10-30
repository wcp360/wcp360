# V4.0.21.21 --- Restore / Selective Restore / Conflict Contract

Restore supports full account and selective resource classes when
adapters allow: files, DB, DNS, mail, app config and other canonical
resources.

Pipeline: select backup → manifest/integrity → authorization →
destination capability → conflict/impact plan → optional pre-restore
checkpoint → restore → reconcile ownership/secrets/capabilities → health
verification → commit/report.

Overwrite/cross-host restores require explicit impact and step-up
according to risk. Cross-tenant target is denied unless a dedicated
migration/admin operation authorizes ownership transfer.
