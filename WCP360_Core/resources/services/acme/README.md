# ACME and certificate-deployment resources

**Status: documentation reservation. No production configuration or new runtime capability is implemented here.**

Owner: [Hosting](../../../src/Module/Hosting/README.md), coordinating Mail, Platform and Operations.

No client or certificate automation is selected. Future profiles name the admitted client/provider, account/key references, certificate/domain ownership, challenge capabilities, renewal schedule and consumer deploy/reload order. Caddy may own automation in a qualified profile; never create competing certificate/listener owners.

Challenge files and DNS credentials need bounded ownership and cleanup. Preserve a usable certificate until replacement and consumer health are observed; interrupted publication requires reconciliation. Eventual tests use authorized disposable domains and the selected staging service.

Private keys, DNS tokens, account material and real certificate bundles stay outside Git. This reservation selects neither certbot nor lego, grants no DNS authority and invents no privileged operation.

Policy/validation stay in Hosting; exact effects use accepted agent contracts. Cross-service deployment must cover Nginx/Apache/Caddy and mail only where claimed. See the [web-server roadmap](../../../src/Module/Hosting/WEB-SERVER-ROADMAP.md) and [service lifecycle](../README.md#configuration-change-lifecycle).

See the [resource index](../../README.md), [Core structure](../../../STRUCTURE.md) and existing owning use cases before adding real source. This README does not select a package, create a machine-path allowlist or authorize privileged operations.
