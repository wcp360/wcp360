# Premium connector catalogue

**60 proposals · 10 categories · design only · not installable**

[CATALOG.json](CATALOG.json) is authoritative. Each entry below links to its three-file proposal record; the JSON mirrors are synchronized with `packages`. Read [the catalogue contract](README.md), [editions](../../documentation/development/EDITIONS-AND-PREMIUM.md) and [Core licensing](../../WCP360_Core/LICENSING.md) before using this inventory.

Every entry has `kind: plugin`, `tier: pro`, `status: design`, `installable: false`, `source_model: undecided` and no chosen package dependencies. Provider-specific behavior and compatibility remain acceptance work. None of the seven `engine.*` catalogue keys is assigned here.

<a id="cloud-infrastructure"></a>

## Cloud infrastructure

Selected external accounts, projects, regions and node resources.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.aws-ec2-pro` | [AWS EC2 infrastructure connector](aws-ec2-pro/README.md) | Platform, Operations, Tenancy | `automation.terraform-opentofu`, `compute.node-groups` |
| `plugin.hetzner-cloud-pro` | [Hetzner Cloud infrastructure connector](hetzner-cloud-pro/README.md) | Platform, Operations, Tenancy | `automation.terraform-opentofu`, `compute.node-groups` |
| `plugin.digitalocean-pro` | [DigitalOcean infrastructure connector](digitalocean-pro/README.md) | Platform, Operations, Tenancy | `automation.terraform-opentofu`, `compute.node-groups` |
| `plugin.ovhcloud-infrastructure-pro` | [OVHcloud infrastructure connector](ovhcloud-infrastructure-pro/README.md) | Platform, Operations, Tenancy | `automation.terraform-opentofu`, `compute.node-groups` |
| `plugin.scaleway-infrastructure-pro` | [Scaleway infrastructure connector](scaleway-infrastructure-pro/README.md) | Platform, Operations, Tenancy | `automation.terraform-opentofu`, `compute.node-groups` |
| `plugin.vultr-infrastructure-pro` | [Vultr infrastructure connector](vultr-infrastructure-pro/README.md) | Platform, Operations, Tenancy | `automation.terraform-opentofu`, `compute.node-groups` |

<a id="dns-cdn-domains"></a>

## DNS, CDN and domains

DNS providers, DNS-01, registrars and admitted edge services.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.cloudflare-dns-pro` | [Cloudflare DNS and DNS-01 connector](cloudflare-dns-pro/README.md) | Hosting, IdentityAccess, Operations | `domains.cloudflare`, `domains.dns01` |
| `plugin.aws-route53-pro` | [Amazon Route 53 DNS connector](aws-route53-pro/README.md) | Hosting, IdentityAccess, Operations | `domains.route53`, `domains.dns01` |
| `plugin.ovh-dns-pro` | [OVHcloud DNS connector](ovh-dns-pro/README.md) | Hosting, Operations | `domains.dns-providers`, `domains.dns01` |
| `plugin.hetzner-dns-pro` | [Hetzner DNS connector](hetzner-dns-pro/README.md) | Hosting, Operations | `domains.dns-providers`, `domains.dns01` |
| `plugin.powerdns-remote-pro` | [Remote PowerDNS API connector](powerdns-remote-pro/README.md) | Hosting, Platform, Operations | `domains.dns-providers` |
| `plugin.cloudflare-cdn-pro` | [Cloudflare CDN and edge connector](cloudflare-cdn-pro/README.md) | Hosting, Observability, Operations | `domains.cloudflare`, `domains.cdn-edge` |
| `plugin.fastly-edge-pro` | [Fastly edge connector](fastly-edge-pro/README.md) | Hosting, Observability, Operations | `domains.cdn-edge` |
| `plugin.registrar-portfolio-pro` | [Registrar portfolio connector pack](registrar-portfolio-pro/README.md) | Hosting, Transfers, Operations | `domains.registrars`, `domains.domain-monitoring` |

<a id="backup-object-storage"></a>

## Backup and object storage

