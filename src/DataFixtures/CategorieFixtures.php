<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\CategorieEtape;

class CategorieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
    //     $this->addSql("INSERT INTO public.categorie_etape(id, nom, explication) VALUES 
    //     (nextval('categorie_etape_id_seq'), 
    //     'histoire',
    //     'Ecrivez une histoire. Par exemple un début ou une fin.'
    //     ),

    //     (nextval('categorie_etape_id_seq'), 
    //     'personnages',
    //     'Décrivez un personnage. Son aspect physique, ses vêtements, son comportement, ses motivations...'
    //     ),

    //     (nextval('categorie_etape_id_seq'), 
    //     'scènes',
    //     E'Ecrivez une scène, par exemple d\'action, romantique, un dialogue, un rebondissement...'
    //     ),

    //     (nextval('categorie_etape_id_seq'), 
    //     'description',
    //     E'Faite une description, par exemple d\'un lieu, d\'une époque, d\'un objet...')
    // ");

        
        $tabCategorie=["0" => array(
                                "nom" => "histoire",
                                "explication" => "Ecrivez une histoire. Par exemple un début ou une fin."
                            ),

                       "1" => array(
                                "nom" => "personnages",
                                "explication" => "Décrivez un personnage. Son aspect physique, ses vêtements, son comportement, ses motivations..."
                            ),

                       "2" => array(
                                "nom" => "scènes",
                                "explication" => "Ecrivez une scène, par exemple d'action, romantique, un dialogue, un rebondissement..."
                            ),

                       "3" => array(
                                "nom" => "description",
                                "explication" => "Faite une description, par exemple d'un lieu, d'une époque, d'un objet..."
                            ),

            
        ];

       
        for ($i=0; $i<count($tabCategorie); $i++)        
        {
            $categorie = new CategorieEtape();
            $categorie->setNom($tabCategorie[$i]["nom"])
                      ->setExplication($tabCategorie[$i]["explication"]);
            $manager->persist($categorie);
            $this->addReference("categorie-".strval($i + 1), $categorie);
        }

        $manager->persist($categorie);
        $manager->flush();
    }
}
