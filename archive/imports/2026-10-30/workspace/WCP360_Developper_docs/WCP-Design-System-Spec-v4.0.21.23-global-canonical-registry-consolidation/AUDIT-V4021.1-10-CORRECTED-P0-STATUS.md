# V4.0.21.1 --- Corrected P0 Closure Status

## Statut

**AUTHORITATIVE CORRECTION OF `AUDIT-V4021-13-P0-CLOSURE.md`**

  --------------------------------------------------------------------------------------------------
  P0                  V4.0.21 claim     V4.0.21.1         Reason
                                        corrected status  
  ------------------- ----------------- ----------------- ------------------------------------------
  P0-001 resource     PASS              **PARTIAL**       field
  schemas                                                 dictionary/types/nullability/cardinality
                                                          incomplete

  P0-002 exact API    PASS              **PARTIAL**       many CORE operations absent as rows

  P0-003 exact Jobs   PASS              **PARTIAL**       high-risk rows improved, but full API→Job
                                                          coverage depends on P0-002

  P0-004 exact        PASS              **PARTIAL**       conditional/generic mappings existed;
  RPC/adapter                                             corrected high-risk set still needs full
                                                          coverage

  P0-005 secret       PASS              **PASS WITH       branch condition formalized by
  rotation                              ERRATA**          `DEC-SECRET-DUAL-001`

  P0-006 RBAC         PASS              **PARTIAL**       shortcuts corrected, but missing API
  scope/step-up                                           actions imply missing action-specific
                                                          authorization rows

  P0-007 audit/event  PASS              **PARTIAL**       payload families exist; missing API
  payload                                                 actions need corresponding audit rows

  P0-008 acceptance   PASS              **PARTIAL**       action-family wording not exact Operation
  oracles                                                 IDs

  P0-009              PASS              **PARTIAL**       explicit policy IDs added; not every CORE
  delete/retention                                        resource has full delete row

  P0-010              PASS              **PASS WITH       key DAGs present; conditions now need
  cross-resource DAG                    ERRATA**          Decision IDs

  P0-011              PASS              **PASS**          sufficiently deterministic for
  naming/allocation                                       repository-entry design level

  P0-012              PASS              **PASS**          sufficiently deterministic for
  migration/version                                       repository-entry design level
  --------------------------------------------------------------------------------------------------

## Verdict

The previous `12/12 PASS` is withdrawn. Repository Entry remains
blocked.
