# MASTER DEPENDENCY GRAPH --- V3.6

## Graphe de dépendances par famille

Une flèche `A → B` signifie **A dépend de B**. Les dépendances sont des
contrats d'architecture, pas seulement des liens UI.

-   **ACME / TLS Advanced** → DNS, Secure Development Normative
-   **AI, Automation & Observability UX** → API / CLI / SDK, Telemetry,
    Secure Development Normative
-   **API / CLI / SDK** → Product Architecture & Identity, Secure
    Development Normative
-   **App Runtimes / LiteSpeed** → Web / PHP, System / OS
-   **Apps / Multitenancy / API / Mail UX** → WCP-USER, Web / PHP, Mail,
    API / CLI / SDK
-   **Backup / IPAM / Metering** → Backup / Shared Storage, Linux
    Network / DNS Advanced
-   **Backup / Shared Storage** → System / OS, Secure Development
    Normative
-   **Billing / File Manager** → API / CLI / SDK, WCP-USER
-   **Cache / Queue** → System / OS, WCP Control Plane
-   **Compute / SDN** → Storage / Cluster, Linux Network / DNS Advanced,
    Telemetry
-   **DNS** → System / OS, Linux Network / DNS Advanced
-   **Databases** → System / OS, Backup / Shared Storage, Secure
    Development Normative
-   **Datacenter / Kubernetes / IaC** → Compute / SDN, API / CLI / SDK
-   **Design Components & UX Patterns** → Design System Foundations
-   **Design System Foundations** → Product Architecture & Identity
-   **Domains / Migration** → DNS, Web / PHP, Databases, Mail, Backup /
    Shared Storage
-   **Enterprise Identity / Support** → Product Architecture & Identity,
    API / CLI / SDK
-   **Governance, API & Quality** → Product Architecture & Identity,
    Secure Development Normative
-   **Host Security / Storage Health** → System / OS, Secure Development
    Normative, Telemetry
-   **Licensing** → API / CLI / SDK, WCP Control Plane
-   **Linux Network / DNS Advanced** → System / OS, Secure Development
    Normative
-   **Mail** → System / OS, DNS, ACME / TLS Advanced, Host Security /
    Storage Health
-   **Mail / DB Advanced** → Mail, Databases
-   **Mail Deliverability** → Mail, DNS, ACME / TLS Advanced
-   **Monitoring / API Tokens** → Telemetry, API / CLI / SDK
-   **Network / SSH / FTP** → System / OS, Secure Development Normative
-   **Plugin Trust / Audit SIEM** → Plugins / Security, Telemetry,
    Secure Development Normative
-   **Plugins / Security** → Secure Development Normative, WCP Control
    Plane
-   **Product Architecture & Identity** → Secure Development Normative
-   **SSH / MFA / SCIM** → Network / SSH / FTP, Product Architecture &
    Identity
-   **Secure Development Normative** → racine
-   **Storage / Cluster** → System / OS, Linux Network / DNS Advanced,
    Telemetry
-   **System / OS** → Secure Development Normative, WCP Control Plane
-   **TLS / Backup** → System / OS, Secure Development Normative
-   **Telemetry** → WCP Control Plane, System / OS
-   **WCP Control Plane** → Secure Development Normative, Product
    Architecture & Identity
-   **WCP-ADMIN Shell** → Product Architecture & Identity, Design
    Components & UX Patterns, API / CLI / SDK
-   **WCP-USER** → Product Architecture & Identity, Design Components &
    UX Patterns, API / CLI / SDK
-   **Web / PHP** → System / OS, Secure Development Normative, TLS /
    Backup

## Ordre de construction recommandé

1.  Secure Development + Product Architecture + Control Plane.
2.  System/OS + Network + API + Telemetry.
3.  Web/PHP + DB + DNS + TLS + Backup.
4.  Mail + Security + Apps + Migration + Billing.
5.  Cluster/Compute et extensions P2 après stabilisation du cœur.
