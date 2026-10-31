# AUDIT-WCP-0100 — WCP360 : aptitude réelle au développement

Date : 2026-10-01. Statut : **REVIEW — constats documentés, décisions propriétaires requises**.

Dépôt : `yyogas/wcp360` (privé). Référence examinée : `aab5033f09a9b9d459d5c723344d7629306fd5ae`, arbre `ba9b7608fea3bdf26f675d821dadfc8889b8b3eb`.

## 1. Verdict et limites

**Les preuves disponibles ne permettent pas un GO d’implémentation global.** Le dépôt possède une base documentaire importante et des invariants de sécurité utiles. Cependant, le blocage ne se réduit pas à importer trois fichiers : certains contrats déjà déclarés « source-ready » ne permettent pas une implémentation déterministe sans choix supplémentaires.

`5/8` décrit la présence des cinq familles canoniques attendues dans `specifications/`, pas une couverture fonctionnelle, une conformité démontrée ni un pourcentage d’avancement du produit. Les mentions `FINAL APPROVED` des exports sont des déclarations documentaires ; cet audit ne révoque pas les décisions historiques, mais ne les transforme pas en preuve de complétude.

Périmètre : fichiers de gouvernance, inventaire des arborescences, exports API/IPC, Permission Registry, exigences Security/DevOps, préparation QA, configuration GitHub observable et reproduction locale du `.gitignore`. Les extraits pertinents ont été lus à la référence ci-dessus. Le corpus de plus de 1 000 Markdown n’a pas été rescanné. Le miroir de 147 fichiers n’a pas fait l’objet d’une nouvelle certification ligne par ligne ni d’un scan exhaustif des secrets.

Aucun service WCP360 n’a été exécuté ; aucun pentest, test de charge, test de restauration ou scan de dépendances installées n’a été réalisé. `src/` contient un README, pas une application. Les scénarios de sécurité décrits plus bas sont des risques d’implémentation, pas des compromissions démontrées.

Une seule reproduction technique a été exécutée : l’application des règles Git aux sept chemins du fichier `gitignore-reproduction.json`. Ce contrôle local n’est pas un résultat PASS de la future suite QA WCP360.

## 2. Ce qui existe réellement et mérite d’être conservé

Le projet distingue explicitement WCP-USER et WCP-ADMIN, identités Unix et sessions comprises. L’architecture privilégiée impose `backend → wcpd non-root → IPC borné → wcp-privd root minimal`. Les commandes arbitraires, signaux arbitraires, proxies sudo/systemctl génériques et chemins privilégiés libres sont interdits.

Les exports définissent la séparation des identifiants typés, un registre de 82 permissions, un inventaire IPC de 47 opérations, la distinction ownership/membership/delegation et la frontière entre PostgreSQL de contrôle et bases hébergées. Ils décrivent aussi l’intention durable avant effet externe, UNKNOWN_OUTCOME, la réconciliation et l’absence de retry destructif aveugle. Ce sont de bons invariants, à conserver, pas une démonstration de leur implémentation.

Les entrées canoniques présentes sont API, IPC, Permissions, Security et DevOps. Les exports DB, QA final et Engineering ne sont pas présents dans `specifications/`. Les documents `governance/handoffs/*INPUT-PACK.md` et le draft QA déclarent correctement qu’ils ne remplacent pas ces autorités.

Sources : [arbre specifications][SPEC-TREE], [état projet][STATUS], [API][API], [IPC][IPC], [Security][SEC], [DevOps][OPS], [arbre src][SRC-TREE].

## 3. Registre des constats

Tous les constats ci-dessous sont **OPEN**. « Élevée » qualifie l’impact potentiel d’une mauvaise implémentation ou d’une mauvaise gouvernance ; ce n’est pas un classement CVSS d’une faille exploitée. La priorité doit être appliquée au composant effectivement activé, sans exiger prématurément tous les détails d’un module futur.

### AUDIT-WCP-0101 — Disponibilité confondue avec aptitude à l’implémentation

**Sévérité : élevée. Propriétaires : MASTER + Audit. Avant : reprise SC-M01.**

