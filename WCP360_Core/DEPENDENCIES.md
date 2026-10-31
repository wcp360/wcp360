# Dependency locations and ownership

The Core, SDK and detached plugins have separate dependency graphs. This guide identifies the future real files; **no dependency manifest or lock is fabricated by this layout change.**

| Component | Definition and lock | Generated/downloaded contents | Update boundary |
| :--- | :--- | :--- | :--- |
| Core PHP/Symfony | WCP360_Core/composer.json and composer.lock, when the approved bootstrap is implemented | WCP360_Core/vendor/ | Review dependency and lock changes together; build/install the qualified locked graph |
| Detached PHP plugin | WCP360_Plugins/<plugin>/composer.json and application lock | Its own vendor/ | Plugin package only; never edit Core dependencies during plugin installation |
| Public PHP SDK | WCP360_SDK/php library definition and compatibility policy | Consumer-owned Composer resolution | Independently versioned library; host applications retain their own locks |
| Go agent/broker | Separate agents/<component>/go.mod and go.sum | Go module cache; vendoring only if explicitly selected | Approved component toolchain and protocol tests; no common PHP vendor folder |
| Python diagnostic tool | Tool-specific package definition and the accepted reproducibility inputs | Isolated environment outside versioned source | Specialized non-root tool; dependency choice is not authority to run arbitrary Python on the host |
| Symfony frontend | Core Composer graph, AssetMapper/importmap and approved CSS compiler inputs | Prepared production assets | Use the selected Symfony pipeline; do not introduce an npm runtime dependency merely to organize folders |
| Optional Node build/test package | Its own package.json and selected lock, where actually needed | Package-local node_modules/ | Build/test component only; pin and review its toolchain |
| Linux services and libraries | Accepted package/version/source records under WCP360_Build/environments and the installer contract | Installed OS packages and package-managed files | Selected Debian target and service adapter; not Composer vendor or npm dependencies |

Composer's application lock and Go's module metadata have different semantics. Do not manufacture a single dependency file spanning PHP, Go, Python and Debian. A service-resource folder is not a list of packages to install automatically.

## Updating a dependency

Identify the owning component, contract and supported environment. Review the manifest/lock or OS input change there, execute its relevant build/tests and affected integration checks, then assemble the approved result through [WCP360_Build](../WCP360_Build/README.md). The production release lane must not resolve an unreviewed graph or run an arbitrary update on the target host.

Generated vendor/, node_modules/, caches, environments and build output do not belong in source control. Qualified production vendor/assets can be included by the package builder; that does not make generated development directories repository source. Keep credentials and private registry tokens outside manifests, locks, fixtures and logs.

- [Repository development/dependency map](../documentation/development/README.md)
- [Core navigation](NAVIGATION.md)
- [Installer and release qualification](../documentation/development/DELIVERY-AND-RELEASES.md)
- [Composer dependency workflow](https://getcomposer.org/doc/01-basic-usage.md)
- [Go module reference](https://go.dev/ref/mod)
