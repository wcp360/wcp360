# V4.0.21.9 --- Mail Deliverability & Authentication Resources

## `dkim_key`

`id`, `mail_domain_id`, `selector`, `algorithm`, `private_key_ref`,
`public_record_material`, `state`, `activated_at?`, `retire_after?`,
`version`.

## `spf_policy`

`id`, `mail_domain_id`, `mechanisms`, `qualifier_policy`,
`rendered_record`, `validation_state`, `version`.

## `dmarc_policy`

`id`, `mail_domain_id`, `policy`, `subdomain_policy`, `percentage`,
`alignment_dkim`, `alignment_spf`, `aggregate_report_targets`,
`forensic_policy`, `version`.

## `mta_sts_policy`

`id`, `mail_domain_id`, `mode`, `mx_patterns`, `max_age`,
`policy_revision`, `status`, `version`.

## `tls_rpt_policy`

`id`, `mail_domain_id`, `report_targets`, `status`, `version`.

## `mail_autoconfig_profile`

`id`, `mail_domain_id`, `imap_endpoint_ref`, `smtp_endpoint_ref`,
`autodiscover_state`, `autoconfig_state`, `version`.

## Rules

DKIM private keys are non-exportable through normal surfaces. DNS
publication and observed DNS state are distinct.
SPF/DMARC/MTA-STS/TLS-RPT rendering is typed and validated; WCP does not
claim healthy deliverability merely because a desired record exists.