Preuve : BM-0001 affiche `CRITICAL BLOCKERS = NONE` et `IMPLEMENTATION-CRITICAL TBD = NONE`, alors que PROJECT-STATUS exige encore trois exports. Les cinq fichiers présents sont comptés « PRESENT AND SOURCE-CODE CONSUMABLE » malgré les ambiguïtés décrites ci-dessous.

Impact : un statut administratif peut être interprété comme une permission d’inventer les règles manquantes. Une version attribuée ne prouve ni la présence, ni la cohérence, ni la vérification du contenu.

Correction proposée : séparer présence, provenance/approbation, complétude contractuelle, cohérence inter-domaines, couverture QA et vérification d’implémentation. BM-0001 reste la référence historique ; un état courant explicite doit porter les écarts et les gates ouverts, sans antidater ni réécrire une approbation.

Preuve de clôture : registre courant rattaché au commit audité, chaque statut soutenu par un fichier, une revue ou un résultat. Une huitième pièce jointe ne ferme pas les divergences sémantiques.

Sources : [BM-0001][BM], [PROJECT-STATUS][STATUS].

### AUDIT-WCP-0102 — Exports propriétaires DB, QA et Engineering absents

**Sévérité : élevée. Propriétaires : Database, QA, Engineering. Avant : composants dépendants de SC-M01.**

Preuve : l’arbre `specifications/` ne contient aucun export dans ces trois domaines. Le pack DB confirme lui-même qu’il n’est pas l’autorité du modèle. Le draft QA se dit `DRAFT — NOT TESTS / QA AUTHORITY`. Les déclarations d’approbation antérieures ne remplacent pas les corps de fichiers et leur provenance.

Impact : impossibilité de vérifier les colonnes, contraintes tenant, FK composites, nullabilité, suppressions, transactions, migrations futures, règles d’ingénierie et assertions QA exactes.

Correction : traiter les issues existantes #6, #7 et #9, sans doublons. Importer le contenu propriétaire exact et ses preuves. Si un contenu final ne peut pas être retrouvé, signaler explicitement le manque de preuve et demander une republication ciblée au propriétaire ; ne pas reconstruire une prétendue v1.0 depuis un résumé MASTER.

Clôture : fichiers complets, références/versions, chemins de consommation, validation propriétaire et comparaison aux contrats connexes. Aucun DDL ni migration n’est créé par cet audit.

Sources : [arbre specifications][SPEC-TREE], [pack DB][DB-PACK], [draft QA][QA-DRAFT].

### AUDIT-WCP-0103 — API : catalogue détaillé mais contrat de transport incomplet

**Sévérité : élevée. Propriétaire : API / IPC / Contracts. Avant : endpoints concernés.**

Preuve : la table API donne des identifiants, permissions, listes de paramètres et noms de réponses, sans colonnes méthode HTTP/route. Des réponses comme `ApplicationView/Collection`, `JobAccepted` et `Mutation` sont référencées sans définition complète dans l’export examiné. Les profils `AU0..AU5`, `S-T`, `S-P`, `S-X` apparaissent sans dictionnaire complet dans cet export. Les champs `operation` regroupent lecture et mutation sans union discriminée détaillée.

Impact : deux implémentations peuvent diverger sur les URLs, méthodes, projections, erreurs HTTP, paramètres obligatoires, pagination et droits au niveau des champs tout en prétendant respecter v1.0.

Correction : publier pour chaque endpoint activé sa méthode/route, schémas et types, bornes, enums, nullabilité, champs inconnus, projections autorisées, erreurs/status/priorités, pagination et dépendances normatives. Cela peut rester en Markdown : l’absence d’OpenAPI n’est pas, à elle seule, le défaut.

Clôture : une fiche permet d’écrire sans invention les tests positif, validation négative, refus d’autorisation et sérialisation. Les profils et DTO référencés ont tous une définition ou un lien versionné.

Sources : [API §§2–5,8–14][API].

### AUDIT-WCP-0104 — Divergences d’action et bindings privilégiés non fermés

**Sévérité : élevée. Propriétaires : API/IPC + Security ; arbitrage Audit. Avant : dispatcher/autorisation.**

Exemples observés :

