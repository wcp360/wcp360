# 830 --- Application / CMS Resource Model

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Ressources : `ApplicationInstallation`, `ApplicationTemplate`,
`ApplicationVersion`, `StagingEnvironment`, `AppBackupRef`,
`VulnerabilityFinding`, `HardeningProfile`.

WordPress est le premier toolkit spécialisé ;
Nextcloud/Laravel/PrestaShop/Magento/Ghost/templates utilisent le
contrat ApplicationAdapter lorsqu'applicable.
