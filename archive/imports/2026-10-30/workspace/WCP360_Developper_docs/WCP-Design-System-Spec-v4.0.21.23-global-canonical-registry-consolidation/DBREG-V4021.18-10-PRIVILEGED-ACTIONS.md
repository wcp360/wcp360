# V4.0.21.18 --- Database / Cache Privileged Actions

Closed actions: `DB-CREATE`, `DB-DROP`, `DB-PRINCIPAL-CREATE`,
`DB-PRINCIPAL-ALTER`, `DB-PRINCIPAL-DROP`, `DB-GRANT-APPLY`,
`DB-IMPORT-RUN`, `DB-EXPORT-RUN`, `CACHE-CONFIG-APPLY`,
`CACHE-SERVICE-RESTART`, `DB-GOVERNOR-MAP-APPLY`.

Inputs are typed identifiers/options generated from canonical resources.
No raw root SQL, shell command, arbitrary config path or arbitrary
service name is accepted from WCP-USER/API payloads.
