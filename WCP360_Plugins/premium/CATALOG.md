# Premium connector catalogue

**97 proposals · 19 categories · design only · not installable**

[CATALOG.json](CATALOG.json) is authoritative. Each entry below links to its three-file proposal record; JSON mirrors are synchronized with `packages`. Read [the catalogue contract](README.md), [editions](../../documentation/development/EDITIONS-AND-PREMIUM.md) and [Core licensing](../../WCP360_Core/LICENSING.md) before using this inventory.

Every entry has `kind: plugin`, `tier: pro`, `status: design`, `installable: false`, `source_model: undecided` and no chosen package dependencies. Provider-specific behavior, maintenance, ownership and compatibility remain acceptance work. None of the seven `engine.*` catalogue keys is assigned here.

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

DNS providers, DNS-01, registrars and explicitly admitted CDN/performance policies.

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
| `plugin.bunny-cdn-pro` | [Bunny CDN edge connector](bunny-cdn-pro/README.md) | Hosting, Observability, Operations | `domains.cdn-edge` |

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

Redacted collaboration, Telegram/SMS/transactional email and incident destinations.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.slack-notifications-pro` | [Slack notifications and reviewed ChatOps](slack-notifications-pro/README.md) | Observability, Operations, IdentityAccess | `notify.team-messaging`, `automation.chatops` |
| `plugin.microsoft-teams-pro` | [Microsoft Teams notification connector](microsoft-teams-pro/README.md) | Observability, Operations, IdentityAccess | `notify.team-messaging` |
| `plugin.discord-notifications-pro` | [Discord notification connector](discord-notifications-pro/README.md) | Observability, Operations, IdentityAccess | `notify.team-messaging` |
| `plugin.pagerduty-oncall-pro` | [PagerDuty incident and on-call connector](pagerduty-oncall-pro/README.md) | Observability, Operations, IdentityAccess | `notify.oncall` |
| `plugin.atlassian-service-desk-pro` | [Atlassian service desk connector](atlassian-service-desk-pro/README.md) | Observability, Operations, Tenancy | `support.helpdesk`, `notify.oncall` |
| `plugin.statuspage-sync-pro` | [Status-page incident publishing connector](statuspage-sync-pro/README.md) | Observability, Operations, IdentityAccess | `support.status-page` |
| `plugin.telegram-notifications-pro` | [Telegram notification connector](telegram-notifications-pro/README.md) | Observability, Operations, IdentityAccess | `notify.team-messaging` |
| `plugin.twilio-sms-pro` | [Twilio SMS notification connector](twilio-sms-pro/README.md) | Observability, Operations, IdentityAccess | `notify.sms` |
| `plugin.brevo-email-pro` | [Brevo transactional email connector](brevo-email-pro/README.md) | Mail, Observability, Operations, IdentityAccess | `mail.smtp-relays` |

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

Source-specific portfolio/mail/database migration and read-scoped Enhance interoperability.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.cpanel-migration-pro` | [cPanel portfolio migration connector](cpanel-migration-pro/README.md) | Transfers, Hosting, Mail, Data | `apps.fleet-management`, `mail.mailbox-migration`, `backup.transfers` |
| `plugin.plesk-migration-pro` | [Plesk portfolio migration connector](plesk-migration-pro/README.md) | Transfers, Hosting, Mail, Data | `apps.fleet-management`, `mail.mailbox-migration`, `backup.transfers` |
| `plugin.imap-mailbox-migration-pro` | [IMAP mailbox migration connector](imap-mailbox-migration-pro/README.md) | Transfers, Mail, IdentityAccess | `mail.mailbox-migration` |
| `plugin.external-postgres-migration-pro` | [External PostgreSQL migration connector](external-postgres-migration-pro/README.md) | Transfers, Data, BackupRestore | `data.external-databases`, `data.replication`, `backup.transfers` |
| `plugin.enhance-interop-pro` | [Enhance inventory and migration interoperability](enhance-interop-pro/README.md) | Transfers, Hosting, Tenancy, Operations | `apps.fleet-management`, `backup.transfers` |

<a id="compute-platforms"></a>

## Docker, Kubernetes and virtualization

