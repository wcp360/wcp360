# 78 --- Rapport de cohérence V3.5

## Statut

Ce document **remplace** le rapport historique V2 devenu obsolète.

## Contrôles

-   Nomenclature officielle : **WCP / WCP-USER / WCP-ADMIN**.
-   Les rapports `95–98` restent des journaux historiques de passes
    précédentes et ne constituent pas la source de vérité courante.
-   `99-module-catalog.md`, `INDEX.md` et les documents `MASTER-*`
    constituent les points d'entrée actuels.
-   Une fonctionnalité ne doit plus être ajoutée par simple empilement :
    elle reçoit un **document canonique**, des sous-spécifications et
    des références.

## Problèmes confirmés pendant l'audit V3.4

1.  Doublon de nom `Backup Destinations` : `212` et `531`.
2.  Chevauchement entre l'ancien SSH Access (`190`) et la série
    détaillée `550–554`.
3.  Chevauchement entre Observability (`65`, `260`) et Telemetry
    (`520–525`).
4.  Plusieurs documents conservent des marqueurs V2/V3.1/V3.2/V3.3
    utiles comme provenance, mais ils ne doivent pas être interprétés
    comme version active du contrat.
5.  Certaines décisions de production restent explicitement ouvertes,
    notamment la rétention/export d'audit.

## Règle V3.5

Les doublons ne sont pas supprimés aveuglément. Le document canonique
est identifié ; les anciens documents deviennent **overview**, **UI
contract**, **adapter contract** ou **historical record**.
