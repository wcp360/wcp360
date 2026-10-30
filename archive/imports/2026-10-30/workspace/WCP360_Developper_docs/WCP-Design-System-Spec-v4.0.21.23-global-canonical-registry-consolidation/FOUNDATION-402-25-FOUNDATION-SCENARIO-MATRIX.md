# V4.0.2 --- Foundation Scenario Matrix

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Scénarios transversaux obligatoires

**Concurrent update** : deux acteurs modifient la même génération →
conflit explicite, aucun silent overwrite.

**Ambiguous timeout** : effet produit, réponse perdue → Job
ambiguous/verify → reconcile → résultat final.

**Worker crash** : lease expire → recovery policy → verify avant reprise
si effet possible.

**Dependency down** : provider externe indisponible → état
waiting/retry/degraded sans bloquer modules indépendants.

**Privilege denial** : action valide mais acteur hors scope → aucun Job
privilégié créé, audit du refus selon policy.

**Secret rotation partial failure** : nouveau credential créé mais
consumer update échoue → ancien reste valide jusqu'à recovery.

**Delete with dependencies** : suppression parent → impact calculé →
refus ou workflow explicite, jamais cascade invisible.

**Drift** : fichier managed modifié manuellement → détection →
classification → correction ou intervention selon risque.

**Clock drift** : TOTP/TLS/scheduler affectés → health dégradé et
diagnostic sans désactiver les protections.

**Disk full** : mutation config impossible → ancienne config valide
reste active, Job failed/degraded avec evidence.
