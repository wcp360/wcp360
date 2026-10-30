# V4.0.2 --- Capability, Adapter & Certification Model

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Trois notions distinctes

**Detected** : logiciel/service observé. **Supported** : WCP possède un
adapter compatible. **Certified** : combinaison version/OS/profil testée
par la matrice de certification.

## Capability

Inclut provider, version, features, constraints, health et evidence
timestamp.

## Adapter

Contrat stable avec discover/plan/apply/verify/reconcile selon son
domaine. Les différences OS/provider restent dans l'adapter, pas dans
les controllers.

## Dégradation

Une capability absente masque/interdit la fonction concernée sans rendre
tout WCP inutilisable.
