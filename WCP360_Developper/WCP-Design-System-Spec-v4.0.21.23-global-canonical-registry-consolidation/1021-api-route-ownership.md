# 1021 --- API Route Ownership

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

Chaque route a exactement un owning module. Les routes WCP-ADMIN et
WCP-USER peuvent exposer des projections différentes d'un même service
métier, mais l'autorisation reste serveur-side.

Aucune route générique `/execute`, `/command`, `/shell`,
`/filesystem?path=/...` ou `/service/{arbitrary-name}/restart`.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
