# Repository transfer — November 3, 2026

WCP360's official repository is **[wcp360/wcp360](https://github.com/wcp360/wcp360)**. This record covers local Git configuration and navigation maintenance after the transfer from the personal namespace.

## Verified identity and local configuration

| Item | Observed result |
| :--- | :--- |
| GitHub repository ID | `1396561464`, unchanged by the transfer. |
| Previous location | `https://github.com/yyogas/wcp360`. |
| Official location | `https://github.com/wcp360/wcp360`. |
| Fetch/push remote | `origin = https://github.com/wcp360/wcp360.git`. |
| Default and local branch | `main`; local and transferred remote matched `e02d01d657d1e687ca57e16f07021a9f7e73162d` before this documentation change. |
| Branch/PR snapshot | One remote branch, main; no open pull requests. |
| Read/write context | Windows PowerShell and the existing bundled Git toolchain; no local application runtime started. |

The repository metadata and branch were read through the GitHub connector. Git fetch against the new origin confirmed the matching revision.

## Navigation maintenance

The change canonicalizes **124 repository URLs in 31 maintained Markdown documents**, including the public overview, architecture/catalogue navigation, contribution/security links, PR template and Core/development guides. Commit SHAs, PR numbers, workflow/job IDs and historical result counts remain unchanged.

Three public capability/extension links now target the English [GitHub overview](../../../README_GITHUB.md), where their anchors exist. The removed specifications index is linked at its [existing historical revision](https://github.com/wcp360/wcp360/blob/da265278c20e230d5ba4614647a8271f4b0d7506/documentation/specifications/README.md). The architecture table now identifies main and the completed integration rather than the retired implementation branch.

The [documentation index](../../README.md) links this record. No badges needed an owner change: the current badges use generic Shields labels.

## Historical identities and evidence

The transfer does not rename source-package identities. Existing Go modules, imports, local require/replace relationships, strict import allowlists and pinned input descriptors remain byte-for-byte unchanged. Their legacy `github.com/yyogas/wcp360` identity must be migrated together in a separately qualified code change if that becomes necessary.

Dated governance/branch evidence, frozen provenance JSON, original dependency locks, canonical specifications, File Manager baseline data and compressed proposal diffs are preserved. Their original repository strings are historical evidence, not missed navigation replacements. User handles in CODEOWNERS are identities and were not replaced with an organization name.

The concurrent `WCP360_SDK/php/registry-client/examples/local-registry.php` edit remains outside this change. Runtime behavior, permissions, security-policy requirements, dependency versions and recorded qualification levels are unchanged.

## Verification

| Check | Status and scope |
| :--- | :--- |
| Repository identity and remote synchronization | passed — same numeric repository ID and matching main revision at the transfer checkpoint. |
| Navigation URL replacement | passed — reviewed file allowlist and preserved URL suffix identities. |
| Markdown paths and repaired anchors | passed — static verification of changed documents and the historical index object. |
| Source/evidence preservation and patch whitespace | passed — scoped Git diff and static checks; no runtime or lock changes included. |
| New local executable tests | not_run — local runtime execution remains stopped; this is documentation/configuration maintenance. |
| New production or executable qualification | not_applicable — the [Core checkpoint](../../../WCP360_Core/STATUS.md) retains its exact tested source and evidence. |
| CODEOWNERS user permissions | not_run — the connector refused the collaborator-permission endpoint; no access-control change or validation is claimed. |

Relevant commands performed were `git remote set-url origin https://github.com/wcp360/wcp360.git`, `git fetch --prune origin`, remote/main identity inspection and scoped `git diff --check`. The private static manifest records the edited paths and their before/after SHA-256 values.

This is a repository-location update. The [Core working guide](../../../WCP360_Core/WORKING-GUIDE.md), [branch-completion snapshot](../branches/BRANCH-COMPLETION-2026-11-03.md) and existing production acceptance requirements retain their distinct scopes.
