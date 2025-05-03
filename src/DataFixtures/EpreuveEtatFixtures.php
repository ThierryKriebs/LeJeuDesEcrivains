<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\EpreuveEtat;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class EpreuveEtatFixtures extends Fixture implements FixtureGroupInterface
{
    //méthode permettant d'utiliser les constantes définie dans .env et déclarées dans: services.yaml
    public function __construct(private ContainerBagInterface $parameterBag)
    {
    }

    //Permet d'appeler uniquement cette fixture
    public static function getGroups(): array
     {
         return ['epreuveEtat'];
     }

    public function load(ObjectManager $manager): void
    {
       
        $tabEpreuveEtat=["0" => array(
                "nom" => $this->parameterBag->get('CONST_ETAT_EPREUVE__EN_COURS'),
                "commentaire" => "Les joueurs sont entrain de jouer à cette épreuve.",
            ),

            "1" => array(
                "nom" => $this->parameterBag->get('CONST_ETAT_EPREUVE__NOTATION_VA_DEMARRER'),
                "commentaire" => "La notation va démarrer.",
            ),

            "2" => array(
                "nom" => $this->parameterBag->get('CONST_ETAT_EPREUVE__NOTATION_DEMARREE'),
                "commentaire" => "La notation a démarrée.",
            ),

            "3" => array(
                "nom" => $this->parameterBag->get('CONST_ETAT_EPREUVE__ABANDONNEE'),
                "commentaire" => "Les joueurs ont décidés d'abandonner l'épreuve.",
            ),

            "4" => array(
                "nom" => $this->parameterBag->get('CONST_ETAT_EPREUVE__NOTATION_TERMINEE'),
                "commentaire" => "Les joueurs ont terminés de noter l'épreuve.",
            ),

            "5" => array(
                "nom" => $this->parameterBag->get('CONST_ETAT_EPREUVE__VA_DEMARRER'),
                "commentaire" => "L'épreuve va bientôt démarrer.",
            ),

        ];


        for ($i=0; $i<count($tabEpreuveEtat); $i++)        
        {
            $epreuveEtat = new EpreuveEtat();
                  
            $epreuveEtat->setNom($tabEpreuveEtat[$i]["nom"])
                      ->setCommentaire($tabEpreuveEtat[$i]["commentaire"]);
                     
            $manager->persist($epreuveEtat);
        }
      
        $manager->flush();
    }
}
