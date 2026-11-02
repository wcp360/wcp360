# SDK test support

Reserve synthetic fixtures and a test host for isolated plugin development. Include wrong tenant/surface, unknown identifiers, expired authorization, timeouts, incompatible versions and redaction. Real Core integration, process isolation and privileged boundaries require separate executed evidence.

The [Registry candidate client](../php/registry-client/README.md) now supplies synthetic consumer tests. The vendor Grav plugin supplies provider/consumer, native Flex and local HTTP tests. These cover its read-only website boundary only; they are not tenant/surface, daemon, privileged, Core or production integration evidence.

See the [SDK status](../README.md) and [development strategy](../../documentation/development/README.md).
