# 656 --- PHP Security Standard

## Statut

**CANONICAL / NORMATIVE --- V3.5**

Les termes **MUST**, **MUST NOT**, **SHOULD**, **SHOULD NOT** et **MAY**
définissent le niveau d'exigence WCP. Toute exception à une règle
MUST/MUST NOT exige une décision d'architecture documentée et une revue
sécurité.

## MUST

Requêtes SQL préparées, escaping contextuel des sorties, CSRF pour
mutations basées sur session, validation serveur, sessions
Secure/HttpOnly/SameSite adaptées, strict mode, régénération après
authentification/élévation.

## System access

`exec`, `shell_exec`, `system`, `passthru`, `proc_open`, `popen` ne
servent jamais aux opérations système WCP. Elles SHOULD être désactivées
dans les pools lorsque compatible.

## Architecture

Controller → Service → Authorization → DaemonClient. Aucun Controller ne
contacte `wcp-privd`.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-23-PHP-SECURITY-ISOLATION.md`
- `HOSTING-404-26-WEB-PHP-SECURITY-ABUSE-CASES.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
