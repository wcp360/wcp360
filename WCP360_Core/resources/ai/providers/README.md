# AI provider resource references

**Status: documentation reservation. No additional runtime or production capability is implemented here.**

Owners: [Platform](../../../src/Module/Platform/README.md) and [ExtensionManagement](../../../src/Module/ExtensionManagement/README.md), with Operations and IdentityAccess for execution/authorization.

No cloud connection or local inference engine is configured here. Future descriptors name the admitted package/provider revision, endpoint or local service reference, protocol/capabilities, model references, deadlines/concurrency/cost budgets and health behavior. Declare permitted outbound destinations, data categories, retention/residency expectations and credential references before sending data.

A provider token is not a Core permission or approval to disclose tenant content. Keys stay outside resources, downloads and logs; scope them to the integration. Validate endpoints, redirects and discovered URLs against private/control-plane access and egress policy.

Local inference is a provider workload with its own identity, capacity, storage and optional GPU requirements; it does not run in the privileged broker. Cloud/vendor availability is not a baseline hosting dependency.

Do not claim universal API compatibility or silently change provider when unavailable. See [AI boundaries](../README.md), [models](../models/README.md) and [extension contributions](../../extensions/README.md).

See the [resource index](../../README.md) and [Core structure](../../../STRUCTURE.md). Add concrete resource files only with their reviewed contract and consuming implementation; no fake configuration or empty template tree is needed.
