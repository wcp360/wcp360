# 85 --- Rétention, confidentialité et redaction

## Objectif V2

Le projet source ne définit pas une politique complète de rétention ; ce
document crée les exigences à décider avant production.

## À définir

Durées de rétention : logs, audit, jobs, notifications, rapports IA,
métriques, sauvegardes.

## Redaction

Secrets, mots de passe, tokens, clés privées et données sensibles ne
doivent pas être copiés dans logs/audit/IA.

## Export/suppression

Les obligations légales et contractuelles dépendent du contexte de
déploiement et doivent être validées séparément ; cette spécification ne
les invente pas.

## UI

Afficher la rétention lorsqu'elle a un impact utilisateur, par exemple
logs disponibles 30 jours, uniquement une fois la politique réellement
définie.
