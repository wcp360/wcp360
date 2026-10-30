# MASTER V3.9 --- Architecture to Code Traceability

  Contrat                                            Code cible principal
  -------------------------------------------------- --------------------------------
  650--669 Security                                  all + CI + deploy
  700--706 Control Plane                             services/wcpd + control schema
  710--713 System                                    Go OS adapters
  720--722 API                                       PHP apps + api/openapi
  730--733 Network                                   Go network adapters
  740--745 Web/PHP                                   web/php modules + adapters
  750--755 DNS/TLS                                   dns/tls modules + adapters
  760--765 Mail                                      mail module + stack adapters
  770--775 DB/Cache                                  database/cache modules
  780--785 Backup                                    backup module/adapters
  790--795 Security/SSH                              security module/adapters
  800--814 Domain/Accounts                           hosting domain/application
  820--844 Files/Apps/Dev                            scoped modules
  850--874 Migration/Billing/Monitoring              scoped modules/connectors
  880--921 Identity/Notify/Scheduler/Audit/Support   platform modules
  930--942 gated future                              not V1 critical path

## V4.0.10 normative deepening

- `OPS-4010-05-OPENTELEMETRY-TRACES.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