| Chemin | Action API | Action requise dans Security | Autre libellé IPC |
|---|---|---|---|
| Runtime applicatif | `applications.runtime.update` | `applications.runtime.manage` pour lock/unlock | `linux_user.lock` / `linux_user.unlock` |
| Grants de base hébergée | `databases.grants.set` | `databases.grants.manage` | `database_grant.apply` |
| Reload service | `servers.service.reload` | `servers.services.manage` | `servers.service.reload` |
| Firewall | `network.firewall.apply` | `network.firewall.manage` | `network.firewall.apply` |

Ces différences pourraient représenter des niveaux distincts — action d’origine et sous-opération technique — mais le mapping normatif n’est pas suffisamment explicite pour les traiter comme synonymes. Security exige de refuser les bindings inconnus. Plusieurs cellules restent textuelles : `approved app/domain/admin binding`, `same approved parent action`, ou des alternatives `OR` sans liste exhaustive des parents et contraintes.

Impact : soit refus de workflows légitimes, soit alias permissifs inventés lors du développement. Le fait que chaque identifiant soit individuellement plausible ne garantit pas la sécurité de la composition.

Correction : publier les tuples autorisés `(surface, permission_id, origin_action_id, technical_action si nécessaire, ipc_operation_id, resource_type, tenant_scope, contraintes, approbation, audit)`. Toute séparation d’action d’origine/technique doit être une décision propriétaire explicite, pas une transformation automatique de cet audit.

Clôture : un cas ALLOW exact et des cas DENY pour chaque combinaison non autorisée ; aucun wildcard fonctionnel ni alias implicite. Clarifier aussi l’événement firewall `firewall.policy.applied` côté API et `network.firewall.applied` côté Security, sans supposer qu’ils représentent le même stade.

Sources : [API registre et §6][API], [IPC §6][IPC], [Security §§7–9][SEC].

### AUDIT-WCP-0105 — Contrat backend/worker → wcpd insuffisamment publié

**Sévérité : élevée. Propriétaires : API/IPC + Security + DevOps. Avant : IPC de contrôle.**

Preuve : DevOps §5.1 décrit la frontière logique et `wcp360-control`. L’export IPC détaillé porte sur IPC-B. Le worker, sans groupe supplémentaire par défaut, doit emprunter la voie de contrôle pour produire un effet privilégié. Les modalités précises d’accès worker et de transport de ce premier segment ne sont pas définies dans ces exports.

L’enveloppe IPC-B utilise `authorization_context_ref`, tandis que Security décrit `authorization_context_id` et un contexte de décision. Créateur, résolveur, stockage, immutabilité, portée, expiration, révocation, liaison au payload et protection contre le rejeu doivent être explicitement reliés. Ces noms ne sont pas nécessairement contradictoires ; leur équivalence n’est simplement pas prouvée.

Impact : risque de transformer un accès au socket ou une référence fournie par l’appelant en preuve d’autorisation. SO_PEERCRED renseigne les credentials du pair local au moment de l’établissement de la connexion ; il ne prouve pas, à lui seul, l’autorisation métier d’un utilisateur ou tenant.

Clôture : contrat complet du segment de contrôle, authenticité des deux extrémités applicable, chemin worker explicite, règles d’émission/résolution du contexte et tests de substitution de tenant, action, payload, contexte révoqué et pair non autorisé. Préciser les garanties attendues du contrôle UID/service/cgroup et leurs limites, sans prétendre résister à une compromission root globale.

Sources : [DevOps §§3,5,7][OPS], [IPC §§2–4][IPC], [Security §7][SEC], [Linux unix(7)][UNIX].

### AUDIT-WCP-0106 — Idempotence : invariants annoncés, protocole persistant incomplet

**Sévérité : élevée. Propriétaires : API/IPC + Database + Security. Avant : mutation/job.**

Preuve : API exige `Idempotency-Key` et expose souvent `idempotency_key` dans les paramètres ; aucune priorité ou règle de divergence n’est définie dans l’export. `API-WCP-0701` annonce une clé en mutation, mais son schéma ne la contient pas. Des lignes IPC exigent `key-required` sans champ idempotence dans l’enveloppe ni définition explicite de sa résolution via job/contexte.

Impact : replay cross-tenant, doubles effets après crash, réponse relue après révocation, collisions ou requêtes concurrentes interprétées différemment.

Correction : spécifier source de la clé, portée acteur/surface/tenant/action, canonicalisation de la requête, transaction d’acquisition, requête en cours, stockage du résultat, revalidation à la relecture, expiration et horizon de non-réutilisation sûr. Une rétention indispensable à la déduplication est un paramètre de correction, pas du simple tuning.

