# V4.0.21.22.1 --- Contradictions, ancienneté et supersession

## README / baseline

Le README commence encore par `Spécification maître v3.3`, puis accumule
les extensions jusqu'à V4.0.21.22. Cette structure est historique mais
ambiguë.

**Correction requise :** - titre courant V4.0.21.22.x ; -
`NO CODE / CODE BLOCKED` visible au début ; - baseline normative
actuelle ; - historique de versions déplacé après le statut courant.

## READY historiques

De nombreux documents V3.x/V4.0.x utilisent `READY`,
`IMPLEMENTATION-READY` ou `Ready for dev`. Ces statuts ne doivent pas
être lus comme un `GLOBAL READY`. La hiérarchie normative récente doit
explicitement superseder ces gates locales.

## PARTIAL historiques

Les anciens audits restent utiles comme preuves de dette découverte,
mais leur statut doit être relié à une disposition : `CLOSED_BY`,
`SUPERSEDED_BY`, `OUT_OF_SCOPE_V1` ou `STILL_OPEN`.

Une simple phrase « normative deepening » n'est pas toujours suffisante
pour prouver que chaque exigence de l'ancien PARTIAL est fermée.
