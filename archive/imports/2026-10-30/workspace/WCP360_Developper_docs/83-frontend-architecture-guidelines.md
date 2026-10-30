# 83 --- Architecture frontend : lignes directrices

## Origine

\[SOURCE\] La documentation WCP-USER prévoit modules, components,
layouts, store, locales, services et hooks IA.

## V2

Organisation par domaine fonctionnel, avec primitives UI séparées du
métier. Les permissions, contexte, jobs, notifications et i18n sont des
services transversaux.

## État

Éviter un store global monolithique. Distinguer état serveur, état UI
local, préférences persistées et contexte de sécurité.

## Routes

Lazy loading par domaine si pertinent. Guards UI pour expérience ;
jamais comme contrôle de sécurité final.

## Erreurs

Error boundaries/équivalent aux frontières de modules, page de
récupération et correlation id lorsque fourni.