Clôture : tests même clé/même requête, même clé/requête différente, concurrence, autre tenant, révocation, crash avant/après effet et expiration. Aucun chiffre de rétention n’est inventé ici.

Sources : [API §4 et API-WCP-0701][API], [IPC §§3,6][IPC], [pack DB][DB-PACK].

### AUDIT-WCP-0107 — Timeouts IPC : phases et signification de deadline manquantes

**Sévérité : élevée. Propriétaires : API/IPC + DevOps + QA. Avant : client/serveur IPC.**

Preuve : I/O = 5 s, délai d’opération par défaut = 30 s, maximum = 900 s ; certaines opérations utilisent effectivement 900 s. L’enveloppe contient `deadline_ms` sans définition claire relative/absolue, horloge de référence, origine, borne ni traitement après dispatch.

Ces chiffres ne sont pas intrinsèquement incompatibles : un timeout d’inactivité par phase et un timeout d’exécution peuvent coexister. Ce qui manque est le protocole qui les articule — attente synchrone, ACK, ticket asynchrone, progression ou autre mécanisme approuvé.

Impact : fermeture prématurée de connexions, multiplication des UNKNOWN_OUTCOME, tentatives de reprise dangereuses, comportement différent PHP/Go.

Clôture : diagramme temporel contractuel et tests opération >5 s, lecture partielle, pair silencieux, coupure post-dispatch, deadline déjà expirée et annulation tardive. Ne pas résoudre en augmentant arbitrairement le timeout réseau.

Source : [IPC §§2,3,5,6][IPC].

### AUDIT-WCP-0108 — Durabilité, concurrence et récupération non assez démontrées

**Sévérité : élevée. Propriétaires : Database + API/IPC + Security + SRE/DevOps. Avant : effet externe réel.**

Preuve : les principes durable intent → dispatch → observe → durable result sont présents. Le modèle DB final est absent. Les scopes de concurrence IPC sont nommés, sans contrat persistant complet de claim, lease, expiration, fencing ou résultat tardif dans les exports examinés.

Impact : un worker dont le droit d’exécution a expiré pourrait encore agir ; un succès système peut rester sans preuve durable ; rollback/compensation peuvent être confondus après un crash.

Correction : définir la transaction job/outbox/audit, l’identité d’opération stable, la revalidation de sécurité au point convenu, la reprise après perte d’ACK, la prévention des effets concurrents et la destination durable quand l’audit final est indisponible. Définir aussi comportement disque plein/DB indisponible, limites de buffer, escalade et récupération au reboot.

Clôture : matrice de pannes injectées avant intention, après commit, avant effet, après effet et avant résultat final ; invariants d’absence de perte silencieuse et de répétition dangereuse vérifiés. La réconciliation n’est pas un retry renommé.

Sources : [Security §11][SEC], [API §§6–7][API], [IPC §§5–6][IPC], [pack DB][DB-PACK].

### AUDIT-WCP-0109 — Protection du dépôt et CI absentes au point observé

**Sévérité : élevée. Propriétaires : DevOps + Engineering. Avant : premières contributions exécutables.**

Preuve directe : l’API branches/main retourne `protected=false`, `protection.enabled=false`, `enforcement_level=off`, `contexts=[]`. L’arbre `.github/` ne contient pas de workflow CI. La lecture de la collection workflows a été refusée par le connecteur ; la conclusion porte donc sur les fichiers présents et la réponse de branche, pas sur toutes les politiques d’organisation invisibles.

Impact : les règles écrites dans les README ne bloquent pas techniquement une fusion ou un push non contrôlé. Un dépôt privé n’apporte pas à lui seul cette garantie.

Correction proposée : protections/rulesets adaptés au plan GitHub, PR obligatoire, contrôles requis, contrôle des bypass et des push forcés. D’abord CI documentaire (liens, inventaire, formats/identifiants, secrets et hashes), puis lint/build/tests lors de SC-M01. Ne pas imposer un check inexistant qui bloquerait tout.

Clôture : lecture de configuration prouvant l’enforcement et PR négatives montrant qu’une violation est réellement rejetée. Aucun paramètre GitHub n’a été modifié par cet audit.

