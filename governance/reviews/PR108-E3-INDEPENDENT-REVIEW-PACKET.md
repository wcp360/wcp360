# WCP360 — PR #108 — dossier de revue indépendante E3

Statut : **PRÊT À SOUMETTRE À UN RELECTEUR — E3 NON EFFECTUÉE**.
Préparé le 2026-10-30 par SOURCE CODE ; classement G1 / collecte E2 du même auteur.
Ce dossier prépare la revue. Il ne contient aucun verdict indépendant, aucune autorisation de merge et aucune clôture d'audit.

## Identité et périmètre à relire

| Élément | Valeur figée |
|---|---|
| PR | [#108](https://github.com/yyogas/wcp360/pull/108), ouverte, sortie du mode draft |
| Branche | `source/sc-m01-inert-daemon` |
| SHA à relire | `71deefa2976a7005983d410907dc2050287be05f` |
| Arbre du candidat | `5634f6216b1b445293b3a85990bc004bfeae7e87` |
| Base déclarée par l'API PR lors du relevé | `main` / `3fd2f8007baa49c2454cc89c0a5488a57c77911b` |
| Main observé séparément | `038617047fb6dc701ad1c2345f4742b890c745e2` |
| Classes | I1 implémentation + R1 CI ; contraintes Security applicables au daemon et au scanner |
| GO applicable | MASTER `3fd2f8007baa49c2454cc89c0a5488a57c77911b` : fondation daemon inerte seulement |

Comportement attendu : un module Go standard library ; `wcpd` affiche l'aide, la version ou un état inerte, puis termine. Entrées non supportées rejetées sans écho. Une écriture impossible ou tronquée doit rendre un échec. Aucun listener, IPC-B, accès DB, effet privilégié, session Web, installateur ou release.

Les limites WCP-USER/WCP-ADMIN et backend → wcpd non-root → IPC authentifié borné → wcp-privd root minimal restent des contraintes futures. Cette PR n'implémente pas cette chaîne. Sprint 0 annulé et V4.1 sont exclus.

## Sources normatives et navigation

- [Manifeste et GO limité](../../manifests/WCP360-BM-0001.md) : DOC v4.0.21.23, DESIGN V2.5.3.36.4, CODE NONE, huit artefacts acceptés.
- [Politique E0/E1/E2/E3 et liaison au SHA](../REVIEW-MERGE-POLICY.md), notamment §§1–4 et 12.
- [Addendum DevOps v1.0](../../specifications/devops/WCP360_SC-M01_DAEMON_FOUNDATION_TOOLCHAIN_ADDENDUM_v1.0.md), blob `f8608d6258f87b8c1099b9b3a7e32ec95c72dc27`.
- Engineering **v0.3** est la version normative sélectionnée ; son ancien nom de fichier v0.2 est un locator, pas une autre version active.
- [Preuves de correction conservées dans main](PR108-CORRECTION-EVIDENCE-2026-10-28.md). Les sections sur les anciens candidats sont historiques ; les avis propriétaires ultérieurs ci-dessous complètent ce document.
- [Snapshot de sources, fichiers, avis et CI](evidence/PR108-PR114-E3-SNAPSHOT-2026-10-30.json) : empreintes des sources normatives, relevé API et résultats extraits des logs.
- [Dossier #114](PR114-E3-INDEPENDENT-REVIEW-PACKET.md) : changement séparé, qui n'est pas incorporé au SHA #108.

## Inventaire exact des 13 fichiers du diff

Les SHA ci-dessous sont des Git blobs, pas des SHA-256 de fichiers.

| Fichier | Blob au candidat #108 |
|---|---|
| `.github/workflows/sc-m01-daemon-foundation.yml` | `46c8412415d8695e25fa35769878d74f5f7cf33f` |
| `SOURCE/README.md` | `31e447ead422c62017634a127c127fba6b6df1f3` |
| `SOURCE/daemon/README.md` | `4a4522e8353757d0e2e99f5848843c176497cb67` |
| `SOURCE/daemon/cmd/wcpd/main.go` | `6dbb415b9af0c65ed5908b73e41fe3bca04a07a5` |
| `SOURCE/daemon/go.mod` | `ac9aa79f44bcf9150d18c5e00b73d0b790fc9780` |
| `SOURCE/daemon/inputs.json` | `ef0e49d9103a5331a5a9cad87785d9143d5f9529` |
| `SOURCE/daemon/internal/cli/cli.go` | `e708f7b89d0e6476929cf0a93583163078f6b1c5` |
| `SOURCE/daemon/internal/cli/cli_test.go` | `0b68c1075b84d5408b1a45741fae151a90958005` |
| `SOURCE/daemon/tools/check-negative.sh` | `f082c159d76db49ef73cbe2fba912a483e8ee031` |
| `SOURCE/daemon/tools/check.sh` | `e9f19e445f7549fbdfdc121f4e3c16f029533823` |
| `SOURCE/daemon/tools/govulncheck.modules` | `6adc82b999b7f55f374cc8e309207b00afea25eb` |
| `SOURCE/daemon/tools/scan.sh` | `39cc10585ea5653746c23e1c300523890d49f756` |
| `out/.gitignore` | `d6b7ef32c8478a48c3994dcadc86837f4371184d` |

Comparer le diff à cette liste et lire chaque fichier. La liste de diff API ne remplace pas la revue de l'arbre complet, des dépendances et du contexte de la base.

## Preuves E1 déjà exécutées

| Run / job | Événement | Commit réellement exécuté | Résultat relu |
|---|---|---|---|
| [40613003105 / 122569155775](https://github.com/yyogas/wcp360/actions/runs/40613003105) | push, tentative 1 | `71deefa2976a7005983d410907dc2050287be05f` | SUCCESS ; 13 gates ; 6 fixtures négatives |
| [40613008116 / 122569185801](https://github.com/yyogas/wcp360/actions/runs/40613008116) | pull_request, tentative 1 | `cc2581bc21013377d8e3b3242be633a98015ee51` | SUCCESS ; 13 gates ; 6 fixtures négatives |

Le merge de test `cc2581b...` a pour parents `768a411b02c68e8845372c28cabe7c157f88766f` et le candidat #108. Ce parent est distinct de la base déclarée dans la réponse PR et du main observé aujourd'hui. Depuis `768a411...`, le diff vers main observé ne porte que sur le document de preuves de correction. **Aucun de ces identifiants ne doit être présenté comme une exécution sur un futur main.**

Environnement : Debian 13.7 linux/amd64 et Go 1.27.3 épinglés ; UID/GID 10001 ; compilation, unités et fixtures hors réseau ; source et racine de conteneur en lecture seule ; pas de socket Docker dans les conteneurs de tests. Le runner Ubuntu orchestre le build et ne constitue pas la cible de compilation.

Résultats déjà exécutés, relus pour le dossier :
- cinq tests unitaires CLI de premier niveau, dont quatre sous-tests aide/version ; couverture CLI 100 %, wrapper cmd 0 % en couverture unitaire, avec smoke binaire séparé ;
- six mutations rejetées au gate prévu : version Go, dépendance interdite, go.sum périmé, formatage, vet, assertion unitaire ;
- inventaire exact des 13 gates PASS, incluant build, reproductibilité bornée et scan ;
- govulncheck v1.9.0 : `No vulnerabilities found.` sur ces exécutions ;
- binaire SHA-256 `9bbb7a499d77d4bd77e12554e915064815fbfa10b2c020e9763f93621f1f5446` sur les deux runs.

Les 21 cas de processus et la septième mutation appartiennent à #114, pas à #108. Ces preuves ne valent pas exécution des 199 références QA du produit, ni race test, ni validation IPC/DB/tenant, ni reproductibilité générale d'une release.

### Conservation

| Run | Artifact ID | SHA-256 annoncé par Actions | Expiration UTC |
|---|---|---|---|
| 40613003105 | 12783921150 | `5d03a3c1a635b66071b5cee986cbf17fa3a6f6b5455aa57a16f40b834a661887` | 2026-11-27 01:09:19 |
| 40613008116 | 12783464620 | `6c13cddbb73b7b4d85f2c70cb2917713a2a3297a551ff7f398b429dafb70702d` | 2026-11-27 01:08:57 |

Les métadonnées Actions et les logs ont été relus ; les ZIP n'ont pas été téléchargés ni recalculés pour ce dossier. Le snapshot et le document de correction conservent les faits et empreintes après expiration, pas les ZIP eux-mêmes.

## Avis déjà présents — tous E2

| Référence | SHA concerné | Conclusion rapportée / limite |
|---|---|---|
| [Engineering 5676455437](https://github.com/yyogas/wcp360/pull/108#pullrequestreview-5676455437) | `281ccaa8694250fd06410f329dd79240096b82f1` | CHANGES REQUESTED sur l'ancien candidat ; historique conservé |
| [Engineering 5676584304](https://github.com/yyogas/wcp360/pull/108#pullrequestreview-5676584304) | candidat actuel | APPROVED pour fondation inerte ; E2 même compte, état GitHub COMMENTED |
| [DevOps 6457999878](https://github.com/yyogas/wcp360/pull/108#issuecomment-6457999878) | candidat actuel | APPROVED FOR SELECTED SLICE ; E2 même compte |
| [Security 5676601072](https://github.com/yyogas/wcp360/pull/108#pullrequestreview-5676601072) | candidat actuel | APPROVED FOR SELECTED SLICE ; SECURITY EXCEPTION = NONE ; E2 même compte |

Tous ces avis proviennent de `yyogas`. Ils ne démontrent pas E3. Le message [6492232481](https://github.com/yyogas/wcp360/pull/108#issuecomment-6492232481) confirme la présentation du candidat inchangé à une revue indépendante. Certaines synthèses anciennes de #13 et du document de correction précèdent l'avis Security : les références exactes ci-dessus donnent l'état ultérieur sans réécrire l'historique.

## Travail demandé au relecteur indépendant

Cases de travail **non remplies** ; ce sont des questions de revue, pas des tests canoniques nouvellement attribués.

- [ ] Confirmer son identité, son indépendance de l'auteur/du processus ayant produit le code et son accès au SHA exact ; lire l'autorisation et les limites.
- [ ] Vérifier la surface CLI finie, le rejet sans écho, les deux flux de sortie, la troncature et les codes propagés par main. Vérifier la pertinence des tests, pas seulement leur couverture.
- [ ] Vérifier imports de production, absence de dépendance runtime tierce, chemins/config/env acceptés, absence de socket, DB, privilège ou fallback d'exécution.
- [ ] Lire workflow et scripts : pins, intégrité, restore, séparation bootstrap/source non fiable, identités, montages, absence de secrets et de socket hôte dans les tests.
- [ ] Examiner le périmètre réel du filtrage scanner : DOCKER-USER et INPUT, contrôles positifs, probes négatives, compteurs, DNS, IPv6 et échec fermé en cas de dérive.
- [ ] Examiner la limite HTTPS IP/port plutôt que hostname et l'acceptation strictement limitée des propriétaires ; ne pas généraliser cette acceptation.
- [ ] Vérifier lock du scanner, versions/h1 transitifs, pseudo-version telemetry et disposition des propriétaires ; comparer les preuves retenues au source.
- [ ] Vérifier les 13 gates, les six rejets intentionnels, les résultats réels, la distinction entre échec attendu et produit, et les limites des preuves de reproductibilité.
- [ ] Examiner les anciennes corrections et signaler toute réserve encore pertinente sans transformer E2 en E3.
- [ ] Consigner verdict, constats, corrections requises, dépendances non examinées et SHA final dans le modèle ci-dessous.

Pour reproduire, utiliser l'environnement isolé exact du workflow au SHA relu. Dans le builder canonique non-root et hors réseau : `sh SOURCE/daemon/tools/check.sh`, puis `sh SOURCE/daemon/tools/check-negative.sh`. Le scan utilise son conteneur et son réseau séparés conformément au workflow ; ne pas exécuter le job comme un script hôte privilégié. Le relecteur conserve run/job/attempt, commandes, environnement et résultats réellement obtenus, y compris les échecs.

## Retour attendu et ordre d'intégration

Utiliser [le modèle de relevé E3](SC-M01-E3-REVIEW-RECORD-TEMPLATE.md), une entrée explicite pour #108 et son SHA. Un relecteur réellement indépendant peut examiner les deux PR, avec deux conclusions séparées.

**État actuel : E3 MISSING ; MERGE HOLD ; CODE-BASELINE NONE ; SC-M02 NOT AUTHORIZED.**

Ne pas merger #114 dans la branche #108 avant d'avoir traité la revue du SHA #108 : cela modifierait le candidat et les avis SHA-bound ne se transféreraient pas automatiquement. Après E3, corrections et vérification, préparer l'intégration #108 dans le main réellement retenu ; rafraîchir E1 et obtenir la revalidation de revue requise si base/candidat changent. L'absence de protection main n'autorise aucun contournement ; voir le [handoff MASTER](../handoffs/SC-M01-MASTER-NEXT-SLICE-HANDOFF-2026-10-30.md).
