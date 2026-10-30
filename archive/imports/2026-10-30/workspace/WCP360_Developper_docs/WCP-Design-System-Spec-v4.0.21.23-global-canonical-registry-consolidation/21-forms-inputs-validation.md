# 21 --- Formulaires, inputs & validation

## Objectif

Créer une grammaire unique pour les formulaires simples et les
configurations système avancées.

## Règles obligatoires

-   Anatomie : label → description optionnelle → contrôle → aide/erreur.
-   Validation client pour confort, validation serveur comme autorité.
-   Conserver les valeurs après erreur serveur.
-   Champs dangereux affichent exemples/contraintes avant saisie.
-   Secret masqué par défaut ; reveal explicite ; copy audité si
    nécessaire.
-   Auto-save seulement pour réglages réversibles et clairement
    signalés.

## Composants / anatomie

TextInput, PasswordInput, Number, Textarea, Select, Combobox, Checkbox,
Radio, Switch, DomainInput, IPInput, CIDRInput, PortInput, PathInput,
KeyValueEditor, DNSRecordEditor, FileUpload, SecretField.

## États à prévoir

default, focus, filled, read-only, disabled, validating, valid, warning,
error, server error.

## Accessibilité

`aria-describedby`, association label/champ, erreurs annoncées, ordre
logique de tabulation.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