Sources : [branche main][BRANCH], [arbre .github][GH-TREE], [GitHub protections][PROTECTION].

### AUDIT-WCP-0110 — Revue indépendante non démontrée

**Sévérité : élevée. Propriétaires : Engineering + Audit + MASTER. Avant : validation de code.**

Preuve : CODEOWNERS affecte tous les chemins à `@yyogas`. L’endpoint reviews de la PR #10 renvoie une liste vide. Cela prouve l’absence de revue GitHub formelle sur cet échantillon, pas l’absence universelle de tout avis historique.

Impact : création de PR, lecture par son auteur et merge sous le même compte peuvent donner l’apparence d’une revue indépendante. GitHub ne permet pas à l’auteur d’approuver sa propre PR. CODEOWNERS ne suffit pas sans exigence de revue configurée.

Correction : distinguer auteur, contrôle automatisé, revue technique réellement séparée et approbation de merge ; rattacher l’avis au SHA exact. Des fiches de workstreams ne démontrent pas que des sous-agents autonomes ont été lancés. Aucune équipe fictive ne doit être déclarée reviewer.

Clôture : preuve de revue sur le dernier SHA, exigences de protection compatibles avec les identités disponibles, et politique explicite pour le mode mono-mainteneur. Ne pas contourner les contrôles en simulant un second utilisateur.

Sources : [CODEOWNERS][OWNERS], [PR10 reviews][REVIEWS], [documentation GitHub des revues][GH-REVIEWS].

### AUDIT-WCP-0111 — `.gitignore` masque des sources et documents légitimes

**Sévérité : moyenne ; impact reproductibilité élevé. Propriétaires : Engineering + DevOps. Avant : import de code.**

La copie locale du `.gitignore` a été vérifiée contre son blob Git exact `479678cad0d0a624bb0dcc3ca394395b339fe4ea`. Une exécution réelle de `git check-ignore --no-index -v --stdin` produit :

| Règle | Chemin illustratif effectivement ignoré |
|---|---|
| `*.sql` | `database/migrations/001_initial.sql` |
| `*.sql` | `tests/fixtures/minimal.sql` |
| `runtime/` | `src/runtime/config.go` |
| `build/` | `packaging/build/README.md` |
| `secrets*` | `documentation/modules/secrets.md` |

Les contrôles `.env` et `config/local/production.ini` sont aussi ignorés, comme souhaité. Ces chemins sont des sondes, pas des fichiers WCP360 implémentés. Aucun DDL n’a été créé.

Correction : distinguer sorties générées et dossiers source, limiter les exclusions ambiguës et maintenir l’exclusion réelle des secrets/dumps. Valider les emplacements avec Engineering avant modification ; `.gitignore` n’est pas un scanner de secrets.

Clôture : mêmes sondes autorisées pour sources fictives non sensibles, secrets toujours exclus, vérification des nouveaux fichiers de build/runtime et tests de non-régression des règles.

Sources : [.gitignore][IGNORE], `gitignore-reproduction.json` dans ce dossier.

### AUDIT-WCP-0112 — Miroir documentaire ≠ livraison complète de la baseline

**Sévérité : moyenne. Propriétaires : MASTER + Documentation/Design pour leurs références. Avant : affirmation de complétude.**

Preuve : PROJECT-STATUS indique 147 Markdown matérialisés et reconnaît séparément l’absence de l’archive physique DOC v4.0.21.23, suivie par #16. Le miroir inclut des chemins de staging et de l’historique ; son nombre ne mesure pas le nombre d’exigences uniques satisfaites. BM-0001 donne des versions sans verrou complet chemin + digest + dépendances + preuve d’approbation pour chaque baseline.

Correction : conserver un miroir figé comme archive de preuve, un point d’entrée vers les fichiers canoniques, et un manifest de consommation lié à des objets précis. Réutiliser les manifests déjà présents, ne pas créer un nouveau compteur manuel à chaque passe. Distinguer import du corpus, provenance, cohérence et validation.

Clôture : import exact du corpus via #16 lorsqu’il est disponible, chemins conservés, digest vérifié ; aucun remplacement par une ancienne version. Ne pas imposer un rescan général à la discussion Documentation pour une simple publication.

