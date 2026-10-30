# V4.0.18 — P0 Traceability Gaps

## Statut
**NORMATIVE AUDIT — V4.0.18 — NO CODE**

## Base auditée
V4.0.17 et ses 30 contrats `IMPL-4017-*`. Cette passe n'invente pas les mappings absents : `PASS` signifie explicitement supporté par le corpus ; `PARTIAL` signifie que la surface existe mais que la chaîne n'est pas fermée ; `N/A` doit être justifié ; `BLOCKED` empêche le Repository Entry Gate.

## Findings P0
1. **API action inventory incomplet** : `/auth`, `/me`, `/mail`, `/backups`, `/security`, `/system` sont des familles mais ne donnent pas encore chaque resource/action/method/status/idempotency/concurrency rule.
2. **Capability inventory incomplet** : `IMPL-4017-18` définit le modèle et des exemples, pas la liste canonique capability × resource × action × scope.
3. **Job-or-N/A decision incomplète** : chaque mutation CORE doit indiquer explicitement Job type ou justification transactionnelle `N/A`.
4. **RPC-or-N/A decision incomplète** : chaque Job doit indiquer les RPC autorisés ou qu'il ne franchit pas la frontière root.
5. **Adapter mapping incomplet** : chaque Job doit désigner l'adapter et sa méthode conceptuelle `plan/apply/verify/observe/reconcile`.
6. **Acceptance IDs absents** : les matrices sécurité/E2E décrivent les scénarios mais ne leur donnent pas d'identifiants traçables par capability.
7. **Audit event catalog incomplet** : le record est défini, mais la liste action/outcome obligatoire par mutation CORE n'est pas fermée.

## Gate
Ces sept findings empêchent un PASS strict de `IMPL-4017-29`.
