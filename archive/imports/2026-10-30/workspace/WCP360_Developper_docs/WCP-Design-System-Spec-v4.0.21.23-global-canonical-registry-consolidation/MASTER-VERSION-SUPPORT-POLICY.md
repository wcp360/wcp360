# MASTER VERSION & SUPPORT POLICY

## Statut

**DECISION REQUIRED.** Les sources V3.5 ne fixent pas encore les
versions certifiées.

## À décider avant production

-   distributions et versions exactes certifiées ;
-   architectures CPU supportées ;
-   navigateurs/versions supportés ;
-   versions minimales des backends/adapters ;
-   cadence WCP, canaux Stable/Preview éventuels ;
-   fenêtre N/N-1 entre control plane et agents ;
-   politique EOL et migrations.

## Contrat

`Detected` ne signifie pas `Certified`. L'UI doit distinguer
`supported`, `unsupported`, `unknown` et `degraded`.

## V4.0.12 normative deepening

- `LIFE-4012-01-PLATFORM-RELEASE-MODEL.md`
- `LIFE-4012-14-COMPATIBILITY-MATRIX.md`
- `LIFE-4012-15-DEPRECATION-EOL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