Sources : [PROJECT-STATUS §2][STATUS], [inventaire][INVENTORY], [BM-0001][BM].

### AUDIT-WCP-0113 — Plateforme choisie, chaîne de construction non verrouillée

**Sévérité : moyenne à élevée selon le composant. Propriétaires : DevOps + Engineering + Security. Avant : build/installation concernés.**

Preuve : DevOps fixe Debian 13 amd64 et demande une toolchain, des dépendances et une provenance déclarées, mais ne fournit pas leurs versions exactes dans l’export. Le ruleset Engineering est absent. Les répertoires `/etc/wcp360` et `/var/lib/wcp360` sont décrits par principe ; les permissions par consommateur, le bootstrap/rotation et la récupération des secrets restent à rendre directement vérifiables pour les composants activés.

Impact : dépendances choisies ad hoc, variations de build, service root minimal seulement de nom, bootstrap non reproductible ou restauration impossible.

Correction : registre des runtimes/outils et sources de paquets, politiques de versions/locks, matrice service → identité → fichiers → droits → capacités → chemins inscriptibles, dépendances de démarrage. Pour la release : mécanisme de signature, racine de confiance, compatibilité migration/binaire, backup pré-upgrade et restauration validée. Une cible OS sélectionnée n’est pas une certification déjà obtenue.

Clôture : construction reproductible à partir d’entrées déclarées, vérification sous l’OS cible et tests d’installation/récupération au jalon concerné. Aucun numéro de runtime, package, SLO ou réglage systemd n’est inventé ici.

Sources : [DevOps §§3–13][OPS], [arbre specifications][SPEC-TREE].

### AUDIT-WCP-0114 — Traçabilité QA à transformer en assertions vérifiables

**Sévérité : élevée. Propriétaire : Tests / QA. Avant : validation de chaque composant.**

Preuve : les exports font référence à des TEST-API/TEST-IPC et catalogues négatifs. Le draft préparatoire QA affiche 86 références API et porte explicitement le statut non autoritaire. Le test canonique final n’existe pas dans `specifications/`.

Impact : compter des identifiants ou des familles de tests peut être confondu avec la capacité de vérifier un comportement déterminé. Les divergences de mapping peuvent ne jamais être détectées si les fixtures copient les mêmes erreurs.

Correction : une ligne par exigence/contrat avec préconditions, entrée, résultat/erreur attendu, effet autorisé/interdit, preuve audit, état DB et scénario de récupération. Lier les cas aux définitions propriétaires, pas seulement aux titres.

Clôture : distinguer PLANNED, READY, BLOCKED, puis résultats réellement exécutés sur un SHA/environnement. Exécuter des tests avant création d’une CODE-BASELINE approuvée est normal dès qu’un candidat existe : `CODE-BASELINE = NONE` n’interdit pas de tester le candidat, seulement de prétendre l’avoir validé sans exécution.

Sources : [draft QA][QA-DRAFT], [API §12][API], [IPC §9][IPC], [Security §14][SEC].

### AUDIT-WCP-0115 — Hiérarchie par dossiers trop générale pour les conflits métiers

**Sévérité : moyenne. Propriétaires : MASTER + Audit + propriétaires concernés. Avant : arbitrage d’une divergence.**

Preuve : AUTHORITY-PRECEDENCE place le manifest au-dessus des exports, puis governance, puis Design. C’est utile pour isoler le miroir, mais insuffisant pour décider d’un conflit technique entre deux propriétaires ou pour faire primer une mention de métadonnées sur une règle de sécurité.

Correction proposée : conserver la sélection des versions par manifest ; arbitrer le contenu par domaine de compétence et décisions explicites. Un résumé MASTER ne modifie pas un contrat Security/DB/API. Une divergence entre owners reçoit un WCP360 CONFLICT et une décision traçable ; l’ordre lexical, numérique ou de dossier ne suffit pas.

Clôture : règle de gouvernance ratifiée, registre de dépendances et décisions datées. Aucune correction silencieuse d’AUTHORITY-PRECEDENCE n’est appliquée par ce rapport.

Sources : [AUTHORITY-PRECEDENCE][AUTHORITY], [gouvernance][GOV].

### AUDIT-WCP-0116 — Découpage du développement à rendre opératoire

