# Audit de cohérence transversale

## Classification finale

### CRITICAL
Aucun défaut CRITICAL détecté dans le corpus source actuel.

Il n’existe pas de preuve d’un endpoint root générique ou d’un mécanisme de privilège cosmétique dans les spécifications récentes.

### HIGH
1. Namespace permissions non synchronisé.
2. State machine Job non synchronisée (`BLOCKED`).
3. Navigation récente non intégrée.
4. 212 placeholders `href="#"`.
5. Double système JavaScript sur 21 pages.
6. Accessibilité du prototype en retard sur sa propre spécification.
7. CSS legacy et overrides massifs.
8. Corpus actif/archive mélangé.

### MEDIUM
1. 20 pages anglaises au milieu d’un corpus principalement français.
2. `dir` absent des pages.
3. 4 pages sans `<h1>` : `login.html`, `mfa.html`, `v2.5.3.7-index.html`, `v2.5.3.8-index.html`.
4. Tables sans caption/scope.
5. Boutons sans `type` explicite sur la majorité des pages.
6. Plusieurs anciennes pages exactes ou quasi-dupliquées.
7. Entrypoint `index.html` ne représente pas clairement la dernière base active.
8. Plusieurs pages utilisent encore l’ancien `styles.css`.

### LOW / CLEANUP
1. Index historiques conservés à la racine.
2. titres HTML dupliqués pour certaines anciennes/canoniques ;
3. fichiers canoniques et aliases exacts ;
4. noms de pages legacy (`ssl.html` vs `ssl-tls.html`, `audit.html` vs `audit-explorer.html`).

## Ce qui est cohérent

- séparation WCP-USER / WCP-ADMIN ;
- daemon allowlist ;
- anti-lockout ;
- privileged session ;
- impersonation read-only par défaut ;
- break-glass séparé ;
- SHOW_ONCE / NEVER_REVEAL ;
- UNKNOWN_OUTCOME ;
- resource-centric UX ;
- plans / feature lists / DEMO ;
- filesystem system boundaries ;
- install/finalization policy.