<?php

namespace App\Repository;

use App\Entity\PartieEpreuve;
use App\Entity\PartieJoueur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @extends ServiceEntityRepository<PartieEpreuve>
 */
class PartieEpreuveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartieEpreuve::class);
    }

    //Retourne les informations de la dernière épreuve enregistrée d'une partie
    public function FindLastEpreuve($partie): PartieEpreuve
    {
        $resultat =  $this->createQueryBuilder('p')
               ->andWhere('p.partie = :val')
               ->setParameter('val', $partie->getId())
               ->orderBy('p.num_etape', 'DESC')
               ->getQuery()
               ->getResult();
        
        return $resultat[0];
    }

    public function  CompteNbreEpreuveDejaJoue($partie, $EtatEpreuveTerminee): Int
    {
        return $this->createQueryBuilder('e')
        ->select('COUNT(e.id)')
        ->Where('e.partie = :val')
        ->setParameter('val', $partie->getId())
        ->andWhere('e.etatEpreuve = :val2')
        ->setParameter('val2', $EtatEpreuveTerminee)
        ->getQuery()
        ->getSingleScalarResult();
    }

    //Nombre d'épreuves que contient une partie qq soit leur état (en cours, terminée, notée...)
    public function  CompteNbreEpreuve($partie): Int
    {
        $requete = $this->createQueryBuilder('e')
            ->select('count(e.id)')
            ->Where('e.partie = :valPartie')
            ->setParameter('valPartie', $partie->getId())
            ->getQuery()
            ->getSingleScalarResult();
        
        return $requete;
    }

    //Pour une partie donnée, liste des sous-categories déjà jouées ou en cours de jeu
    public function ListeEpreuveDejaJoue ($partie): array
    {
        $resultat = $this->createQueryBuilder('e')
        ->Where('e.partie = :val')
        ->setParameter('val', $partie->getId())
        ->getQuery()
        ->getResult();

       //Filtrage des résultats pour ne récupérer QUE les sous-catégories:
        $listeSousCategorieDejaJoue=[];
        for ($i=0; $i < count($resultat); $i++)
        {
            array_push($listeSousCategorieDejaJoue, $resultat[$i]->getSousCategorie()->getId());
        }

        return $listeSousCategorieDejaJoue; 
    }

     //Calcul du score final de chaque joueur + MAJ table partie_joueur
     public function calculScoreFinal ($partie, $em):void
     {
         $listeScoresFinaux = $this->createQueryBuilder('pe')
                     ->select('j.id,  j.login, SUM(r.score) as ScoreTotal')  
                     ->leftJoin('pe.redactions', 'r')
                     ->leftJoin('pe.partie', 'p')
                     ->leftJoin('r.joueur', 'j')

                     ->andWhere('p.id = :idPartie')
                     ->setParameter('idPartie', $partie)
                                    
                     ->groupBy('j.login, j.id')
                     ->getQuery()
                     ->getResult();

        foreach ($listeScoresFinaux as $unScore)
        {
            $joueurId = $unScore["id"];
            $score = round($unScore["ScoreTotal"],1); 

            if (!is_null($joueurId))        //Se produit si il y a eu une erreur lors d'un enregistrement précédent
            {
                //Recherche de la partieJoueur correspondante
                $partiejoueur = $this->createQueryBuilder('pe')
                ->select('DISTINCT pj.id')
                ->leftJoin('pe.partie', 'p')
                ->leftJoin('p.partieJoueurs', 'pj')
                
                ->andWhere('p.id = :idPartie')
                ->setParameter('idPartie', $partie)
                
                ->andWhere('pj.Joueur = :joueurId')
                ->setParameter('joueurId', $joueurId)
                ->getQuery()
                ->getSingleScalarResult();

                $objetPartieJoueur = $em->getRepository(PartieJoueur::class)->find($partiejoueur);

                //MAJ du score en base
                $objetPartieJoueur->setScore($score);
                $em->persist( $objetPartieJoueur);
                $em->flush();
            }
        }
     }
}
