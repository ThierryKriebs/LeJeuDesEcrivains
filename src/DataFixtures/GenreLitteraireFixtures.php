<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\GenreLitteraire;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;


class GenreLitteraireFixtures extends Fixture
{
   //méthode permettant d'utiliser les constantes définie dans .env et déclarées dans: services.yaml
   public function __construct(private ContainerBagInterface $parameterBag)
   {
   }

    public function load(ObjectManager $manager): void
    {
        // $this->addSql("INSERT INTO public.genre_litteraire(id, nom, commentaire, exemple, est_active, nom_image) VALUES 
        //     (nextval('genre_litteraire_id_seq'), 
        //     'Libre (Chaque joueur choisit un genre librement)', 
        //     E'Ce que l\'on veut', 
        //     '-',
        //     TRUE,
        //     'free.jpg'),

        //     (nextval('genre_litteraire_id_seq'), 
        //     'Science Fiction', 
        //     '<div>commentaire</div>', 
        //     '',
        //     TRUE,
        //     'terminator.jpg'),

        //     (nextval('genre_litteraire_id_seq'), 
        //     'Science Fiction => Space Opéra', 
        //     '<div>commentaire</div>', 
        //     E'<div><a href=\'https://www.google.com/search?q=star+wars&amp;sca_esv=417bc6eb85d48c0f&amp;rlz=1C1FKPE_frFR942FR942&amp;sxsrf=ADLYWIJIeg8rsb2ZuapmWRkyy6J8vEh_XQ%3A1723925615707&amp;ei=bwTBZp7uKoajkdUPm83KiQo&amp;ved=0ahUKEwje4cWv6_yHAxWGUaQEHZumMqEQ4dUDCA8&amp;uact=5&amp;oq=star+wars&amp;gs_lp=Egxnd3Mtd2l6LXNlcnAiCXN0YXIgd2FyczIKECMYgAQYJxiKBTIKEC4YgAQYQxiKBTINEAAYgAQYsQMYFBiHAjIKEC4YgAQYQxiKBTIKEC4YgAQYQxiKBTIKEC4YgAQYQxiKBTIKEC4YgAQYQxiKBTIKEC4YgAQYQxiKBTIKEAAYgAQYQxiKBTIQEC4YgAQYsQMYgwEYFBiHAjIZEC4YgAQYQxiKBRiXBRjcBBjeBBjgBNgBAUiWBFAAWK4BcAB4AZABAJgBgwGgAbcBqgEDMS4xuAEDyAEA-AEBmAICoALFAcICCxAuGIAEGLEDGIMBmAMAugYGCAEQARgUkgcDMS4xoAfUMg&amp;sclient=gws-wiz-serp\'>Star Wars</a></div>',
        //     TRUE,
        //     '2024-08-17-star-wars-a-new-hope-71bfa06d9e937f9f5eaf3eb39bd96f57ec18af12.jpg'),

        //     (nextval('genre_litteraire_id_seq'), 
        //     'Science Fiction => Hard Science', 
        //     E'<div>La hard science-fiction est un genre de science-fiction dans lequel les technologies, les sociétés et leurs évolutions, telles qu\'elles sont décrites dans le roman, peuvent être considérées comme vraisemblables au regard de l\'état des connaissances scientifiques au moment où l\'auteur écrit son œuvre.</div>', 
        //     E'<ul><li><a href=\'https://www.google.com/search?q=2001+l%27odyss%C3%A9e+de+l%27espace&amp;rlz=1C1FKPE_frFR942FR942&amp;oq=2001+l%27odyss%C3%A9e+de+l%27espace&amp;gs_lcrp=EgZjaHJvbWUqBwgAEAAYjwIyBwgAEAAYjwIyDAgBEC4YJxiABBiKBTIHCAIQABiABDIHCAMQLhiABDIHCAQQABiABDIHCAUQABiABDIHCAYQABiABDIHCAcQABiABDIHCAgQLhiABDIHCAkQABiABNIBCDQ1NThqMGo3qAIAsAIA&amp;sourceid=chrome&amp;ie=UTF-8\'><strong>2001&nbsp;</strong>l\'Odyssée de l\'espace</a></li><li><a href=\'https://www.google.com/search?q=Interstellar&amp;rlz=1C1FKPE_frFR942FR942&amp;oq=Interstellar&amp;gs_lcrp=EgZjaHJvbWUyEggAEEUYORiDARjjAhixAxiABDINCAEQLhiDARixAxiABDINCAIQLhiDARixAxiABDINCAMQABiDARixAxiABDIKCAQQABixAxiABDINCAUQABiDARixAxiABDIQCAYQABiDARixAxiABBiKBTIHCAcQABiABDIHCAgQABiABDIJCAkQLhgKGIAE0gEHNzQyajBqOagCALACAA&amp;sourceid=chrome&amp;ie=UTF-8\'>Interstellar</a></li><li><a href=\'https://www.google.com/search?q=seul+sur+mars&amp;rlz=1C1FKPE_frFR942FR942&amp;oq=seul+sur+Mars&amp;gs_lcrp=EgZjaHJvbWUqBwgAEAAYjwIyBwgAEAAYjwIyCggBEC4YsQMYgAQyDwgCEEUYORjjAhixAxiABDIHCAMQABiABDIHCAQQABiABDIHCAUQLhiABDIHCAYQABiABDIHCAcQABiABDIHCAgQABiABDIHCAkQABiABNIBCDIzNThqMGo3qAIAsAIA&amp;sourceid=chrome&amp;ie=UTF-8\'>Seul sur Mars</a></li></ul>',
        //     FALSE,
        //     '2024-08-17-2001-90916ff6db1b0d73a6082e0117b4c6ed722f888f.jpg'),

        //     (nextval('genre_litteraire_id_seq'), 
        //     'Romance', 
        //     E'<div>Une belle histoire d\'amour!</div>', 
        //     '<div>Euh...? Titanic!</div>',
        //     TRUE,
        //     'coeur.jpg'),

        //     (nextval('genre_litteraire_id_seq'), 
        //     'Espionnage', 
        //     '', 
        //     '',
        //     TRUE,
        //     'bond.jpg'),
                                    
        //     (nextval('genre_litteraire_id_seq'), 
        //     'Policier', 
        //     E'<div>Le roman policier est un roman relevant du genre policier. Le drame y est fondé sur l\'attention d\'un fait ou, plus précisément, d\'une intrigue, et sur une recherche méthodique faite de preuves, le plus souvent par une enquête policière ou encore une enquête de détective privé.</div>', 
        //     E'<div>Les romans d\'Agatha Christie ou d\'Harlan Coben, la série Columbo... les exemples sont innombrables!</div>',
        //     TRUE,
        //     '2024-08-28-columbo-0d5b0c63688957fe6c45a5f32bd12a3ebbe0e4be.jpg'),

        //     (nextval('genre_litteraire_id_seq'), 
        //     'Horreur', 
        //     'test', 
        //     'test',
        //     TRUE,
        //     'freddy.webp'),

        //     (nextval('genre_litteraire_id_seq'), 
        //     'Héroic fantasy', 
        //     E'L\'heroic fantasy est un genre littéraire dans lequel les personnages évoluent dans un univers fictif, médiéval et fantasy (on trouve parfois l\'expression médiéval-fantastique). Il y a des chevaliers, des princes... comme en France ou en Angleterre à l\'époque des croisades. Il est aussi fantastique, avec des magiciens, des créatures imaginaires (telles que des licornes, des dragons...) et des êtres non humains, intelligents et parlant (tels que des elfes ou des démons). Le vrai succès de ce genre littéraire a débuté grâce à John Tolkien, le célèbre auteur du Seigneur des anneaux.', 
        //     'Le Seigneur des anneaux, Warcraft, Dungeon&Dragon',
        //     TRUE,
        //     'le seigneur des anneaux.jpg'),

        //     (nextval('genre_litteraire_id_seq'), 
        //     'Fantastiques', 
        //     E'Le fantastique se caractérise par l\’intrusion du surnaturel dans le cadre réaliste d\'un récit. Le fantastique créé une hésitation entre le surnaturel et le naturel, le possible ou l\'impossible et parfois entre le logique et l\'illogique.', 
        //     'X-Files',
        //     TRUE,
        //     'x-files.png')

        // ");

        $genreParDefaut = $this->parameterBag->get('CONST_GENRE_LITT_PAR_DEFAUT');
        $tabgenreLitteraire=["0" => array(
            "nom" => $genreParDefaut, //"Libre (Chaque joueur choisit un genre librement)",
            "commentaire" => "Ce que l'on veut",
            "exemple" => "-",
            "est_active" => TRUE,
            "nom_image" => "free.jpg",
        ),

        "1" => array(
            "nom" => "Science Fiction",
            "commentaire" => "<div>commentaire</div>",
            "exemple" => "",
            "est_active" => TRUE,
            "nom_image" => "terminator.jpg",
        ),

        "2" => array(
            "nom" => "Science Fiction => Space Opéra",
            "commentaire" => "<div>commentaire</div>",
            "exemple" => "<div><a href=\'https://www.google.com/search?q=star+wars&amp;sca_esv=417bc6eb85d48c0f&amp;rlz=1C1FKPE_frFR942FR942&amp;sxsrf=ADLYWIJIeg8rsb2ZuapmWRkyy6J8vEh_XQ%3A1723925615707&amp;ei=bwTBZp7uKoajkdUPm83KiQo&amp;ved=0ahUKEwje4cWv6_yHAxWGUaQEHZumMqEQ4dUDCA8&amp;uact=5&amp;oq=star+wars&amp;gs_lp=Egxnd3Mtd2l6LXNlcnAiCXN0YXIgd2FyczIKECMYgAQYJxiKBTIKEC4YgAQYQxiKBTINEAAYgAQYsQMYFBiHAjIKEC4YgAQYQxiKBTIKEC4YgAQYQxiKBTIKEC4YgAQYQxiKBTIKEC4YgAQYQxiKBTIKEC4YgAQYQxiKBTIKEAAYgAQYQxiKBTIQEC4YgAQYsQMYgwEYFBiHAjIZEC4YgAQYQxiKBRiXBRjcBBjeBBjgBNgBAUiWBFAAWK4BcAB4AZABAJgBgwGgAbcBqgEDMS4xuAEDyAEA-AEBmAICoALFAcICCxAuGIAEGLEDGIMBmAMAugYGCAEQARgUkgcDMS4xoAfUMg&amp;sclient=gws-wiz-serp\'>Star Wars</a></div>",
            "est_active" => TRUE,
            "nom_image" => "2024-08-17-star-wars-a-new-hope-71bfa06d9e937f9f5eaf3eb39bd96f57ec18af12.jpg",
        ),

        "3" => array(
            "nom" => "Science Fiction => Hard Science",
            "commentaire" => "<div>La hard science-fiction est un genre de science-fiction dans lequel les technologies, les sociétés et leurs évolutions, telles qu'elles sont décrites dans le roman, peuvent être considérées comme vraisemblables au regard de l'état des connaissances scientifiques au moment où l'auteur écrit son œuvre.</div>",
            "exemple" => "<ul><li><a href=\'https://www.google.com/search?q=2001+l%27odyss%C3%A9e+de+l%27espace&amp;rlz=1C1FKPE_frFR942FR942&amp;oq=2001+l%27odyss%C3%A9e+de+l%27espace&amp;gs_lcrp=EgZjaHJvbWUqBwgAEAAYjwIyBwgAEAAYjwIyDAgBEC4YJxiABBiKBTIHCAIQABiABDIHCAMQLhiABDIHCAQQABiABDIHCAUQABiABDIHCAYQABiABDIHCAcQABiABDIHCAgQLhiABDIHCAkQABiABNIBCDQ1NThqMGo3qAIAsAIA&amp;sourceid=chrome&amp;ie=UTF-8\'><strong>2001&nbsp;</strong>l'Odyssée de l'espace</a></li><li><a href=\'https://www.google.com/search?q=Interstellar&amp;rlz=1C1FKPE_frFR942FR942&amp;oq=Interstellar&amp;gs_lcrp=EgZjaHJvbWUyEggAEEUYORiDARjjAhixAxiABDINCAEQLhiDARixAxiABDINCAIQLhiDARixAxiABDINCAMQABiDARixAxiABDIKCAQQABixAxiABDINCAUQABiDARixAxiABDIQCAYQABiDARixAxiABBiKBTIHCAcQABiABDIHCAgQABiABDIJCAkQLhgKGIAE0gEHNzQyajBqOagCALACAA&amp;sourceid=chrome&amp;ie=UTF-8\'>Interstellar</a></li><li><a href=\'https://www.google.com/search?q=seul+sur+mars&amp;rlz=1C1FKPE_frFR942FR942&amp;oq=seul+sur+Mars&amp;gs_lcrp=EgZjaHJvbWUqBwgAEAAYjwIyBwgAEAAYjwIyCggBEC4YsQMYgAQyDwgCEEUYORjjAhixAxiABDIHCAMQABiABDIHCAQQABiABDIHCAUQLhiABDIHCAYQABiABDIHCAcQABiABDIHCAgQABiABDIHCAkQABiABNIBCDIzNThqMGo3qAIAsAIA&amp;sourceid=chrome&amp;ie=UTF-8\'>Seul sur Mars</a></li></ul>",
            "est_active" => FALSE,
            "nom_image" => "2024-08-17-2001-90916ff6db1b0d73a6082e0117b4c6ed722f888f.jpg",
        ),
        
        "4" => array(
            "nom" => "Romance",
            "commentaire" => "<div>Une belle histoire d'amour!</div>",
            "exemple" => "<div>Euh...? Titanic!</div>",
            "est_active" => TRUE,
            "nom_image" => "coeur.jpg",
        ),

        "5" => array(
            "nom" => "Espionnage",
            "commentaire" => "",
            "exemple" => "",
            "est_active" => TRUE,
            "nom_image" => "bond.jpg",
        ),

        "6" => array(
            "nom" => "Policier",
            "commentaire" => "<div>Le roman policier est un roman relevant du genre policier. Le drame y est fondé sur l'attention d'un fait ou, plus précisément, d'une intrigue, et sur une recherche méthodique faite de preuves, le plus souvent par une enquête policière ou encore une enquête de détective privé.</div>",
            "exemple" => "<div>Les romans d'Agatha Christie ou d'Harlan Coben, la série Columbo... les exemples sont innombrables!</div>",
            "est_active" => TRUE,
            "nom_image" => "2024-08-28-columbo-0d5b0c63688957fe6c45a5f32bd12a3ebbe0e4be.jpg",
        ),

        "7" => array(
            "nom" => "Horreur",
            "commentaire" => "test",
            "exemple" => "test",
            "est_active" => TRUE,
            "nom_image" => "freddy.webp",
        ),

        "8" => array(
            "nom" => "Héroic fantasy",
            "commentaire" => "L'heroic fantasy est un genre littéraire dans lequel les personnages évoluent dans un univers fictif, médiéval et fantasy (on trouve parfois l'expression médiéval-fantastique). Il y a des chevaliers, des princes... comme en France ou en Angleterre à l\'époque des croisades. Il est aussi fantastique, avec des magiciens, des créatures imaginaires (telles que des licornes, des dragons...) et des êtres non humains, intelligents et parlant (tels que des elfes ou des démons). Le vrai succès de ce genre littéraire a débuté grâce à John Tolkien, le célèbre auteur du Seigneur des anneaux.",
            "exemple" => "Le Seigneur des anneaux, Warcraft, Dungeon&Dragon",
            "est_active" => TRUE,
            "nom_image" => "le seigneur des anneaux.jpg",
        ),

        "9" => array(
            "nom" => "Fantastiques",
            "commentaire" => "Le fantastique se caractérise par l’intrusion du surnaturel dans le cadre réaliste d'un récit. Le fantastique créé une hésitation entre le surnaturel et le naturel, le possible ou l'impossible et parfois entre le logique et l'illogique.",
            "exemple" => "X-Files",
            "est_active" => TRUE,
            "nom_image" => "x-files.png",
        )];

        for ($i=0; $i<count($tabgenreLitteraire); $i++)        
        {
            $genreLitt = new GenreLitteraire();
                  
            $genreLitt->setNom($tabgenreLitteraire[$i]["nom"])
                      ->setCommentaire($tabgenreLitteraire[$i]["commentaire"])
                      ->setExemple($tabgenreLitteraire[$i]["exemple"])
                      ->setEstActive($tabgenreLitteraire[$i]["est_active"])
                      ->setNomImage($tabgenreLitteraire[$i]["nom_image"]);
           
                      $manager->persist($genreLitt);
        }
      
        $manager->flush();
    }
}