Optional remote backup destinations and application media stores.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.aws-s3-backup-pro` | [Amazon S3 backup destination](aws-s3-backup-pro/README.md) | BackupRestore, Operations, IdentityAccess | `backup.s3`, `backup.immutable` |
| `plugin.backblaze-b2-backup-pro` | [Backblaze B2 backup destination](backblaze-b2-backup-pro/README.md) | BackupRestore, Operations, IdentityAccess | `backup.s3`, `backup.immutable` |
| `plugin.wasabi-backup-pro` | [Wasabi backup destination](wasabi-backup-pro/README.md) | BackupRestore, Operations, IdentityAccess | `backup.s3`, `backup.immutable` |
| `plugin.azure-blob-backup-pro` | [Azure Blob backup destination](azure-blob-backup-pro/README.md) | BackupRestore, Operations, IdentityAccess | `backup.transfers`, `backup.immutable`, `storage.object-storage` |
| `plugin.google-cloud-storage-backup-pro` | [Google Cloud Storage backup destination](google-cloud-storage-backup-pro/README.md) | BackupRestore, Operations, IdentityAccess | `backup.transfers`, `backup.immutable`, `storage.object-storage` |
| `plugin.remote-sftp-backup-pro` | [Remote SFTP backup destination](remote-sftp-backup-pro/README.md) | BackupRestore, Files, Operations | `backup.sftp` |
| `plugin.s3-application-media-pro` | [Application object-storage connector](s3-application-media-pro/README.md) | Hosting, Files, Data, Transfers | `storage.object-storage` |

<a id="observability"></a>

## Observability and SIEM

Scoped metrics, traces, logs, incidents and security exports.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.prometheus-federation-pro` | [Prometheus telemetry connector](prometheus-federation-pro/README.md) | Observability, Platform, IdentityAccess | `observe.prometheus` |
| `plugin.opentelemetry-export-pro` | [OpenTelemetry export connector](opentelemetry-export-pro/README.md) | Observability, Operations, IdentityAccess | `observe.opentelemetry` |
| `plugin.grafana-cloud-pro` | [Grafana Cloud observability connector](grafana-cloud-pro/README.md) | Observability, IdentityAccess, Operations | `observe.grafana`, `observe.opentelemetry` |
| `plugin.loki-log-shipping-pro` | [Loki log-shipping connector](loki-log-shipping-pro/README.md) | Observability, IdentityAccess, Operations | `observe.loki` |
| `plugin.datadog-telemetry-pro` | [Datadog telemetry connector](datadog-telemetry-pro/README.md) | Observability, IdentityAccess, Operations | `observe.opentelemetry`, `observe.synthetics` |
| `plugin.splunk-siem-pro` | [Splunk SIEM export connector](splunk-siem-pro/README.md) | Observability, IdentityAccess, Operations | `security.siem`, `observe.syslog` |

<a id="identity-secrets"></a>

## Identity and secrets

Federation, external directories and secret/key references.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.keycloak-sso-pro` | [Keycloak SSO connector](keycloak-sso-pro/README.md) | IdentityAccess, Tenancy, Operations | `identity.oidc`, `identity.saml` |
| `plugin.microsoft-entra-sso-pro` | [Microsoft Entra identity connector](microsoft-entra-sso-pro/README.md) | IdentityAccess, Tenancy, Operations | `identity.oidc`, `identity.scim` |
| `plugin.okta-identity-pro` | [Okta identity connector](okta-identity-pro/README.md) | IdentityAccess, Tenancy, Operations | `identity.saml`, `identity.scim` |
| `plugin.ldap-directory-pro` | [LDAP directory connector](ldap-directory-pro/README.md) | IdentityAccess, Tenancy, Operations | `identity.ldap` |
| `plugin.hashicorp-vault-secrets-pro` | [HashiCorp Vault secrets connector](hashicorp-vault-secrets-pro/README.md) | IdentityAccess, Operations, ExtensionManagement | `identity.vaults-kms`, `delivery.deployment-secrets` |
| `plugin.cloud-kms-key-refs-pro` | [Cloud KMS key-reference connector](cloud-kms-key-refs-pro/README.md) | IdentityAccess, BackupRestore, Operations | `identity.vaults-kms` |

<a id="billing-business"></a>

## Billing and business systems

Payment events, optional billing systems and CRM mappings.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.stripe-billing-pro` | [Stripe billing connector](stripe-billing-pro/README.md) | Tenancy, Operations, IdentityAccess | `commerce.business-systems` |
| `plugin.paypal-commerce-pro` | [PayPal commerce connector](paypal-commerce-pro/README.md) | Tenancy, Operations, IdentityAccess | `commerce.business-systems` |
| `plugin.whmcs-provisioning-pro` | [WHMCS provisioning connector](whmcs-provisioning-pro/README.md) | Tenancy, Setup, Operations | `commerce.whmcs` |
| `plugin.blesta-provisioning-pro` | [Blesta provisioning connector](blesta-provisioning-pro/README.md) | Tenancy, Setup, Operations | `commerce.blesta` |
| `plugin.fossbilling-sync-pro` | [FOSSBilling service synchronization](fossbilling-sync-pro/README.md) | Tenancy, Operations, Setup | `commerce.fossbilling` |
| `plugin.business-crm-sync-pro` | [Business CRM synchronization connector](business-crm-sync-pro/README.md) | Tenancy, Operations, IdentityAccess | `commerce.business-systems` |

<a id="collaboration-support"></a>

## Collaboration and support

