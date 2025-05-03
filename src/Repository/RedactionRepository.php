<?php

namespace App\Repository;

use App\Entity\Redaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;


/**
 * @extends ServiceEntityRepository<Redaction>
 */
class RedactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private ContainerBagInterface $parameterBag)
    {
        parent::__construct($registry, Redaction::class);
    }

    //Recherche si le joueur a déjà écrit une rédaction pour une épreuve
    public function findRedactionForEpreuveByJoueur($joueur, $epreuve): ?Redaction
   {
       return $this->createQueryBuilder('r')
           ->andWhere('r.joueur = :joueur')
           ->setParameter('joueur', $joueur)
           ->andWhere('r.partieEpreuve = :epreuve')
           ->setParameter('epreuve', $epreuve)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }

   //Retourne une rédaction à partir de son Id
   public function getredactionById ($redactionId): ?Redaction
   {
       return $this->createQueryBuilder('r')
           ->andWhere('r.id = :id')
           ->setParameter('id', $redactionId)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }

   //Pour un joueur donné, pour une épreuve donnée, retourne toutes les rédactions qu'il doit noter
   public function getListRedactionMustBeNoteFromAPartieEpreuveFromAPlayer ($joueurId, $partieEpreuveId)
   {
        $etatEpreuveTermine = $this->parameterBag->get("CONST_ETAT_EPREUVE__NOTATION_DEMARREE");

        //Sélectionne l'ID correspondant à l'état EpreuveTermine  
        $idEtatEpreuveTerminee = $this->createQueryBuilder('r')
        ->join('r.partieEpreuve', 'pe')
        ->join('pe.etatEpreuve', 'ee')
        ->select("ee.id")
        ->andWhere('r.joueur = :joueurId')
        ->setParameter('joueurId', $joueurId)
        ->andWhere('ee.nom = :etatEpreuveTermine')
        ->setParameter('etatEpreuveTermine', $etatEpreuveTermine)
        ->getQuery()
        ->getOneOrNullResult();

        $listeRedaction = $this->createQueryBuilder('r')
        ->select('r','pe')
        ->leftJoin('r.partieEpreuve', 'pe')
        ->andWhere('r.joueur != :joueurId')
        ->setParameter('joueurId', $joueurId)
        ->andWhere('r.partieEpreuve = :partieEpreuve')
        ->setParameter('partieEpreuve', $partieEpreuveId)
        ->andWhere('pe.etatEpreuve = :etatEpreuveTermine')
        ->setParameter('etatEpreuveTermine',$idEtatEpreuveTerminee["id"])
        ->orderBy('r.id', 'DESC')
        ->getQuery()
        ->getResult()
        ;

        shuffle($listeRedaction); //Mélange l'ordre des rédactions à noter
        
        return $listeRedaction;
   }


   //Retourne le nombre de joueurs ayant terminés l'épreuve (rendus une rédaction)
   public function getNumberPlayerWhoCompletedTheEvent ($partieEpreuveId):int
   {
      $nbreJoueurEventCompleted = $this->createQueryBuilder('r')
        ->select('COUNT(r.id)')
        ->Where('r.partieEpreuve = :val')
        ->setParameter('val', $partieEpreuveId)
        ->getQuery()
        ->getSingleScalarResult();

      return $nbreJoueurEventCompleted;
   }

   //Renvoie la liste de toutes les rédactions pour une épreuve donnée
   public function getListOfAllRedactionForAEpreuve ($partieEpreuveId): array
   {
     $listeRedaction = $this->createQueryBuilder('r')
        ->select('r')
        ->Where('r.partieEpreuve = :val')
        ->setParameter('val', $partieEpreuveId)
        ->orderBy('r.id', 'ASC')
        ->getQuery()
        ->getResult()
        ;
    
    return $listeRedaction;
   }


   public function creationClassementForAEpreuve($partieEpreuve, $em): void
   {
        $classementParEpreuve = $this->createQueryBuilder('r')
            ->select('r.id, r.score')
            ->leftJoin('r.partieEpreuve', 'pe')
            ->Where('pe.id = :partieEpreuve')
            ->setParameter('partieEpreuve', $partieEpreuve->getId())
            ->orderBy('r.score', 'DESC')
            ->getQuery()
            ->getResult()
            ;
        
        $classement = 0;
        $scoreOld = -1 ; //Si jamais 2 joueurs ont le même score => même classement

        //Enregistrement du classement dans la table rédaction
        for ($i=0; $i < count($classementParEpreuve); $i++)
        {
            $idRedaction = $classementParEpreuve[$i]["id"];
            $scoreCourant = $classementParEpreuve[$i]["score"];

            if ($scoreCourant != $scoreOld)
            {
                $classement++;
            }

            $uneRedaction = $em->getRepository(Redaction::class)->find($idRedaction);
            $uneRedaction->setClassement($classement);

            $em->persist( $uneRedaction);
            $em->flush();

            $scoreOld = $scoreCourant;
        }
   }

   //Renvoie le classement par épreuve, pour toutes les épreuves
   public function getclassementByRound ($partie): array
   {
        $classementParEpreuve = $this->createQueryBuilder('r')
            ->select('pe.num_etape, sc.explication, j.login, r.redaction, r.classement, r.score, r.id as redactionId')
            ->leftJoin('r.partieEpreuve', 'pe')
            ->leftJoin('r.joueur', 'j')
            ->leftJoin('pe.sous_categorie', 'sc')
            ->leftJoin('pe.partie', 'p')
            ->Where('pe.partie = :partie')
            ->setParameter('partie', $partie)
            ->orderBy('pe.num_etape, r.classement, j.login', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        
        //Récupération des différentes Notes et injection $classementParEpreuve
        for ($i=0; $i < count($classementParEpreuve); $i++ )
        {
            $redactionId = $classementParEpreuve[$i]["redactionId"];
            
            $lesNotes = $this->createQueryBuilder('r')
                        ->select('j.login as noteur, n.note, n.remarque')
                        ->leftJoin('r.notations', 'n')
                        ->leftJoin('n.noteur', 'j')
                        ->Where('r.id = :redactionId')
                        ->setParameter('redactionId', $redactionId)
                        ->getQuery()
                        ->getResult();

            $tabNotes = [];
            foreach($lesNotes as $UneNote)
            {
                $noteurCourant = $UneNote["noteur"];
                $noteCourante = $UneNote["note"];
                $remarqueCourante = $UneNote["remarque"];
                $tabNotes[$noteurCourant] = $noteCourante;
                $tabRemarques[$noteurCourant] = $remarqueCourante;
            }
            $classementParEpreuve[$i]["liste_notes"] = $tabNotes;
            $classementParEpreuve[$i]["liste_remarques"] = $tabRemarques;
        }

        return $classementParEpreuve;
   }
}
