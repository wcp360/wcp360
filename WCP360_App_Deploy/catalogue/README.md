# Editorial catalogue interpretation

**Status: candidate metadata mapping. No recipe loader, permission grant or deployment route is enabled by this directory.**

The preserved [CSV](../WCP360_App_Catalogue_Candidat.csv) is the canonical editorial input for Core's bounded candidate reader. [Markdown](../WCP360_App_Catalogue_Candidat.md) and [XLSX](../WCP360_App_Catalogue_Candidat.xlsx) remain unchanged companion documents. Read the [audit](../docs/CATALOGUE-AUDIT.md) before consuming them.

There is no parallel 410-entry JSON index. Keep all 70 source categories and their original labels/order. CSV and Markdown have identical shared fields per name but different row positions. Future stable local catalogue keys, aliases/editions and recipe versions require reviewed provenance mappings; source row numbers, labels and generated slugs are not canonical Core identities.

## Sixteen-profile mapping

[profile-routing.json](profile-routing.json) contains one record per existing source profile, in source-profile first-occurrence order. It decomposes mixed labels into preparation axes. The word routing means editorial classification, not runtime dispatch.

| Field | Closed meaning |
| :--- | :--- |
| schema_version | Local mapping revision 1; no accepted Core schema is selected. |
| status / scope | draft / editorial_profile_mapping only. |
| source | The preserved CSV reference, not a caller-selected runtime path. |
| execution_authority | Always false. No enabled flag or executable instruction is present. |
| profiles | Exactly the sixteen unique source-profile records. |
| profile | One exact existing CSV profile label. |
| method / isolation / hardware / eligibility | Fixed metadata values in the committed mapping; unknown values require an explicit mapping revision/review, never a fallback executor. |
| access | A nonempty list of exact source access labels occurring for that profile, not IAM roles or grants. |

The root contains only schema_version, status, scope, source, execution_authority and profiles. Each profile record contains only profile, method, isolation, hardware, access and eligibility. Future consumers must reject unknown keys, missing/wrong-type values, unknown/duplicate profiles and unexpected enum/access values. This document does not supply a runtime parser for the mapping.

Source access values are USER, MASQUE, USER-CONDITIONNEL, LAB-PRIVE, ADMIN-DELEGABLE, ADMIN, USER-BORNE and USER-DEVELOPPEUR. Preserve the per-row value; membership in a profile's list does not override it. Hidden, laboratory, migration-only, unresolved or reinforced-review entries remain subject to their individual source state and future qualification.

## Admission remains separate

PHP and DEPLOY describe possible mechanisms; ISOLE describes enhanced isolation; CPU-GPU needs model/backend capacity review. Extensions require parents, dependencies/builds/templates require project workflows, clusters/VMs need separate placement, and QUALIFIER needs identity resolution.

No mapping makes any row installable. A source cell claiming verified or qualified status cannot enable an operation. Current Core authorization, trusted artifact/provider admission, commercial rights where applicable, compatible recipe and exact-tuple qualification remain independent requirements.

The candidate Core command reads only fixed CSV metadata through the [typed boundary](../docs/CORE-INTEGRATION.md); it does not load this JSON or recipe descriptors. Never parse a spreadsheet macro, include PHP from this workspace, follow an editorial URL as a privileged installer or turn a row into a shell command.

For new work, use the [candidate descriptor](../schemas/app-recipe.schema.json), [authoring template](../_templates/application/README.md) and [qualification](../docs/QUALIFICATION.md). They remain drafts until their own contract and consuming implementation are reviewed.
