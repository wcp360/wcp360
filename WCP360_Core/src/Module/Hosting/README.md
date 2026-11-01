# Hosting

**Status: executable development candidate for hosting plans and guarded PHP-FPM activation; the complete production hosting lifecycle is not implemented.**

Sites, domains, TLS, web-engine selection and hosted language-runtime assignments.

Start here when changing: **Website creation, virtual hosts, certificates and PHP pool settings.**

## Implemented candidate paths

| Concern | Actual source and scope |
| :--- | :--- |
| Hosting requests | [SubmitHostingPlan](Application/SubmitHostingPlan.php) validates tenant permission, lowercase ASCII `.test` names and idempotency before creating a durable plan job. |
| Runtime selection | [ApplicationServerPolicy](Application/ApplicationServerPolicy.php) selects PHP-FPM by default or explicitly enabled FrankenPHP Worker, with worker compatibility acknowledgement. Global defaults belong to Platform. |
| Configuration plans | [GoPlanRenderer](Infrastructure/GoPlanRenderer.php) calls the fixed, bounded [Go planner](../../../agents/wcpd/candidate/planner/README.md). Its candidate templates render Nginx, PHP-FPM or FrankenPHP configuration; rendering alone does not activate a site. |
| Physical activation | [SubmitActivation](Application/SubmitActivation.php) and [RunActivation](Application/RunActivation.php) require a separate activation grant, check the planned revision and distinguish failure from an uncertain outcome. Inspection reconciles an uncertain result without blindly replaying the apply operation. |
| Execution boundary | [DisposableExecution](Infrastructure/DisposableExecution.php) restricts this activation experiment to an explicitly prepared disposable container or Debian 13 VM. It does not authorize production execution or privileged execution on every recognized OS. |

## Remaining scope

Production account/site lifecycle, real-domain admission, authoritative DNS, ACME/TLS, runtime/version administration, quotas and full change/recovery workflows remain to be implemented and qualified. The worker plan selector is not a general production FrankenPHP installer or activation service.

Use the common [module layout](../README.md) for Domain, Application, Infrastructure and UI code. Hosting owns Nginx/Apache/PHP-FPM policy. The [service resource index](../../../resources/services/README.md) identifies canonical configuration ownership and links the existing candidate renderers; controllers must not duplicate their configuration text.

- [Core navigation index](../../../NAVIGATION.md)
- [Core roadmap and completion evidence](../../../ROADMAP.md)
- [System-service resources](../../../resources/services/README.md)
- [Core implementation and acceptance](../../../../documentation/development/CORE-ENGINE.md)

The README groups several capability domains here: websites, authoritative DNS, certificates/ACME and hosted PHP/PHP-FPM. Runtime assignment, effective settings, pool health, permitted PHP extensions and a site's Composer/CLI task remain scoped to that hosted project and identity; they are distinct from the Core Composer dependency graph. The [capability map](../../../CAPABILITY-MAP.md) identifies collaborators without creating a second resource authority.
