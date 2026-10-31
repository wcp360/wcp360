# Chart and metric presentation components

**Status: first-party presentation source location reserved; no chart library or executable component is installed here.**

Own reusable usage/history/availability visualizations described in the README UI library. Consume authorized view data from the owning domain; Observability supplies shared measurement provenance/freshness and collection behavior. Resource quotas and entitlement decisions remain in their respective domains.

Show units, sampling/time interval, source/freshness and missing/stale/error states. Provide an accessible text/table alternative and predictable keyboard/focus behavior where interaction exists. Do not infer current usage or a healthy service from placeholder data or a chart animation.

First-party DOM behavior belongs in assets/controllers and styles in assets/styles. Choose and qualify an actual rendering dependency with the approved frontend build when implementation starts; a folder must not imply a new Node runtime, remote script source or injected plugin JavaScript.

- [Shared component map](../README.md)
- [Observability owner](../../../src/Module/Observability/README.md)
- [Frontend sources](../../../assets/README.md)
- [Browser/accessibility tests](../../../tests/Browser/README.md)
- [Capability ownership](../../../CAPABILITY-MAP.md)
