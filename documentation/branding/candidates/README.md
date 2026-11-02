# WCP360 — Propositions de marque : originaux en attente d'import

Statut au 2 octobre 2026 : **archive vérifiée localement, NON TÉLÉVERSÉE dans GitHub**. Aucun logo n'est déclaré officiel. Le présent README et le vérificateur ne contiennent pas les pixels des sept images.

## Fichier attendu

Déposer, sans le décompresser ni le recomprimer, le fichier remis dans la conversation :

`WCP360-unpublished-source-assets-2026-10-02.zip`

Chemin final attendu dans cette branche :

`documentation/branding/candidates/WCP360-unpublished-source-assets-2026-10-02.zip`

Taille exacte : 6 596 441 octets. SHA-256 :

`3580a3f79aa194b77abb3c9b1ed026cf99f226ac7b179a6da78d96d2dbda53d0`

L'archive contient les sept PNG originaux, un manifeste et un README. Les noms des originaux et leurs empreintes sont inscrits dans le catalogue de publication du projet. Aucun nouveau fichier graphique ou remplacement de Design System n'est créé par cette étape.

## Import sans code à écrire

Dans ce dossier de la branche `maintenance/sc-m01-repository-guardrails`, choisir **Add file → Upload files**, déposer le ZIP ci-dessus puis valider le commit sur cette branche. Ne pas créer de nouvelle branche pour ce transfert. La PR #42 ouverte recevra le fichier et son workflow vérifiera la copie publiée. Ce transfert ne fusionne pas la PR et ne vaut pas validation des propositions de marque.

La procédure de chargement par navigateur est décrite dans la [documentation GitHub](https://docs.github.com/fr/repositories/working-with-files/managing-files/adding-a-file-to-a-repository). L'archive ne nécessite pas Git LFS et reste sous la limite navigateur indiquée par GitHub.

## Vérification

`tools/repository/check_branding_archive.py` contrôle la taille et le SHA-256 de l'archive, ses neuf membres exacts, les longueurs et empreintes de chaque membre, la signature PNG et l'absence de liens symboliques ou d'entrée supplémentaire. Il lit les membres sans les extraire et sans modifier le dépôt.

Neuf tests synthétiques couvrent le cas valide et des rejets : contenu changé, membre absent, ajout inattendu, traversal, lien symbolique, absence optionnelle, absence obligatoire et mauvaise archive d'origine. Ils ne sont pas des tests applicatifs WCP360.

Sans le ZIP, le workflow produit explicitement `NOT_PUBLISHED`. Le mode optionnel laisse avancer la maintenance indépendante, mais ne certifie ni la présence des images ni la publication globale. Avec le ZIP exact, il produit `ORIGINAL_BYTES_VERIFIED`. Pour déclarer la publication GitHub acquise, conserver aussi le commit qui contient le ZIP et le run qui vérifie ce commit ; une vérification locale seule ne suffit pas.

SC-M01 reste en pause. Les publications DB/QA/Engineering, le corpus DOC et les avis propriétaires ne sont pas remplacés par cet import d'images. Suivi existant : #41 ; aucune nouvelle issue ni autorité de marque.