**Sévérité : moyenne. Propriétaires : Modules + Engineering + MASTER + QA. Avant : élargissement fonctionnel.**

Preuve : les versions Module/Observability/Performance sont citées dans BM-0001 ; leurs exports complets ne figurent pas dans l’arbre `specifications/` examiné. Le gate de huit artefacts est une liste de prérequis de fondation, pas une preuve que tous les modules ou exigences opérationnelles du produit sont prêts.

Correction : définir le périmètre exact SC-M01, ses fichiers, dépendances, non-objectifs et critères de sortie. Les choix propres aux modules DNS, mail, backup, HA, billing, etc. doivent être fermés avant l’activation de ces modules, pas mélangés à la première fondation.

Clôture : matrice module → règles → données → contrats → UI → observabilité → tests → composant et ordre de livraison. Ni partitionnement avancé, ni benchmarks futurs, ni tous les modules complets ne sont exigés pour chaque tâche documentaire indépendante.

Sources : [BM-0001][BM], [arbre specifications][SPEC-TREE], [PROJECT-STATUS][STATUS].

## 4. Scénarios adverses à inscrire dans la validation

Ces scénarios sont des tests proposés, non exécutés :

1. Modifier tenant/resource/action/payload en conservant une ancienne référence d’autorisation : refus, aucun effet et preuve corrélée.
2. Révoquer une délégation ou permission pendant qu’un job attend : appliquer la règle d’autorisation au moment d’exécution convenu, pas uniquement à l’enqueue.
3. Soumettre simultanément la même mutation avec une clé identique : un seul résultat logique et aucune double exécution non sûre.
4. Provoquer une perte d’ACK après effet système : UNKNOWN_OUTCOME et observation/réconciliation, pas répétition aveugle.
5. Redémarrer un worker pendant un travail : le worker périmé ne doit pas continuer des effets concurrents avec le nouveau détenteur.
6. Opération de 30–900 s, socket silencieux au-delà de 5 s : comportement contractuel prévisible, sans inventer ACK/heartbeat.
7. DB/audit indisponible après effet, puis disque plein : conservation ou escalade déterminée ; jamais succès fictif ni rollback fictif.
8. Présenter un autre pair Unix, une référence de service invalide ou une instance de processus périmée : refus sans mécanisme générique de secours.
9. Utiliser une session USER sur ADMIN, un token machine pour une action nécessitant MFA fraîche ou un contexte privilégié expiré : comportement propriétaire explicite et tests négatifs.
10. Restaurer ancien binaire avec schéma plus récent ou retirer le package : compatibilité contrôlée, pas de destruction implicite des données hébergées.

## 5. Structure cible proposée, sans déplacer les autorités

```text
README.md                          point d’entrée
manifests/                         versions, chemins, digests, décisions de gel
specifications/                    contrats normatifs par propriétaire
contracts/                         projections/outils de contrat approuvés ultérieurement
src/                               code seulement après gate
  wcp-user/                        surface USER
  wcp-admin/                       surface ADMIN
  backend/                         logique applicative sans privilège root
  wcpd/                            orchestration non-root
  wcp-privd/                       primitives privilégiées bornées
tests/                             unit, contract, integration, security, e2e, recovery
packaging/                         installation, build, release après contrat DevOps
.github/                           CI et contrôles du dépôt
governance/                        état courant, décisions, handoffs et audits
documentation/                     navigation et explications
  library-mirror/                  snapshot non autoritaire
```

Ce layout est une proposition à valider par Engineering/DevOps. Il ne choisit aucun framework et ne doit pas être créé comme un skeleton implémenté. Éviter les copies éditables concurrentes : un contrat normatif a une source ; les DTO, schémas ou vues dérivés doivent avoir une origine et un contrôle de dérive. Un document de statut central doit alimenter les résumés au lieu de nombreux états `5/8` maintenus séparément.

## 6. Ordre de correction proposé

**Lot A — Contrôle du dépôt et sincérité des preuves.** Qualifier 5/8 comme présence, enregistrer ces écarts, traiter protections/CI/revue et `.gitignore`. Le travail documentaire et ses contrôles peuvent avancer sans daemon applicatif.

