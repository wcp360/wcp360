# 73 --- Matrice de tests et quality gates

## P0

Tests unitaires des primitives critiques, intégration RBAC/scope, E2E
des workflows principaux, erreurs réseau, concurrence, permissions
refusées, action destructive, job async.

## Accessibilité

Automatique + manuel : clavier seul, focus, lecteur d'écran sur
workflows P0, zoom 200 %, contraste, reduced motion.

## Internationalisation

Pseudo-localisation, texte long, RTL sur composants structurants,
formats date/nombre/unité.

## Sécurité UI

XSS dans valeurs affichées/logs, secret redaction, changement de scope,
bulk action, stale form, session expirée.

## Navigateurs

Matrice définie par politique produit ; aucun navigateur n'est ajouté
implicitement dans cette spécification.

## Gate

Un P0 ne passe pas Stable avec un défaut critique sécurité/accessibilité
ou un workflow E2E cassé.
