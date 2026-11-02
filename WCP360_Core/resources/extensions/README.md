# Extension resource contributions

**Status: documentation reservation. No additional runtime or production capability is implemented here.**

Owner: [ExtensionManagement](../../src/Module/ExtensionManagement/README.md), collaborating with each contribution's existing resource owner.

Future static contributions may describe reviewed recipes, compatibility/capabilities, UI/resource references and lifecycle inputs. Actual detached packages have separate projects and dependencies. Package admission, signature validity, licensing/entitlements, actor permissions and authorization of an operation are separate decisions.

An extension gains no arbitrary system paths, direct Core-table writes, shell or Docker API access. It cannot load its PHP into the Core Symfony container. Contributions use the same owning use cases and durable operations as first-party interfaces.

Install/enable/disable/update/uninstall require compatibility/dependency checks, explicit capability changes, ownership, revocation, drain and retention. Disabling a recipe must not terminate existing hosting. Uninstall refuses or explicitly transfers active-resource ownership and preserves a Core-readable inventory/recovery path.

Do not duplicate the plugin catalogue, accepted manifests/contracts or service templates here. Publisher keys, package archives, paid credentials and generated state stay outside source.

See [applications](../applications/README.md) and [optional AI integrations](../ai/README.md).

See the [resource index](../README.md) and [Core structure](../../STRUCTURE.md). Add concrete resource files only with their reviewed contract and consuming implementation; no fake configuration or empty template tree is needed.
