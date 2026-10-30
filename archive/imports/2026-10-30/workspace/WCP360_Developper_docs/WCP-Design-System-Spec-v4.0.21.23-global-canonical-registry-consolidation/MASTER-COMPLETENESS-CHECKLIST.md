# MASTER COMPLETENESS CHECKLIST

## Gate avant développement d'un module

-   Owner produit/technique identifié.
-   WCP-USER/WCP-ADMIN/reseller scope défini.
-   Ressources et machine d'états définies.
-   Capabilities backend définies.
-   Permissions et isolation tenant définies.
-   API REST et erreurs définies.
-   Audit défini pour mutations sensibles.
-   Job async défini pour opérations longues.
-   Secrets/redaction définis.
-   Loading/empty/error/denied/stale/partial définis.
-   Responsive + clavier + WCAG + i18n/RTL définis.
-   Logs/métriques et fraîcheur définis.
-   Preflight/diff/rollback lorsque pertinent.
-   Tests unit/integration/E2E/security/accessibility définis.
-   Documentation/help/migration/deprecation définies.

## Gate plateforme

Aucune fonctionnalité UI critique ne doit exister uniquement dans le
frontend. Toute action métier doit être accessible via le service de
domaine/API conformément au contrat API-first.