**Lot B — Contrats indispensables à la fondation.** Résoudre #6/#7/#9 ; fermer les mappings API/Security/IPC, le segment de contrôle, les schémas, idempotence, deadlines et durabilité nécessaires au périmètre retenu. Demander des deltas ciblés aux owners, pas un nouveau tour de scans généraux.

**Lot C — Gel de consommation.** Figer chemins/digests/dépendances et avis sur le SHA exact. Une revue croisée doit démontrer qu’aucun choix de sécurité ou de données n’est abandonné à SOURCE CODE. Confirmer la toolchain et le plan QA applicable.

**Lot D — SC-M01 contrôlé.** Après autorisation, construire une fondation étroite, d’abord sans effets privilégiés réels ; exécuter réellement ses tests, publier les résultats et faire revoir le dernier commit. Les modules fonctionnels et SC-M02 restent hors périmètre jusqu’à leur décision distincte.

Critère de passage : pas « tous les documents ont un numéro », mais « chaque comportement indispensable a un contrat non ambigu et une preuve de validation possible ». Les éléments de performance/release non nécessaires à la fondation restent suivis à leur bon jalon.

## 7. État proposé après audit

```text
DOC-BASELINE = v4.0.21.23 — inchangée
DESIGN-BASELINE = V2.5.3.36.4 — inchangée
WCP360-BM-0001 = référence historique conservée
CANONICAL ARTIFACT FAMILIES PRESENT = 5 / 8
IMPLEMENTATION READINESS = NON DÉMONTRÉE ; ÉCARTS DOCUMENTÉS
SC-M01 = PAUSED — TARGETED EVIDENCE + CONTRACT CLARIFICATIONS
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
APPLICATION TEST EXECUTIONS DURING THIS AUDIT = 0
LOCAL GITIGNORE RULE REPRODUCTION = EXECUTED
```

Ce rapport ne change pas les baselines, n’approuve pas de correction propriétaire, ne ferme aucun blocker et ne certifie pas la sécurité d’un logiciel inexistant. Il fournit les changements à instruire et les preuves nécessaires à leur clôture.

## 8. Sources figées et vérifications externes

Les liens des fichiers pointent sur le commit examiné. Les réponses d’API de branche/revues sont des observations au moment de l’audit. Les références externes ne redéfinissent pas WCP360 ; elles vérifient uniquement le comportement de GitHub et SO_PEERCRED.

[BM]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/manifests/WCP360-BM-0001.md
[STATUS]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/governance/PROJECT-STATUS.md
[API]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
[IPC]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md
[SEC]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
[OPS]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/specifications/devops/WCP360_SC-M01_DEVOPS_BASELINE_v1.0.md
[IGNORE]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/.gitignore
[OWNERS]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/.github/CODEOWNERS
[DB-PACK]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/governance/handoffs/SC-M01-DB-CANONICAL-INPUT-PACK.md
[QA-DRAFT]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/governance/proposals/SC-M01-QA-TEST-BASELINE-v1.0-DRAFT.md
[AUTHORITY]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/governance/AUTHORITY-PRECEDENCE.md
[GOV]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/governance/README.md
[INVENTORY]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/documentation/MATERIALIZED-MARKDOWN-INVENTORY.md
[SPEC-TREE]: https://api.github.com/repos/yyogas/wcp360/git/trees/02e11c5d2815c0289ec6744ee429ecc433448384?recursive=1
[SRC-TREE]: https://api.github.com/repos/yyogas/wcp360/git/trees/e5022b33a719f6b1a5966775e19d977a19f92154?recursive=1
[GH-TREE]: https://api.github.com/repos/yyogas/wcp360/git/trees/4b3ad1248664e491e9310776f931f5a2ea3c4995?recursive=1
[BRANCH]: https://api.github.com/repos/yyogas/wcp360/branches/main
[REVIEWS]: https://api.github.com/repos/yyogas/wcp360/pulls/10/reviews?per_page=100
[PROTECTION]: https://docs.github.com/en/repositories/configuring-branches-and-merges-in-your-repository/managing-protected-branches/about-protected-branches
[GH-REVIEWS]: https://docs.github.com/en/enterprise-cloud@latest/pull-requests/how-tos/review-pull-requests/reviewing-proposed-changes-in-a-pull-request?tool=webui
[UNIX]: https://www.man7.org/linux/man-pages/man7/unix.7.html
