<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\PartieEtat;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class PartieEtatFixtures extends Fixture
{
    //méthode permettant d'utiliser les constantes définie dans .env et déclarées dans: services.yaml
    public function __construct(private ContainerBagInterface $parameterBag)
    {
    }

    public function load(ObjectManager $manager): void
    {
    //     $this->addSql("INSERT INTO public.partie_etat(id, nom, commentaire) VALUES (nextval('partie_etat_id_seq'), 'En cours', 'Les joueurs sont entrain de jouer.'),
    //     (nextval('partie_etat_id_seq'), 'Terminée', 'La partie est finie.'),  
    //     (nextval('partie_etat_id_seq'), 'En cours de connexion', 'Les différents joueurs sont entrain de rejoindre la partie.'), 
    //     (nextval('partie_etat_id_seq'), 'Abandonnée', E'Les joueurs ont décidés d\'abandonner la partie. Ou le maitre du jeu à décider d\'annuler la création de la partie.') 
    //    ");

    
        $tabpartieEtat=["0" => array(
                "nom" => $this->parameterBag->get('CONST_ETAT_PARTIE__EN_COURS'),
                "commentaire" => "Les joueurs sont entrain de jouer.",
            ),

            "1" => array(
                "nom" => $this->parameterBag->get('CONST_ETAT_PARTIE__TERMINEE'),
                "commentaire" => "La partie est finie.",
            ),

            "2" => array(
                "nom" => $this->parameterBag->get('CONST_ETAT_PARTIE__EN_COURS_DE_CONNEXION'),
                "commentaire" => "Les différents joueurs sont entrain de rejoindre la partie.",
            ),

            "3" => array(
                "nom" => $this->parameterBag->get('CONST_ETAT_PARTIE__ABANDONNEE'),
                "commentaire" => "Les joueurs ont décidés d'abandonner la partie. Ou le maitre du jeu à décider d'annuler la création de la partie.",
            ),
        ];

        for ($i=0; $i<count($tabpartieEtat); $i++)        
        {
            $partieEtat = new PartieEtat();
                  
            $partieEtat->setNom($tabpartieEtat[$i]["nom"])
                      ->setCommentaire($tabpartieEtat[$i]["commentaire"]);
                     
            $manager->persist($partieEtat);
        }
      
        $manager->flush();
    }
}
