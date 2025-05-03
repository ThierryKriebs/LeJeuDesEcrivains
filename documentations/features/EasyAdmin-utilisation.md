
EasyAdmin est un outil de Symfony permettant de mettre à disposition des administrateurs, des interfaces Web de CRUD simples (gestion d'une table de la base de données), sans ou pratiquement sans programmer.

Il permet de gagner beaucoup de temps!

Sources:
- [site Officiel](https://symfony.com/bundles/EasyAdminBundle/current/index.html) (en anglais) pour Symfony 7
- [Youtubeur](https://www.youtube.com/watch?v=3SlpNt4CxRE&list=PLf0lQ0jp2dlZBBF5GHINwvmyOL2hFfI0T&index=8) (en français) pour Symfony 6
- [Site Officel (extrait de leur livre)](https://symfony.com/doc/6.4/the-fast-track/fr/9-backend.html) (en français) pour Symfony 7


## Mise en oeuvre de base:
Pour l'essentiel, il suffit de suivre les étapes comme précisée dans la documentation (Site Officiel (extrait de leur livre) en Français):
- Installer Easyadmin à l'aide de la commande suivante: `symfony composer req "easycorp/easyadmin-bundle:4.x-dev"`
- Créer un Dashboard, un tableau de bord, (Une première interface vide): `symfony console make:admin:dashboard` Cela va créer le contrôleur: << src/Controller/Admin/DashboardController.php >>, accessible sur la route << \Admin >>.
- Créer ensuite vos interfaces web de CRUD à l'aide de la commande suivante: `symfony console make:admin:crud`. La commande vous affiche ensuite toutes vos entités et vous demande pour quelle entité vous souhaitez créer une interface (entrez son numéro). Entrez ensuite les valeurs par défaut. Cela créé le fichier suivant: << src/Controller/Admin/XXXCrudController.php >>, où XXX est le nom de votre entité.
- Il suffit ensuite de relier ces CRUDS au Dashboard (tableau de bord). Cela se fait dans la méthode configureMenuItems() du Dashboard Controller.

Exemple:
 ` yield MenuItem::linkToRoute('Page d\'accueil', 'fa fa-home', 'app_home');`

'app_home' => nom de la route dans le contrôleur home

'fa fa-home' => Icône à utiliser, drectement rechercher sur le site: https://fontawesome.com/icons

'Page d\'accueil' => Nom de la page dans le Dashboard

- L'interface d'EasyAdmin apparaîtra en français si l'application est configurée en français. Pour cela, mettre le paramètre `default_locale` en français sous: config\packages\translation.yaml
default_locale: fr


## Intitulé des titres et des boutons dans les formulaires de CRUD:
Il est possible de changer le titre des interfaces web de CRUD ainsi que des boutons dans la méthode configureCrud de chaque interface. Exemple avec le l'interface "SousCategorieEtapeCrudController.php"

`return $crud`

 `->setEntityLabelInSingular('Sous catégorie d\'étape')//Titre des boutons`

 `->setEntityLabelInPlural('Sous catégories d\'étapes')//Titre en haut de la page`

`;`

ATTENTION: Pour changer le titre dans le Dasboard, il faut le faire également dans la méthode `configureMenuItems` du Dashboard contrôleur.

## Personnaliser le type de champ dans chaque formulaire:
Il est possible de personnaliser chaque type de champs (liste déroulante, champ de texte, champ de texte enrichi...). Cela se fait dans la méthode:  "public function configureFields(string $pageName): iterable".

La liste des champs est définie ici: [liste des champs](https://symfony.com/bundles/EasyAdminBundle/current/fields.html#field-types)

Il est également possible de les afficher ou nom sur la page d'index avec les méthodes `hideOnIndex()` et `onlyOnIndex()`.
On peut faire de même sur les pages de création et de modification. Plus de détails ici: [options d'affichage](https://symfony.com/bundles/EasyAdminBundle/current/fields.html#configuring-the-fields-to-display)

D'autres options sont possibles telles que le format d'affichage des données, ou les tri par défaut. Voir [plus de détails](https://symfony.com/bundles/EasyAdminBundle/current/fields.html#configuring-the-fields-to-display)

# Exemple de création d'un champ de gestion des images:#
`yield ImageField::new('nom_image', 'Images')`

        `->setSortable(false)`
        `->setBasePath('images/genres_litteraires/')`
        `->setUploadDir('public/images/genres_litteraires/')`
        `->setFileConstraints(new Image(maxSize: '1000k'))`
        `->setUploadedFileNamePattern('[year]-[month]-[day]-[slug]-[contenthash].[extension]');`

- Seul le nom de l'image sera stocké en base de données
- setBasePath et setUploadDir indiquent où sont stocké les images
- setFileConstrants permet dans l'exemple de ne pas autoriser les images de plus de 1MO
- setUploadedFileNamePattern permet ici de donner un nom unique à l'image


## Créer des filtres:
Il est possible de créer des filtres pour une interface Web de crud, afin de faire des recherches parmis des données.
Il faut pour cela écrire une méthode configureFilters. Exemple:

 `public function configureFilters(Filters $filters): Filters`

    `{`

        `return $filters`

            `->add('nom');`

    `}`

[plus de détails sur les filtres](https://symfony.com/bundles/EasyAdminBundle/current/filters.html)


## Tri par défaut lors de l'affichage:
Sur l'interface Web d'index de chaque CRUD, il est possible d'effectuer un tri par défaut des données. Cela se fait dans la méthode `configureCrud`:

Exemple:

`->setDefaultSort(['nom' => 'DESC'])`

ATTENTION: le nom doit respecter la casse et être strictement le même que celui utilisé dans l'entité.

Dans la méthode configureFields, il est possible de spécifier si chaque champs est triable ou nom par les utilisateurs. Pour cela il faut utiliser la méthode `setSortable`

Exemple:

 `yield TextField::new('nom', 'Nom de la sous-catégorie')`

 `           ->setSortable(true)`