Core-mediated Docker/virtualization profiles and separately owned Portainer environments.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.docker-management-adapter-pro` | [Docker managed operations adapter](docker-management-adapter-pro/README.md) | Platform, Operations, Tenancy | `compute.containers`, `compute.images`, `compute.rolling-operations` |
| `plugin.kubernetes-api-pro` | [Kubernetes API connector](kubernetes-api-pro/README.md) | Platform, Operations, Tenancy | `compute.kubernetes`, `compute.node-groups`, `compute.rolling-operations` |
| `plugin.proxmox-ve-pro` | [Proxmox VE virtualization connector](proxmox-ve-pro/README.md) | Platform, Operations, Tenancy | `compute.proxmox`, `compute.node-groups`, `compute.images` |
| `plugin.libvirt-kvm-pro` | [libvirt and KVM managed adapter](libvirt-kvm-pro/README.md) | Platform, Operations, Tenancy | `compute.libvirt`, `compute.images`, `compute.node-groups` |
| `plugin.incus-pro` | [Incus project and instance connector](incus-pro/README.md) | Platform, Operations, Tenancy | `compute.lxc`, `compute.containers`, `compute.images` |
| `plugin.openstack-pro` | [OpenStack project infrastructure connector](openstack-pro/README.md) | Platform, Operations, Tenancy | `automation.terraform-opentofu`, `compute.node-groups`, `compute.images` |
| `plugin.portainer-management-pro` | [Portainer environment interoperability](portainer-management-pro/README.md) | Platform, Operations, Tenancy | `compute.containers`, `compute.node-groups` |

<a id="storage-platforms"></a>

## Storage platforms

Distinct RGW objects, CephFS files, RBD blocks, GlusterFS and dataset/share provider contracts.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.ceph-rgw-pro` | [Ceph RGW object-storage connector](ceph-rgw-pro/README.md) | BackupRestore, Files, Platform, Operations | `storage.ceph`, `storage.object-storage`, `backup.s3` |
| `plugin.minio-storage-pro` | [MinIO object-storage connector](minio-storage-pro/README.md) | BackupRestore, Files, Platform, Operations | `storage.object-storage`, `backup.s3`, `backup.immutable` |
| `plugin.truenas-zfs-pro` | [TrueNAS and ZFS storage connector](truenas-zfs-pro/README.md) | Platform, Files, BackupRestore, Operations | `storage.zfs`, `storage.nfs`, `storage.smb` |
| `plugin.glusterfs-storage-pro` | [GlusterFS storage interoperability](glusterfs-storage-pro/README.md) | Platform, Files, BackupRestore, Operations | `storage.glusterfs` |
| `plugin.cephfs-storage-pro` | [CephFS filesystem connector](cephfs-storage-pro/README.md) | Platform, Files, BackupRestore, Operations | `storage.ceph` |
| `plugin.ceph-rbd-storage-pro` | [Ceph RBD block-storage connector](ceph-rbd-storage-pro/README.md) | Platform, BackupRestore, Operations, Tenancy | `storage.ceph`, `compute.images` |

<a id="security-defense"></a>

## Cybersecurity and DDoS defense

Scoped provider evidence, defensive decisions and provenance-qualified single-writer interoperability.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.crowdsec-defense-pro` | [CrowdSec defensive workflow connector](crowdsec-defense-pro/README.md) | Platform, Observability, Operations, Hosting | `security.crowdsec`, `security.threat-intel` |
| `plugin.modsecurity-crs-pro` | [ModSecurity CRS managed-policy connector](modsecurity-crs-pro/README.md) | Hosting, Platform, Observability, Operations | `security.waf`, `security.advisories` |
| `plugin.coraza-waf-pro` | [Coraza managed-policy connector](coraza-waf-pro/README.md) | Hosting, Platform, Observability, Operations | `security.waf`, `security.advisories` |
| `plugin.upstream-ddos-scrubbing-pro` | [Upstream DDoS scrubbing provider connector](upstream-ddos-scrubbing-pro/README.md) | Platform, Hosting, Observability, Operations | `network.sdn`, `domains.global-routing`, `security.threat-intel` |
| `plugin.bitninja-defense-pro` | [BitNinja defensive interoperability](bitninja-defense-pro/README.md) | Platform, Observability, Operations | `security.threat-intel`, `security.siem` |
| `plugin.imunify360-defense-pro` | [Imunify360 defensive interoperability](imunify360-defense-pro/README.md) | Platform, Hosting, Observability, Operations | `security.imunify360` |
| `plugin.csf-lfd-policy-pro` | [CSF and lfd policy evidence connector](csf-lfd-policy-pro/README.md) | Platform, Observability, Operations | `security.threat-intel`, `security.siem`, `automation.event-rules` |

<a id="multi-server-automation"></a>

## Multi-server automation

Admitted controller inventories and bounded reviewed multi-server job templates.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.awx-controller-pro` | [AWX multi-server workflow connector](awx-controller-pro/README.md) | Platform, Operations, Tenancy | `automation.ansible`, `compute.node-groups`, `compute.rolling-operations` |

