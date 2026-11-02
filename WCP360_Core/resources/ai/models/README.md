# AI model metadata resources

**Status: documentation reservation. No additional runtime or production capability is implemented here.**

Owners: [Platform](../../../src/Module/Platform/README.md) and [ExtensionManagement](../../../src/Module/ExtensionManagement/README.md), with Tenancy for budgets and Operations for lifecycle.

No model is downloaded, loaded or qualified here. Future metadata records provider/model identifier, immutable revision where available, provenance/digest, license/distribution restrictions, runtime/format compatibility, capabilities, context/input/output limits and evidence. Local models need observed memory/VRAM/storage/concurrency admission; a model name is not a capacity guarantee.

Weights, tokenizer/runtime binaries, caches, embeddings and private datasets stay outside Git. Their eventual download/storage/update path requires trusted artifacts, ownership, quota, integrity, retention and removal contracts.

Distinguish a model from an [assistant mission](../agents/README.md). Model output may be incorrect or influenced by untrusted content. It cannot modify permissions, call privileged operations directly or turn a generated statement into measured evidence.

Provider/model updates can change behavior and resource use. Record the exact qualified combination and refuse unsupported capabilities instead of silently falling back.

See [AI boundaries](../README.md) and [providers](../providers/README.md).

See the [resource index](../../README.md) and [Core structure](../../../STRUCTURE.md). Add concrete resource files only with their reviewed contract and consuming implementation; no fake configuration or empty template tree is needed.