Redacted notifications, support records and reviewed ChatOps.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.slack-notifications-pro` | [Slack notifications and reviewed ChatOps](slack-notifications-pro/README.md) | Observability, Operations, IdentityAccess | `notify.team-messaging`, `automation.chatops` |
| `plugin.microsoft-teams-pro` | [Microsoft Teams notification connector](microsoft-teams-pro/README.md) | Observability, Operations, IdentityAccess | `notify.team-messaging` |
| `plugin.discord-notifications-pro` | [Discord notification connector](discord-notifications-pro/README.md) | Observability, Operations, IdentityAccess | `notify.team-messaging` |
| `plugin.pagerduty-oncall-pro` | [PagerDuty incident and on-call connector](pagerduty-oncall-pro/README.md) | Observability, Operations, IdentityAccess | `notify.oncall` |
| `plugin.atlassian-service-desk-pro` | [Atlassian service desk connector](atlassian-service-desk-pro/README.md) | Observability, Operations, Tenancy | `support.helpdesk`, `notify.oncall` |
| `plugin.statuspage-sync-pro` | [Status-page incident publishing connector](statuspage-sync-pro/README.md) | Observability, Operations, IdentityAccess | `support.status-page` |

<a id="git-ci-registries"></a>

## Git, CI and artifact registries

Source-provider events and immutable artifact references.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.github-delivery-pro` | [GitHub source and delivery connector](github-delivery-pro/README.md) | Hosting, Operations, IdentityAccess | `delivery.providers`, `delivery.git`, `delivery.pipelines` |
| `plugin.gitlab-delivery-pro` | [GitLab source and delivery connector](gitlab-delivery-pro/README.md) | Hosting, Operations, IdentityAccess | `delivery.providers`, `delivery.git`, `delivery.pipelines` |
| `plugin.bitbucket-delivery-pro` | [Bitbucket source and delivery connector](bitbucket-delivery-pro/README.md) | Hosting, Operations, IdentityAccess | `delivery.providers`, `delivery.git`, `delivery.pipelines` |
| `plugin.harbor-registry-pro` | [Harbor OCI registry connector](harbor-registry-pro/README.md) | Platform, Operations, IdentityAccess | `delivery.registries`, `delivery.dependency-analysis` |
| `plugin.ghcr-registry-pro` | [GitHub Container Registry connector](ghcr-registry-pro/README.md) | Platform, Operations, IdentityAccess | `delivery.registries` |
| `plugin.dockerhub-registry-pro` | [Docker Hub registry connector](dockerhub-registry-pro/README.md) | Platform, Operations, IdentityAccess | `delivery.registries` |
| `plugin.artifact-repository-pro` | [Enterprise artifact repository connector](artifact-repository-pro/README.md) | Platform, Operations, ExtensionManagement | `delivery.registries` |

<a id="ai-mcp"></a>

## AI and MCP

Optional redacted assistance, selected models and admitted MCP HTTP.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.openai-provider-pro` | [OpenAI assistance provider connector](openai-provider-pro/README.md) | Platform, ExtensionManagement, Operations | `automation.ai-readonly`, `automation.ai-missions` |
| `plugin.anthropic-provider-pro` | [Anthropic assistance provider connector](anthropic-provider-pro/README.md) | Platform, ExtensionManagement, Operations | `automation.ai-readonly`, `automation.ai-missions` |
| `plugin.local-inference-provider-pro` | [Local inference provider connector](local-inference-provider-pro/README.md) | Platform, ExtensionManagement, Operations | `automation.ai-readonly`, `automation.ai-missions` |
| `plugin.mcp-http-connector-pro` | [MCP HTTP provider connector](mcp-http-connector-pro/README.md) | Platform, ExtensionManagement, Operations | `automation.ai-readonly`, `automation.chatops` |

<a id="migration-data"></a>

## Migration and data transfer

Source-specific portfolio, mailbox and PostgreSQL migration profiles.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.cpanel-migration-pro` | [cPanel portfolio migration connector](cpanel-migration-pro/README.md) | Transfers, Hosting, Mail, Data | `apps.fleet-management`, `mail.mailbox-migration`, `backup.transfers` |
| `plugin.plesk-migration-pro` | [Plesk portfolio migration connector](plesk-migration-pro/README.md) | Transfers, Hosting, Mail, Data | `apps.fleet-management`, `mail.mailbox-migration`, `backup.transfers` |
| `plugin.imap-mailbox-migration-pro` | [IMAP mailbox migration connector](imap-mailbox-migration-pro/README.md) | Transfers, Mail, IdentityAccess | `mail.mailbox-migration` |
| `plugin.external-postgres-migration-pro` | [External PostgreSQL migration connector](external-postgres-migration-pro/README.md) | Transfers, Data, BackupRestore | `data.external-databases`, `data.replication`, `backup.transfers` |

## Count and mapping rules

The 60 IDs are unique local proposal IDs. Their catalogue-key references reuse exact entries from the [149-entry catalogue](../../PLUGIN_CATALOG.md); they do not reclassify the 233 essential Core capabilities as paid features. One connector can implement several keys, and several proposed connectors can address one provider family.

The [_template](_template/README.md) is excluded. The separate [36 module proposals](../../WCP360_Modules/CATALOG.md) are not duplicated here. No entry is a released package, Core-discovered manifest, accepted provider contract or legal licensing decision.
