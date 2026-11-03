# WCP360 Core licensing

**Public community license: `AGPL-3.0-only` (effective November 2, 2026). Dual-licensing direction recorded November 3, 2026.**

## License grant and scope

WCP360 Core's original first-party source in `WCP360_Core/` is publicly offered under the **GNU Affero General Public License, version 3 only**. You may use, study, modify and redistribute it under that license. The complete terms are in [LICENSE](LICENSE); [composer.json](composer.json) identifies the public source distribution.

This grant covers the original Core application, its built-in modules, first-party agents, installer source, templates and supporting source in this directory. It does not replace separately identified third-party licenses or notices. Dependencies retain their own licenses, including those recorded in [composer.lock](composer.lock) and each dependency's distribution. Generated runtime data, credentials and customer content are not a grant of rights to disclose those materials.

This is a scoped Core license. It does not silently relicense the SDK, application recipes, existing independent plugins, premium proposals, imported reference material or the entire repository. Those components need their own explicit compatible license and provenance before distribution.

## Open source and commercial editions

The Core must function with zero optional packages. Its essential hosting, administration, permissions, security, local backup/recovery and extension infrastructure remain open source. An entitlement check cannot be a condition for exercising rights granted by the Core license.

The project direction is a **dual-licensing model**: the community source remains available under AGPL-3.0-only, and the authorized rights holders may separately offer a commercial/proprietary license for eligible Core versions to customers who need different usage, integration or redistribution terms. Recipients already using the AGPL grant keep the rights that grant gives them; a commercial offer does not withdraw or replace those rights.

This document and the AGPL [LICENSE](LICENSE) are **not** a commercial license agreement and do not themselves grant proprietary-use exceptions. No commercial agreement, licensing entity, eligibility rules, fees, terms or signing process is published here; the commercial option is a product direction, not yet an available grant. Until a separate written agreement is made available and accepted, the public Core source is offered under AGPL-3.0-only.

Offering a commercial license requires confirming that the licensor has authority over all covered first-party contributions and publishing the agreement's scope and terms. Third-party dependencies, imported material, the SDK, application recipes, independent plugins and premium packages remain outside this Core grant unless their own rights and licenses expressly say otherwise. A separate folder, process, API or SDK does not by itself establish that a component is legally independent of the Core. Premium package source and licenses therefore remain **undecided**; assess code reuse, linking, communication and dependencies before selecting their terms. This policy grants no blanket proprietary-plugin exception.

## Source and notice obligations

Keep the license, applicable copyright notices, modification notices and warranty disclaimers when redistributing covered work. Distribution of binaries or other non-source forms must satisfy the applicable Corresponding Source requirements, including build and installation material required by the license.

Under section 13, a modified version that supports remote network interaction must prominently offer its interacting users access to that version's Corresponding Source at no charge through a suitable network mechanism. A generic link to another branch is insufficient when it does not contain the deployed modifications. Access required by the license must remain independent of PRO subscription state.

Production packaging must retain the exact source revision and required material, provide the applicable legal notices and source offer in the interface/distribution, and verify that source access survives commercial-service outages. No production source-offer interface, release archive, complete dependency-license review or release compliance qualification is claimed by adding these files.

## Development and release checklist

| Check | Required outcome |
| :--- | :--- |
| Source ownership and provenance | Contributors have authority to submit the material; retain third-party attribution and applicable terms. |
| Public Core metadata | `AGPL-3.0-only` agrees with the full license and public source distribution. |
| Commercial option | Publish and authorize a separate written agreement before advertising or granting proprietary Core rights. |
| Rights to dual-license | Confirm authority for all covered contributions and exclude material not controlled by the licensor. |
| Independent packages | Record each component's license and dependency graph; evaluate compatibility before publication. |
| Corresponding Source | Bind source and required build/install material to the exact distributed or network-served covered version. |
| Notices and access | Provide the applicable notices and source access without a premium key or subscription gate. |
| Release evidence | Record artifact/source identity, license inventory and unresolved restrictions; a catalogue entry is not a qualified release. |

## References and text provenance

[GNU AGPL version 3](https://www.gnu.org/licenses/agpl-3.0.html) supplies the governing terms; the [GNU licensing FAQ](https://www.gnu.org/licenses/gpl-faq.html#GPLPlugins) discusses plugin relationships. Read the complete license when assessing a specific distribution.

The text in LICENSE is copied verbatim from [SPDX's AGPL-3.0-only reference](https://raw.githubusercontent.com/spdx/license-list-data/main/text/AGPL-3.0-only.txt), retrieved November 2, 2026. Its SHA-256 is `d8a6cc31abc16b6748c7a21f21611f5a1ec33f67d22ca23d7da1c19b95496bee`. It is the GNU license text, without project-specific amendments. The public source distribution's version-only choice is stated above and in Composer metadata. The commercial option, if made available, is a separate agreement and is not encoded as an alternate license in the public package metadata.
