# Shared HTTP presentation and transport primitives

**Status: shared source location reserved; no HTTP framework bootstrap, endpoint or public schema is supplied here.**

Place genuinely shared HTTP input/output plumbing here: accepted pagination/filter envelopes, stable error presentation, correlation propagation and bounded request/response helpers. Domain controllers and form mappings remain in `src/Module/<Owner>/UI`; the application use case and its authorization remain with that owner.

The folder is not a second API registry, a universal controller or an alternative plugin SDK. Public endpoint/identifier/error definitions come from the accepted contracts. SDK clients remain in WCP360_SDK; deriving a shared Core helper does not authorize a new route or capability.

Inbound/outbound webhook delivery and replay/retry orchestration belong to Operations, token/identity scope to IdentityAccess, and resource-specific payload rules to the owning module. Reuse the shared security boundary rather than placing independent access rules in each response helper.

- [Shared foundations](../README.md)
- [Shared security primitives](../Security/README.md)
- [Accepted API specifications](../../../../documentation/specifications/api/)
- [Public SDK](../../../../WCP360_SDK/README.md)
- [Capability ownership](../../../CAPABILITY-MAP.md)