<a id="performance-cache"></a>

## Performance and HTTP caching

Qualified external-cache profiles; native compression and default Apache routing stay Core.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.varnish-cache-pro` | [Varnish-compatible managed cache connector](varnish-cache-pro/README.md) | Hosting, Platform, Observability, Operations | `domains.cdn-edge` |

<a id="application-platforms"></a>

## Application platform interoperability

Coolify/Dokku external application profiles and authenticated Nextcloud/WebDAV clients.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.coolify-applications-pro` | [Coolify application interoperability](coolify-applications-pro/README.md) | Hosting, Platform, Operations, Tenancy | `apps.fleet-management`, `delivery.providers`, `compute.containers` |
| `plugin.dokku-applications-pro` | [Dokku application interoperability](dokku-applications-pro/README.md) | Hosting, Platform, Operations | `apps.fleet-management`, `delivery.git`, `runtime.workers` |
| `plugin.nextcloud-webdav-pro` | [Nextcloud application WebDAV connector](nextcloud-webdav-pro/README.md) | Files, Transfers, IdentityAccess, Operations | `apps.business-recipes`, `backup.transfers` |

<a id="proxy-platforms"></a>

## Proxy platform interoperability

Traefik observations/provider plans and NPM export/import without ingress takeover.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.traefik-routing-pro` | [Traefik routing interoperability](traefik-routing-pro/README.md) | Hosting, Platform, Operations | `network.traefik` |
| `plugin.nginx-proxy-manager-pro` | [Nginx Proxy Manager interoperability](nginx-proxy-manager-pro/README.md) | Hosting, Platform, Operations | `domains.cdn-edge` |

<a id="database-services"></a>

## External and tenant data services

Tenant-owned external MySQL/MariaDB, Galera, Redis and MongoDB profiles.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.external-mysql-mariadb-pro` | [External MySQL and MariaDB connector](external-mysql-mariadb-pro/README.md) | Data, BackupRestore, Operations, Tenancy | `data.external-databases`, `data.sql-analysis` |
| `plugin.galera-cluster-pro` | [Galera cluster observation connector](galera-cluster-pro/README.md) | Data, BackupRestore, Operations, Observability | `data.replication`, `data.external-databases` |
| `plugin.redis-service-pro` | [Tenant Redis service connector](redis-service-pro/README.md) | Data, Platform, Operations, Tenancy | `runtime.redis`, `data.external-databases` |
| `plugin.mongodb-service-pro` | [External MongoDB service connector](mongodb-service-pro/README.md) | Data, BackupRestore, Operations, Tenancy | `data.external-databases` |

<a id="certificate-providers"></a>

## Additional certificate authorities

Additional ACME account/authority policy; baseline TLS, renewal and wildcard stay Core.

| Proposal ID | Connector | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| `plugin.external-acme-authority-pro` | [Additional ACME authority connector](external-acme-authority-pro/README.md) | Hosting, IdentityAccess, Operations | `domains.acme-authorities`, `domains.dns01` |

## Count and mapping rules

The 97 IDs are unique local proposal IDs. Their catalogue-key references reuse exact entries from the [149-entry catalogue](../../PLUGIN_CATALOG.md); they do not reclassify the 233 essential Core capabilities as paid features. One connector can address several keys, and several distinct upstream profiles can address one provider family.

The [_template](_template/README.md) is excluded. The separate [44 module proposals](../../WCP360_Modules/CATALOG.md) are not duplicated here. No entry is a released package, Core-discovered manifest, accepted provider contract or legal licensing decision.
