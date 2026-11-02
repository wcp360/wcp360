# WCP360 Core licensing

**Effective project decision: November 2, 2026. SPDX identifier: `AGPL-3.0-only`.**

## License grant and scope

WCP360 Core's original first-party source in `WCP360_Core/` is licensed under the **GNU Affero General Public License, version 3 only**. You may use, study, modify and redistribute it under that license. The complete terms are in [LICENSE](LICENSE); the Composer package declares the same identifier in [composer.json](composer.json).

This grant covers the original Core application, its built-in modules, first-party agents, installer source, templates and supporting source in this directory. It does not replace separately identified third-party licenses or notices. Dependencies retain their own licenses, including those recorded in [composer.lock](composer.lock) and each dependency's distribution. Generated runtime data, credentials and customer content are not a grant of rights to disclose those materials.

This is a scoped Core license. It does not silently relicense the SDK, application recipes, existing independent plugins, premium proposals, imported reference material or the entire repository. Those components need their own explicit compatible license and provenance before distribution. No alternate commercial Core license or linking exception is granted by this document.

## Open source and commercial editions

The Core must function with zero optional packages. Its essential hosting, administration, permissions, security, local backup/recovery and extension infrastructure remain open source. An entitlement check cannot be a condition for exercising rights granted by the Core license.

PRO is an optional bundle of services and qualified feature packages described in the [edition strategy](../documentation/development/EDITIONS-AND-PREMIUM.md). Charging for software, distribution or support is compatible with open source; payment does not remove a recipient's AGPL rights. Future commercial conditions must not contradict the license of the code they cover.

Premium package source and licensing models are **undecided**. A separate folder, process, API or SDK does not by itself establish that a component is legally independent of the Core. Assess the actual code reuse, linking and communication, the SDK's license and all dependencies before choosing package terms. A work covered by the Core's copyleft must comply with that license unless appropriate separate permission exists. This planning work grants no proprietary-plugin exception.

## Source and notice obligations

Keep the license, applicable copyright notices, modification notices and warranty disclaimers when redistributing covered work. Distribution of binaries or other non-source forms must satisfy the applicable Corresponding Source requirements, including build and installation material required by the license.

Under section 13, a modified version that supports remote network interaction must prominently offer its interacting users access to that version's Corresponding Source at no charge through a suitable network mechanism. A generic link to another branch is insufficient when it does not contain the deployed modifications. Access required by the license must remain independent of PRO subscription state.

Production packaging must retain the exact source revision and required material, provide the applicable legal notices and source offer in the interface/distribution, and verify that source access survives commercial-service outages. No production source-offer interface, release archive, complete dependency-license review or release compliance qualification is claimed by adding these files.

## Development and release checklist

| Check | Required outcome |
| :--- | :--- |
| Source ownership and provenance | Contributors have authority to submit the material; retain third-party attribution and applicable terms. |
| Core metadata | `AGPL-3.0-only` agrees with the full license and project documentation. |
| Independent packages | Record each component's license and dependency graph; evaluate compatibility before publication. |
| Corresponding Source | Bind source and required build/install material to the exact distributed or network-served covered version. |
| Notices and access | Provide the applicable notices and source access without a premium key or subscription gate. |
| Release evidence | Record artifact/source identity, license inventory and unresolved restrictions; a catalogue entry is not a qualified release. |

## References and text provenance

[GNU AGPL version 3](https://www.gnu.org/licenses/agpl-3.0.html) supplies the governing terms; the [GNU licensing FAQ](https://www.gnu.org/licenses/gpl-faq.html#GPLPlugins) discusses plugin relationships. Read the complete license when assessing a specific distribution.

The text in LICENSE is copied verbatim from [SPDX's AGPL-3.0-only reference](https://raw.githubusercontent.com/spdx/license-list-data/main/text/AGPL-3.0-only.txt), retrieved November 2, 2026. Its SHA-256 is `d8a6cc31abc16b6748c7a21f21611f5a1ec33f67d22ca23d7da1c19b95496bee`. It is the GNU license text, without project-specific amendments. The project's version-only choice is stated above and in Composer metadata.
