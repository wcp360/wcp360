# MASTER V4.0 --- Decisions Before First Commit

Les documents sources permettent de commencer, mais quatre choix
d'implémentation restent à figer par ADR pour éviter deux
implémentations concurrentes :

1.  Framework HTTP PHP minimal ou stack interne conforme aux couches
    V3.9.
2.  Outil canonique de migrations PostgreSQL.
3.  Encodage IPC V1 : JSON strict, protobuf ou autre format typé.
4.  Bibliothèques de password hashing/session/token et paramètres de
    sécurité exacts.

Ces décisions ne changent pas l'architecture : elles sélectionnent des
outils à l'intérieur des contrats existants.
