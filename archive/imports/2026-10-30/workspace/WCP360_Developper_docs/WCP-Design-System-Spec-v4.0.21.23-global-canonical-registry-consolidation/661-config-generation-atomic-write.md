# 661 --- Configuration Generation & Atomic Write

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

## Pipeline normatif

Resolve → Prepare → Generate temp → set owner/mode → syntax validate →
diff → snapshot/recovery point → fsync si nécessaire → atomic rename →
reload → health check → commit/audit.

En échec : rollback/recovery → verify → audit failure.

## MUST

Une configuration invalide ne remplace jamais directement la dernière
configuration fonctionnelle.
