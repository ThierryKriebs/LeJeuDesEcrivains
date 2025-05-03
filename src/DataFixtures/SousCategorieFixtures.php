<?php

namespace App\DataFixtures;

use App\Entity\SousCategorieEtape;
use App\Entity\CategorieEtape;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SousCategorieFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct( 

    ){

}   
    public function load(ObjectManager $manager): void
    {
        //     $this->addSql("INSERT INTO public.sous_categorie_etape(id, categorie_etape_id, nom, duree_par_defaut) VALUES 
        //     (nextval('sous_categorie_etape_id_seq'),
        //     1,
        //     'Début',
        //     5),

        //     (nextval('sous_categorie_etape_id_seq'),
        //     1,
        //     'Fin',
        //     5),

        //     (nextval('sous_categorie_etape_id_seq'),
        //     1,
        //     'Résumé',
        //     5),
            
        //     (nextval('sous_categorie_etape_id_seq'),
        //     2,
        //     'Antagoniste',
        //     5),
            
        //     (nextval('sous_categorie_etape_id_seq'),
        //     2,
        //     'Protagoniste',
        //     5),
            
        //     (nextval('sous_categorie_etape_id_seq'),
        //     3,
        //     'Action',
        //     5),

        //     (nextval('sous_categorie_etape_id_seq'),
        //     3,
        //     'Suspense',
        //     5),
            
        //     (nextval('sous_categorie_etape_id_seq'),
        //     3,
        //     'Romantique',
        //     5),

        //     (nextval('sous_categorie_etape_id_seq'),
        //     3,
        //     E'Dialogue pour faire avancer l\'intrigue',
        //     5),
            
        //     (nextval('sous_categorie_etape_id_seq'),
        //     3,
        //     'Dramatique',
        //     5),
            
        //     (nextval('sous_categorie_etape_id_seq'),
        //     3,
        //     'Rebondissements',
        //     5),
            
        //     (nextval('sous_categorie_etape_id_seq'),
        //     4,
        //     E'Lieu de l\'intrigue',
        //     5),

        //     (nextval('sous_categorie_etape_id_seq'),
        //     4,
        //     E'Contexte de l\'époque (politique...)',
        //     5)

        // ");
    
        // $this->addSql("INSERT INTO public.sous_categorie_etape(id, categorie_etape_id, nom, duree_par_defaut) VALUES 
        
        $tabSousCategorie=["0" => array(
                                        "categorie_etape_id" => '1',
                                        "nom" => "Début",
                                        "duree_par_defaut" => 5,
                                        "explication" => "Ecrire le début d'une histoire"
                            ),

                       "1" => array(
                                        "categorie_etape_id" => '1',
                                        "nom" => "Fin",
                                        "duree_par_defaut" => 5,
                                        "explication" => "Ecrire la fin d'une histoire"
                            ),

                       "2" => array(
                                        "categorie_etape_id" => '1',
                                        "nom" => "Résumé",
                                        "duree_par_defaut" => 5,
                                        "explication" => "Ecrire un résumé"
                            ),

                       "3" => array(
                                        "categorie_etape_id" => '2',
                                        "nom" => "Antagoniste",
                                        "duree_par_defaut" => 5,
                                        "explication" => "Décrire l'antagoniste du personnage principal"
                            ),

                       "4" => array(
                                        "categorie_etape_id" => '2',
                                        "nom" => 'Protagoniste',
                                        "duree_par_defaut" => 5,
                                        "explication" => "Décrire le personnage principal"
                            ),
                    
                      "5" => array(
                                        "categorie_etape_id" => '3',
                                        "nom" => 'Action',
                                        "duree_par_defaut" => 5,
                                        "explication" => "Ecrire une scène d'action"
                      ),
                    
                      "6" => array(
                                        "categorie_etape_id" => '3',
                                        "nom" => 'Suspense',
                                        "duree_par_defaut" => 5,
                                        "explication" => "Ecrire une scène de suspens"
                      ),

                      "7" => array(
                                        "categorie_etape_id" => '3',
                                        "nom" => 'Romantique',
                                        "duree_par_defaut" => 5,
                                        "explication" => "Ecrire une scène romantique"
                      ),

                      "8" => array(
                                        "categorie_etape_id" => '3',
                                        "nom" => "Dialogue pour faire avancer l'intrigue",
                                        "duree_par_defaut" => 5,
                                        "explication" => "Ecrire un dialogue pour faire avancer l'intrigue"

                      ),

                      "9" => array(
                                        "categorie_etape_id" => '3',
                                        "nom" => 'Dramatique',
                                        "duree_par_defaut" => 5,
                                        "explication" => "Ecrire une scène dramatique, émouvante"
                      ),


                      "10" => array(
                                        "categorie_etape_id" => '3',
                                        "nom" => 'Rebondissements',
                                        "duree_par_defaut" => 5,
                                        "explication" => "Ecrire un rebondissement"
                      ),


                      "11" => array(
                        "categorie_etape_id" => '4',
                        "nom" => "Lieu de l'intrigue",
                        "duree_par_defaut" => 5,
                        "explication" => "Décrire le lieu de l'intrigue"
                      ),


                      "12" => array(
                        "categorie_etape_id" => '4',
                        "nom" => "Contexte de l'époque (politique...)",
                        "duree_par_defaut" => 5,
                        "explication" => "Décrire le contexte de l'époque (politique...)"
                      ),
        ];

        for ($i=0; $i<count($tabSousCategorie); $i++)        
        {
            $sousCategorie = new SousCategorieEtape();
                  
            $categorie = $this->getReference('categorie-'.$tabSousCategorie[$i]["categorie_etape_id"],CategorieEtape::class) ;
            $sousCategorie->setCategorieEtape($categorie)
                        ->setNom($tabSousCategorie[$i]["nom"])
                        ->setDureeParDefaut($tabSousCategorie[$i]["duree_par_defaut"])
                        ->setExplication($tabSousCategorie[$i]["explication"]);
           $manager->persist($sousCategorie);
        }
      
        $manager->flush();
    }

    function getDependencies():array
    {
        //Pour l'implémentation de l'interface permettant de modifier l'ordre d'exécutiond des fixtures (catégorie avant souscategorie)
        return [CategorieFixtures::class];
    }
}
    
