# Main protection — current single-maintainer activation settings

Authority: [D-GOV-SOLO-MAIN-0001](../decisions/D-GOV-SOLO-MAIN-0001.md).
This supersedes the older two-approval main proposal in README.md. No GitHub
Settings change is performed by adding these files.

Reviewed configuration: [disabled model](wcp360-main.disabled.json) and
[activation payload](wcp360-main.active.json), versioned in this repository.
No download or manual source-file transfer is required to review them.

An administrator may enter these values directly in repository Settings ->
Rules -> Rulesets -> New branch ruleset:

- Name: WCP360 - Main Protection; enforcement Active; target main only.
- Bypass list empty; restrict deletions; block force pushes.
- Require a pull request; zero required approvals; no code-owner requirement;
  no most-recent-push approval; dismiss stale reviews and resolve conversations.
- Required checks: repository-secrets and gitignore-regression, source GitHub
  Actions app15368; require the branch to be up to date before merging.
- Do not add a Restrict updates/creation lock or wildcard branch target.

The existing main workflow contains the required check names; successful results
on each proposed promotion are still required. Do not promote develop directly
to main or create new branches to work around the approved routing policy.
The repository JSON preserves do_not_enforce_on_create=true from the reviewed
model; main already exists. This does not authorize branch creation.

After actual activation, record the new Ruleset ID and check main's protected
flag and exact returned parameters. Configuration readback is not by itself a
negative-push/force/delete test. No destructive test should be used as a probe.

Current administration remains unavailable to the connected integration (403).
An active-looking JSON file is only a payload until GitHub installs it. Do not
report global branch-protection completion while main remains unprotected.
