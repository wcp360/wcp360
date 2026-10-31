# V4.0.21.13 --- Permission / Scope / Entitlement Registry

## Séparation normative

Permission = ce que l'acteur peut demander. Scope = sur quelles
ressources. Entitlement = ce que le compte/plan possède
commercialement/fonctionnellement. Capability = ce que l'hôte/backend
sait réellement faire. Security floor = ce qui ne peut pas être abaissé.

## Décision effective

`permission ∩ scope ∩ entitlement ∩ capability ∩ lifecycle ∩ security_floor`.

## Wildcards

Les wildcards de permission ne doivent jamais accorder implicitement une
future Operation ID. Toute nouvelle opération sensible requiert
classification.
