# Deferred dependency migrations — November 3, 2026

These five proposals were reviewed during the all-branch consolidation and deliberately **not merged into the adopted runtime**. Their exact commits remain reachable through non-release archive tags. Complete Git diffs are retained here as compressed evidence, with raw and compressed SHA-256 checksums in [MANIFEST.json](MANIFEST.json).

| Proposal | Deferred change | Reason |
| :--- | :--- | :--- |
| [#119](https://github.com/yyogas/wcp360/pull/119) | Symfony Process 7.4 → 8.1 | The Core retains its selected Symfony 7.4 component line. |
| [#122](https://github.com/yyogas/wcp360/pull/122) | Symfony CssSelector 7.4 → 8.1 | Requires a coordinated major migration and qualification. |
| [#123](https://github.com/yyogas/wcp360/pull/123) | Symfony TwigBundle 7.4 → 8.1 | Requires a coordinated major migration and qualification. |
| [#124](https://github.com/yyogas/wcp360/pull/124) | Symfony Dotenv 7.4 → 8.1 | Requires a coordinated major migration and qualification. |
| [#120](https://github.com/yyogas/wcp360/pull/120) | Docker PHP 8.4.27 → 8.5.11 | Conflicts with Core's PHP ~8.4.0 constraint and runtime catalogue; its CI fails before Composer while building PHP extensions. |

The Symfony disposition concerns the chosen version line, not a claim that all Symfony 8 components are incompatible with PHP 8.4. The PHP build failure is recorded in [run 41544583123](https://github.com/yyogas/wcp360/actions/runs/41544583123/job/125616930053).

## Preservation and inspection

Each `pr-N.diff.gz` expands to the complete native Git diff from the recorded merge base to the frozen proposal head. The manifest records every changed path, mode, base/head blob ID, byte count and checksum. All nine changed-file pairs were compared with the complete PR file lists; no truncated API patch is substituted for the native diff.

Inspect the proposal directly with its `archive/deferred-pr-N-20261103` tag or decompress the corresponding file. The tags preserve the original Git history; they are not product releases. Review the original base and the current target before proposing a later migration. These files are evidence and must not be executed or automatically applied.

## Update policy

The [Dependabot configuration](../../../../.github/dependabot.yml) retains weekly checks. Routine Composer and GitHub Actions version proposals are limited to minor/patch updates, compatible Actions proposals are grouped, and Docker proposals are limited to patches. Major migrations can be proposed and reviewed explicitly.

GitHub documents that `allow.update-types` filters version updates without filtering security updates. No `ignore` rule, disabled update block, advisory dismissal, audit exclusion or test bypass was added. This record does not establish that the repository has no open security advisories. [Official Dependabot reference](https://docs.github.com/en/code-security/reference/supply-chain-security/dependabot-options-reference)

See the [branch-completion record](../BRANCH-COMPLETION-2026-11-03.md) for accepted proposals, CI identities and final branch disposition.
