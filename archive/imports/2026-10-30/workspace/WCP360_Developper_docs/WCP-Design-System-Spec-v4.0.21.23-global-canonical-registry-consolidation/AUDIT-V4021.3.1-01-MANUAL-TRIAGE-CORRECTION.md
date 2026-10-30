# V4.0.21.3.1 --- Manual Correction of Gap Triage

## Statut

**AUTHORITATIVE CORRECTION --- NO CODE**

The heuristic V4.0.21.3 triage over-classified several historical audit
documents as real gaps and, more importantly, must distinguish
**blocking P0 coverage gaps** from **open but optional product
decisions**.

## Blocking gaps

  --------------------------------------------------------------------------------------------------------------------------
  Evidence document                                   P0                         Why still open
  --------------------------------------------------- -------------------------- -------------------------------------------
  `AUDIT-V4021.1-07-API-COMPLETENESS-CORRECTION.md`   `P0-002`                   Full CORE API inventory is still not
                                                                                 demonstrated. V4.0.21.2 adds exact
                                                                                 Plan/Demo/Trial operations but does not
                                                                                 enumerate every CORE module/action.

  `AUDIT-V4021.1-08-DATA-SCHEMA-CORRECTION.md`        `P0-001`                   V4.0.21.2 adds field dictionaries for
                                                                                 selected CORE resources, not every CORE
                                                                                 resource in the module catalog.

  `AUDIT-V4021.1-09-ACCEPTANCE-CORRECTION.md`         `P0-008`                   V4.0.21.2 binds Plan/Demo/Trial tests, but
                                                                                 global exact
                                                                                 Operation→Job/Decision/RPC/Adapter→oracle
                                                                                 coverage is not demonstrated.

  `AUDIT-V4021.1-10-CORRECTED-P0-STATUS.md`           `P0-003/004/006/007/009`   These P0 statuses depend on the
                                                                                 still-incomplete global API/resource
                                                                                 inventory; they cannot be promoted globally
                                                                                 until coverage is exhaustive.
  --------------------------------------------------------------------------------------------------------------------------

## Open but non-blocking V1 decisions

  --------------------------------------------------------------------------------------------------------
  Document                               Class                   Reason
  -------------------------------------- ----------------------- -----------------------------------------
  `930-licensing-resource-contract.md`   `PRODUCT DECISION`      Commercial licensing provider/protocol
                                                                 and offline grace policy remain open, but
                                                                 MASTER-V1-STANDALONE-HOSTING-PROFILE.md
                                                                 explicitly lists the commercial license
                                                                 server as optional/non-blocking.

  `MASTER-OPEN-DECISIONS.md`             `ROADMAP DECISIONS`     HA, distributed storage, compute/SDN, AI,
                                                                 commercial licensing, external KMS/HSM,
                                                                 arm64 and Kubernetes/IaC remain open but
                                                                 are explicitly non-blocking for V1
                                                                 Standalone Hosting.
  --------------------------------------------------------------------------------------------------------

## Superseded decisions

  ------------------------------------------------------------------------------------------------------------------------------------------------
  Historical document                       Closed by                                                                      Reason
  ----------------------------------------- ------------------------------------------------------------------------------ -----------------------
  `MASTER-PACKAGE-REPOSITORY-STRATEGY.md`   `ADR-002-package-repository-strategy.md`                                       Package/repository
                                                                                                                           strategy is ACCEPTED.

  `MASTER-VERSION-SUPPORT-POLICY.md`        `ADR-001-certified-os-baseline.md + later lifecycle compatibility contracts`   Tier-1 OS baseline is
                                                                                                                           ACCEPTED; old DECISION
                                                                                                                           REQUIRED is historical.

  `V35-DEEP-NORMALIZATION-REPORT.md`        `ADR-001/003/004/005 and V4.0.16 ADRs`                                         The report predates
                                                                                                                           accepted decisions for
                                                                                                                           OS, control-plane DB,
                                                                                                                           jobs/events and
                                                                                                                           SecretStore.
  ------------------------------------------------------------------------------------------------------------------------------------------------

## False positives

  -----------------------------------------------------------------------------------
  Document                                        Reason
  ----------------------------------------------- -----------------------------------
  `REGISTRY-V4019-06-RPC-AUTH-TARGETS.md`         The word PARTIAL appears only in a
                                                  rule saying no PARTIAL P0 is
                                                  accepted; registry declares the RPC
                                                  authorization gap CLOSED.

  `SYSTEM-403-01-SUPPORTED-OS-CERTIFICATION.md`   'partial repositories' is a test
                                                  condition, not a status marker.
  -----------------------------------------------------------------------------------

## Conclusion

The relevant repository-entry problem is no longer "24 real open
documents". It is an **exhaustive global coverage problem** concentrated
around the CORE resource/action inventory and the contracts derived from
it.
