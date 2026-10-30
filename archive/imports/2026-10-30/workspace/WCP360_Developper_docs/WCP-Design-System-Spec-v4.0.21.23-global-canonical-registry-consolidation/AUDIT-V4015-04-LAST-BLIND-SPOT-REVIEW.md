# V4.0.15 --- Last Blind-spot Review

## Recherche ciblée

  Sujet périphérique            Occurrences indicatives
  --------------------------- -------------------------
  support/ticketing                                  18
  legal/privacy/data export                          29
  status page                                         4
  cost/chargeback                                     0
  hardware SMART/RAID                                 0
  license compliance OSS                              0

## Classification

### Support/ticketing

Utile pour un hébergeur commercial, mais **pas une responsabilité
obligatoire du control panel core**. À intégrer via API/webhooks ou
module OPTIONAL si souhaité.

### RGPD / privacy / data-subject workflows

La sécurité, audit, rétention et isolation existent ; les workflows
juridiques complets dépendent du rôle réel de l'opérateur et de sa
juridiction. **POLICY/OPTIONAL**, avec exigences privacy-by-design dans
le core.

### Public status page

Observability/incident existent. Une page publique de statut est
**OPTIONAL/INTEGRATION**, pas une condition du moteur WCP.

### Chargeback / FinOps

Quotas, plans et billing existent. Coût interne/chargeback est
**FUTURE/OPTIONAL**.

### SMART/RAID matériel

Important sur bare metal mais dépend du périmètre
infrastructure/provider. À traiter comme **OPTIONAL infrastructure
adapter**, avec health hooks.

### OSS license inventory / third-party notices

À inclure dans la gouvernance de build/release et SBOM, mais ne
nécessite pas une nouvelle famille UI fonctionnelle.

## Verdict

Ces sujets ne justifient pas V4.0.16 fonctionnelle avant les ADR.
