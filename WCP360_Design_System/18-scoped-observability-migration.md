# Journaux, échantillons de trafic et rapports — 30 octobre 2026

Cette passe raccorde les journaux USER et ROOT aux grilles, formulaires, dialogues et jobs communs. Les deux portails utilisent un seul contrôleur, avec des états et des sources distincts. Le shell et le langage visuel de V2.5.3.12 sont conservés.

## Centres, catégories et liens

| Route | Contenu | Propriétaire |
| --- | --- | --- |
| `site-analytics.html` | Échantillons de trafic par site, journaux, erreurs, rapports capturés et activité | USER / Operations / Site Analytics & Logs |
| `logs-diagnostics.html` | Événements système synthétiques, profils de diagnostic fixes, rapports et activité | ROOT / System / Logs & Diagnostics |
| `server-logs.html` | Redirection vers `logs-diagnostics.html#logs` | ROOT, route de compatibilité |
| `resource-diagnostics.html` | Diagnostics spécialisés de ressources, parcours existant conservé | ROOT / Infrastructure |

L’ancienne page System logs est consolidée dans Logs & Diagnostics ; les paramètres et fragments explicites sont conservés. Le paramètre historique `server` est accepté par le centre ROOT, en plus du paramètre canonique `node`.

Le lien « Website logs » du centre Sites envoyait auparavant vers `server-logs.html`, une surface ROOT. Il ouvre maintenant `site-analytics.html#logs`. Le formulaire Manage possède un lien vers les journaux du site précis avec `?site=<id>#logs`, à la place de son ancien bouton qui affichait seulement un toast. Les liens de repli de **31 pages USER** ont été corrigés de la même façon. Les liens ROOT restent dans leur portail.

Le libellé USER devient « Site Analytics & Logs », sans ajouter d’entrée concurrente. Les modules historiques `analytics` de `hosting-parity.js` et `diagnostics` de `root-control.js` ont été retirés. Les autres centres de ces scripts sont conservés.

## Données séparées et navigation

- Contrats métier : `assets/observability-schemas.js`.
- Contrôleur commun : `assets/observability-center.js`.
- Cache USER : `wcp360-account-observability-v1`.
- Cache ROOT : `wcp360-platform-observability-v1`.
- Les sites USER viennent de `website-model.js` ; une ligne explicitement affectée à un autre propriétaire est exclue.
- Les deux sources système proposées, `srv-par-01` et `srv-ams-01`, sont des identifiants d’échantillons, pas une découverte de la flotte.

Un identifiant de source inconnu ne sélectionne pas automatiquement une autre source. Les onglets, la sélection, les filtres et les détails utilisent les composants et liens communs. Les grilles sont détruites avant remplacement d’un onglet. Les relations ARIA et les commandes flèches/Home/End sont présentes ; leur fonctionnement natif reste à vérifier visuellement.

Les jobs sont rapprochés par identifiant exact et scope actif. Un job ROOT ne peut pas appliquer le résultat d’une opération USER. Une suppression, réaffectation ou modification du domaine sélectionné invalide une collecte ou un rapport encore en attente. La lecture du cache et les événements `storage` rafraîchissent les sources disponibles.

## Échantillons, fenêtres et indicateurs

Le centre USER commence avec 18 requêtes synthétiques pour trois sites. Le staging sans échantillon affiche « No sample ». Les indicateurs comptent uniquement les requêtes sélectionnées, leurs octets, leur proportion de 5xx et le p95 des latences disponibles par rang le plus proche. Il n’y a ni estimation de visiteurs uniques, ni extrapolation à un trafic réel. Une métrique sans observations reste indisponible ; une valeur absente n’est pas remplacée par zéro.

Les fenêtres de temps et de visibilité sont persistantes. USER peut choisir 1 h/24 h/7 j/14 j et une visibilité de 1/7/14 jours. ROOT dispose aussi de 30 jours. La plus petite fenêtre s’applique. Ce réglage filtre le cache et ne configure ni purge ni rétention d’un collecteur réel.

