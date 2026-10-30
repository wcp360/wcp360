# 71 --- Workflows E2E

## Objectif

Définir les parcours que l'équipe doit prototyper et tester de bout en
bout avant livraison.

## Règles obligatoires

Chaque workflow documente : préconditions, rôle/scope, entrée, étapes,
validations, tâches asynchrones, erreurs, audit, notifications, rollback
et résultat.

## Pages et cas couverts

### WCP-USER prioritaires

1.  Ajouter domaine → DNS → SSL automatique → vérification.
2.  Créer boîte mail → quota → délivrabilité.
3.  Créer base → utilisateur → privilèges → accès distant.
4.  Sauvegarder → restaurer un fichier/base.
5.  Changer PHP → vérifier compatibilité → appliquer.
6.  Créer clé SSH / token API.

### WCP-ADMIN prioritaires

1.  Créer compte → package → domaine → quotas.
2.  Suspendre/réactiver compte.
3.  Modifier firewall avec diff + confirmation.
4.  Redémarrer service et diagnostiquer échec.
5.  Mise à jour serveur avec maintenance + résultat.
6.  Backup policy → échec destination → retry.
7.  Migration compte inter-serveurs.
8.  Incident disque critique → alerte → investigation → action.
9.  Mission IA audit_firewall → findings → approbation éventuelle.
10. Plus tard : ajout node → placement → rolling update.

## États à prévoir

Happy path + validation error + permission denied + timeout + partial
success + backend unavailable + stale state + concurrent modification.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
