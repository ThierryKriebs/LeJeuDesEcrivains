<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\LongueurPartie;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class LongueurPartieFixtures extends Fixture
{
    public function __construct(private ContainerBagInterface $parameterBag)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // $this->addSql("INSERT INTO public.longueur_partie(id, nom, nombre_etape) VALUES 
        //     (nextval('longueur_partie_id_seq'), 
        //     'Partie courte', 
        //     3),

        //     (nextval('longueur_partie_id_seq'), 
        //     'Partie moyenne', 
        //     5),

        //     (nextval('longueur_partie_id_seq'), 
        //     'Partie longue', 
        //     7),
                                    
        //     (nextval('longueur_partie_id_seq'), 
        //     'Partie extrême', 
        //     999)
        // ");
        
        $tabLongueurPartie=["0" => array(
                "nom" => 'Partie courte', 
                "nombre_etape" => 3,
            ),

            "1" => array(
                "nom" =>  $this->parameterBag->get('CONST_NOM_LONGUEUR_PARTIE_PAR_DEFAUT'), 
                "nombre_etape" => 5,
            ),

            "2" => array(
                "nom" => 'Partie longue', 
                "nombre_etape" => 7,
            ),

            "3" => array(
                "nom" => 'Partie extrême', 
                "nombre_etape" => 999,
            )];
        
        for ($i=0; $i<count($tabLongueurPartie); $i++)        
        {
            $longueurPartie = new LongueurPartie();
                    
            $longueurPartie->setNom($tabLongueurPartie[$i]["nom"])
                           ->setNombreEtape($tabLongueurPartie[$i]["nombre_etape"]);
            
            $manager->persist($longueurPartie);
        } 

        $manager->flush();
    }
}