« Add demo sample » demande une confirmation et crée un job. Les entrées ne sont ajoutées qu’après sa fin réussie, une seule fois. Une annulation ou un échec ne modifie pas les événements. Un seul job de génération peut être actif par portail. Le cache est borné à 1 000 entrées, ordonnées par date, avec identifiants uniques ; les rapports déjà capturés restent distincts de ce cache.

## Projection et masquage

Les entrées sont projetées vers une liste de champs connus avant leur stockage, affichage et capture. Les scopes, propriétaires, identifiants de site et sources autorisées sont contrôlés dans ce modèle de démonstration.

- Les query strings et fragments sont omis des chemins de requête.
- Les IPv4 sont représentées par leur préfixe `/24` ; les IPv6 et clients non reconnus sont masqués.
- Les exemples de messages passent par un masque pour e-mails, URL, Bearer et champs nommés password/token/secret/api-key/cookie/authorization.
- Les champs bruts inconnus ne sont pas conservés dans les entrées projetées.
- Le masquage reste stable lorsqu’une entrée est relue et projetée plusieurs fois.

Ces transformations s’appliquent aux fixtures du prototype. Le centre n’importe aucun journal réel et ne constitue pas un filtre de secrets complet pour des logs de production arbitraires.

## Rapports capturés et export commun

Un rapport capture les entrées de la source et de la fenêtre sélectionnées. Dans les onglets de journaux et d’erreurs, il respecte aussi la recherche et les filtres actifs de la grille. La revue annonce le nombre d’entrées. Le label est unique parmi les rapports conservés et les demandes en attente.

Le snapshot est copié au moment de la revue. Sa fin de job le conserve sans le recalculer : les nouvelles entrées, filtres et préférences suivants ne le modifient pas. Une annulation ne crée aucun rapport. L’option « Include entries » permet de conserver uniquement les statistiques, avec leurs valeurs indisponibles le cas échéant. Les rapports terminés restent des captures historiques du compte, y compris si leur site est supprimé ensuite.

Les détails et aperçus JSON utilisent du texte, sans interprétation HTML. « Download JSON » utilise **`WCPUI.download(content, filename, mimeType)`**, désormais exposé par Foundation **1.1.1**. Il s’agit du même helper que les exports de grilles, sans second moteur de téléchargement. Les exports CSV/JSON, colonnes, filtres et vues enregistrées des journaux restent ceux de DataGrid.

Les profils ROOT « Log pipeline », « Service readiness » et « Collector availability » produisent une preuve explicitement simulée. Ils n’acceptent aucun script, commande, chemin système ou cible externe. Ils ne corrigent et ne redémarrent aucun service.

## Vérification

```powershell
node tools/audit_wcp360_observability.mjs
node tools/audit_wcp360_applications.mjs
node tools/audit_wcp360_delivery.mjs
node tools/audit_wcp360_dns_tls.mjs
node tools/audit_wcp360_protection.mjs
node tools/audit_wcp360_workspace.mjs
node tools/audit_wcp360_hosting.mjs
node tools/audit_wcp360_foundation.mjs
node tools/audit_wcp360_integration.mjs
node tools/audit_wcp360_design_system.mjs WCP360_Design_System
```

Résultats : **44 nouveaux tests**, soit **315 tests de comportement réussis** avec les suites précédentes. L’intégration vérifie **232 pages HTML, 53 scripts, 2 786 références locales, 58 redirections et 4 055 assertions**. L’audit statique ne signale aucun écart. Foundation est chargé par 167 pages complètes ; System logs est désormais une simple redirection. Les deux centres, la redirection, Sites, les deux nouveaux scripts et Foundation répondent en HTTP 200.

Les tests exécutent le vrai contrôleur, les schémas, le moteur de grille et le helper de téléchargement partagé avec un Blob natif et des adaptateurs DOM. Ils vérifient les octets du rapport exporté, son indépendance, les changements entre onglets et les destinations de journaux de chaque page USER. Ils ne prouvent pas qu’un navigateur a téléchargé un fichier.

Le connecteur navigateur était indisponible. Le rendu, les téléchargements natifs, le focus des dialogues et les petits écrans restent à vérifier visuellement. Aucun collecteur, fichier de logs, endpoint, outil de diagnostic ou service réel n’est contacté. Les états localStorage et contrôles de formulaire restent une simulation, sans authentification serveur ni transactions backend.
