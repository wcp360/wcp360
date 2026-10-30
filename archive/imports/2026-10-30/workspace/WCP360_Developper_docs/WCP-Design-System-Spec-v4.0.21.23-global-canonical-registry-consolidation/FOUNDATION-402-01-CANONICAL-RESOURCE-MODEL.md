# V4.0.2 --- Canonical Resource Model

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Principe

Toute fonction WCP manipule une **Resource** identifiée, scopée et
auditable. Une ressource n'est jamais un simple formulaire ou un fichier
de configuration.

## Identité minimale

Chaque ressource possède conceptuellement : `resource_type`,
`resource_id` opaque, `tenant_scope`, `owner_ref`, `desired_state`,
`observed_state`, `lifecycle_state`, `generation`,
`observed_generation`, `created_at`, `updated_at`, et si nécessaire
`deleted_at`.

## Catégories

-   **Control-plane resources** : identities, roles, plans, jobs,
    policies, nodes.
-   **Hosting resources** : hosting accounts, domains, websites, mail
    domains/mailboxes, databases, runtimes.
-   **System resources** : service bindings, PHP pools, vhosts, DNS
    zones, certificates, Unix identities.
-   **External resources** : DNS-provider records, registrar objects,
    remote backup objects, billing references.

## Règles

Un ID public ne doit pas exposer UID Linux, ID SQL séquentiel ou secret.
Le nom affiché n'est jamais l'identité canonique. Un rename ne change
pas l'identité interne. Les relations parent/enfant doivent être
explicites.

## Desired vs observed

WCP sépare ce que l'utilisateur **demande** de ce qui est **réellement
observé** sur le serveur. `generation` augmente lorsqu'un desired state
change ; `observed_generation` indique jusqu'où le système a convergé.

## Suppression

La suppression est un lifecycle, pas un `DELETE` immédiat : validation
des dépendances → intent → job → effets → vérification →
tombstone/rétention selon la ressource → audit.
