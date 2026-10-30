# 590 --- Kubernetes / K3s Adapter

## Positionnement

P2/plugin. WCP ne devient pas un clone de Rancher.

## Scope initial

Connect cluster, namespaces/scopes, workload summary,
deployments/services/ingress limited management, logs/events,
application deployment hooks.

## Sécurité

Kube credentials/service account scoped ; RBAC Kubernetes respecté ; pas
de cluster-admin par défaut.

## Contrat transversal

-   **WCP-ADMIN** porte l'administration système/infrastructure et les
    politiques globales.
-   **WCP-USER** ne voit que les capacités explicitement déléguées à son
    scope client/revendeur.
-   Les technologies concrètes sont des **adapters/capabilities** ; le
    cœur WCP dépend d'un contrat stable, pas d'un fournisseur unique.
-   Toute mutation sensible exige autorisation backend, audit, preflight
    lorsque pertinent et résultat confirmé.
-   Toute opération longue est un job observable, annulable/retryable
    lorsque techniquement sûr.
-   Secrets et données sensibles sont redacted dans logs, audit,
    notifications, traces et sorties IA.
