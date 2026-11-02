# WCP360 build and qualification

**Status: the executable Core candidate has a development Docker/Compose environment and CI verification workflow; stable-release qualification and publication remain unimplemented.**

See [the candidate guide](../documentation/development/EXECUTABLE-CANDIDATE.md), [its environment](environments/core-candidate/README.md) and [the source audit](verification/CORE-CANDIDATE-AUDIT.md). The pipeline tests the candidate; it does not promote CODE-BASELINE or publish a stable release.

Build from reviewed Core, SDK and plugin revisions with their approved toolchains/locks. Assemble each component independently; run relevant checks before a candidate becomes a release.

| Directory | Responsibility |
| :--- | :--- |
| [environments](environments/README.md) | Pinned supported build/test inputs and isolation requirements. |
| [packaging](packaging/README.md) | Package assembly, file modes/content inventory, dependency/asset inclusion and exclusions. |
| [verification](verification/README.md) | CI/test matrix, candidate identity and retained result/evidence requirements. |
| [publication](publication/README.md) | Immutable tags/assets/records and qualified channel promotion. |
| build/ | Generated disposable output; ignored by the existing build/ rule and not created as source. |

Production artifacts include only approved runtime dependencies and prepared assets. Never publish build caches, private signing material, customer data, unqualified inert binaries as production agents or vendor-side Internal services.

Do not update dependencies in the release lane. Review lock changes as development work, then build using those exact locks. A correct package builder must reject missing source, unqualified toolchains and incomplete mandatory evidence rather than manufacturing an archive from this documentation scaffold.

See [installation and release acceptance](../documentation/development/DELIVERY-AND-RELEASES.md), [qualified release records](../WCP360_Releases/README.md) and the [current manifest](../documentation/manifests/WCP360-BM-0001.md).
