# Intégration — proposition à valider

Statut : DRAFT. Pas d’intégration au repository officiel, pas d’approbation implicite par MASTER, Security ou API/IPC. Les baselines WCP360 rappelées dans le contexte ne sont pas remplacées par ce paquet ; aucune archive canonique n’a été réauditée ici.

## Dans une application Symfony

1. Ajouter le paquet local via un repository Composer `path`, ou le publier dans le registre privé approuvé. Aucun paquet public portant ce nom n’est présumé disponible.
2. Enregistrer `Wcp360\FileManager\FileManagerBundle::class` dans `config/bundles.php`.
3. Importer les routes attributs du répertoire `src/Controller/` du paquet.
4. Importer `resources/config/services.php` ; configurer explicitement les deux interfaces `IdentityProvider` et `FileBackend`.
5. Autoriser `ROLE_FILE_MANAGER` sur le module ; le provider retourne un tenant et un sujet authentifiés côté serveur et une permission d’écriture réellement autorisée.
6. Fournir Security, sessions et CSRF dans l’hôte. Aligner `/logout` et son token CSRF avec le firewall hôte, ou adapter le bouton fourni. Les URL de l’interface supposent un déploiement à la racine de l’origine.
7. Injecter un logger JSON dédié `file_manager` comme dans l’application autonome.

Ne pas injecter `LocalBackend` en environnement WCP360. Injecter temporairement `UnavailableWcpBackend` pour fermer l’accès aux fichiers tant que l’adaptateur de daemon n’est pas validé.

## Port de stockage fourni

```php
public function execute(Identity $identity, string $operation, array $input): array;
```

Contrat interne à cette extension, **pas un contrat IPC officiel WCP360**. Les opérations et réponses sont documentées dans `API.md`. L’adaptateur futur doit traduire ce port en opérations typées et autorisées, jamais relayer librement une chaîne d’opération au shell.
WCP-USER gère seulement les fichiers de son tenant autorisé. WCP-ADMIN possède un contexte et des autorisations distincts ; aucun accès administratif implicite par rôle utilisateur.

## Chaîne cible pour les opérations système

Symfony → autorisation et demande typée → job persistant/outbox → Messenger → worker → IPC authentifié → wcpd → exécuteur isolé tenant → résultat/audit.
Messenger ne fournit pas l’autorisation, le confinement ni l’idempotence à lui seul. Les chemins du payload restent relatifs ; le daemon résout la racine depuis une identité vérifiée. Aucun chemin absolu fourni par le navigateur et aucune action arbitraire.
Les contenus de `.env`, clés et autres fichiers ne doivent pas être déposés en clair dans les logs ou files de messages. Pour les gros transferts : jeton de staging borné, lié à l’opération et au tenant, stockage temporaire privé, expiration et nettoyage.

## Handoffs nécessaires avant activation

| Autorité | Décision demandée |
|---|---|
| MODULES | Fiche File Manager, périmètre, dépendances, droits, entitlements et fonctions différées |
| API / IPC / CONTRACTS | Actions typées, schémas, identité, versions, erreurs, deadlines, retries, cancellation et idempotence |
| SECURITY / HARDENING | Résolution descriptor-relative, isolation UID/mount, peer credentials, symlinks, hardlinks, limites et permissions |
| DATABASE / DATA MODEL | Jobs/outbox, révisions, corbeille, audit, rétention et nettoyage, si persistés dans PostgreSQL |
| TESTS / QA | Tests multi-tenant, courses TOCTOU, refus IPC, déconnexions, limites, archives et restauration |
| DEVOPS | Packaging extension publique, installation sous UID dédié, dépendances et rollback |
| Design System | Adaptation du shell, styles, navigation et composants à la baseline active |
| MASTER / Audit | Résolution des divergences et autorisation d’intégration |

Ces handoffs sont fournis dans l’archive ; ils n’ont pas été envoyés ni publiés dans d’autres discussions ou dans GitHub.

## ZIP et permissions : volontairement non actifs

L’extraction future exige protection Zip Slip, limites de taille décompressée/nombre/profondeur/ratio, refus des liens et chemins absolus, quotas, staging isolé, absence d’écrasement implicite, annulation et rollback définis. `chmod` doit avoir une liste de modes et une politique distincte excluant les bits privilégiés. Aucun handler factice ne prétend accomplir ces opérations.

## Distribution publique / services privés

Cette extension est candidate à la distribution publique. Aucun service de licences, signature, catalogue privé, secrets de distribution ou accès ROOT interne n’est inclus. L’autorité de build doit exporter sur allowlist les composants publics et vérifier l’absence de secrets et d’extensions privées avant tout ZIP stable officiel.

## Handoff additionnel 0.3.0 — non envoyé

Le port inclut maintenant les outils décrits dans PRO-TOOLS.md. Identity.canManage est false par défaut ; l’hôte doit traduire les droits canoniques vers des capacités par opération, pas simplement reprendre le rôle administrateur autonome. La route de partage publique de standalone/ShareController.php dépend explicitement du backend local ; un hôte WCP360 doit la remplacer par sa propre autorisation de téléchargement, son stockage temporaire et ses règles d’audit/révocation. Elle n’est pas auto-enregistrée par le bundle dans l’hôte.
Les codes d’erreur et schémas de cette alpha sont des propositions de module, pas des modifications des contrats API/IPC officiels. Pas de merge GitHub, d’activation wcpd ou de changement de baseline dans cette livraison.
