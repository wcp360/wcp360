# 290 --- Security Center

## Statut et origine

\[SOURCE\] Security & Access + Monitoring + AI security assistant.

## Objectif

Agréger posture sécurité sans remplacer les modules spécialisés.

## Pages / sous-vues

Overview; Findings; Firewall; SSH; Fail2ban; WAF; Auth; Updates; AI
audit.

## Ressources et données

finding, severity, evidence, resource, status, owner.

## Actions opérateur

Open remediation; dismiss/accept risk only if governance defines it.

## Monitoring, santé et logs

Findings trend only from real history.

## Sécurité, permissions et audit

Security roles; audit governance decisions.

## Contrat backend attendu

Findings API with evidence/correlation.

## États UI obligatoires

Loading, skeleton, empty, no-results, partial data, stale telemetry,
permission denied, service unavailable, maintenance, action pending,
action failed, success confirmé.

## Règles UX communes

-   Afficher serveur/nœud, scope et cible avant toute mutation.
-   Distinguer `Save`, `Apply`, `Reload` et `Restart`.
-   Une modification de configuration sensible présente un diff si
    possible.
-   Toute opération longue crée un job dans Task Center.
-   Ne jamais afficher un pourcentage de progression artificiel.
-   Les commandes/configurations proposées par l'UI restent validées et
    autorisées côté backend.
-   Les secrets sont redacted dans logs, audit et notifications.

## Critères d'acceptation

Une recommandation IA est visuellement distincte d'un finding
déterministe.

## V4.0.7 normative deepening

- `SECOPS-407-21-SECURITY-CENTER-FINDINGS.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.
