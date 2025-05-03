<?php

namespace App\Repository;

use App\Entity\Notation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use App\Repository\PartieJoueurRepository;
use App\Repository\RedactionRepository;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Notation>
 */
class NotationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private ContainerBagInterface $parameterBag)
    {
        parent::__construct($registry, Notation::class);
    }

    public function findNotationForEpreuveByJoueur($noteur, $epreuve)
    {
            return $this->createQueryBuilder('n')
            ->join('n.redaction', 'r')
            ->andWhere('n.noteur = :noteur')
            ->setParameter('noteur', $noteur)
            ->andWhere('r.partieEpreuve = :epreuve')
            ->setParameter('epreuve', $epreuve)
            ->getQuery()
            ->getResult()
        ;
    }

   //Retourne le nombre de notes enregistrées pour une épreuve 
   public function getNumberNoteForTheEvent ($partieEpreuve, int $nbreJoueur, RedactionRepository $ReposRedaction):int
   {
        //Vérifie que l'épreuve est terminée (donc en cours de notation):
        $etatEpreuveTerminee = $this->parameterBag->get("CONST_ETAT_EPREUVE__NOTATION_DEMARREE");
        $etatEpreuveNotee = $this->parameterBag->get("CONST_ETAT_EPREUVE__NOTATION_TERMINEE");

        //Calcul du nombre de notes TOTAL une fois l'épreuve notée = nbre_joueurs * nbre_joueurs - 1
        $nbreNotesFinales = $nbreJoueur * ($nbreJoueur - 1);
                
        //Compte le nombre de notes déjà reçues pour chaque rédaction
        if ($partieEpreuve->getEtatEpreuve()->getNom() === $etatEpreuveTerminee)
        {
            //Récupération de la liste des rédactions de la dernière épreuve.
            $listeRedaction = $ReposRedaction->getListOfAllRedactionForAEpreuve($partieEpreuve);
            
            //Création d'une requête de comptage de toutes les notes de toutes les rédactions.
            $comptageNote = $this->createQueryBuilder('n');
            $comptageNote->select('COUNT(n.id)');

            for ($i=0; $i < count($listeRedaction); $i++)
            {
                $redactionCourante = $listeRedaction[$i];

                $comptageNote->orWhere("n.redaction = :param_$i");
                $comptageNote->setParameter("param_$i", $redactionCourante->getId());
            }
            $nbreNoteDejaRecue = $comptageNote->getQuery()->getSingleScalarResult();

            return $nbreNoteDejaRecue;
        }

        elseif($partieEpreuve->getEtatEpreuve()->getNom() === $etatEpreuveNotee)
        {
            //Chaque joueur note les autres et pas lui-même
            return $nbreJoueur * ($nbreJoueur - 1);
        }

        //Si l'épreuve n'est pas dans l'état terminé ou noté, personne n'a encore noté l'épreuve...
        else
        {
            return 0;
        }
   }

   //Calcul les scores d'une épreuve + MAJ de la table Redaction
   public function calculScoreForAEpreuve($epreuve,  RedactionRepository $ReposRedaction, EntityManagerInterface $em):void
   {
         //Récupération de la liste des rédactions pour une épreuve.
         $listeRedactions = $ReposRedaction->getListOfAllRedactionForAEpreuve($epreuve);
                  
         foreach($listeRedactions as $redaction)
         {
            //Somme de toutes les notes
            $sommeNote = $this->createQueryBuilder('n')
                        ->select('SUM(n.note) as somme, COUNT(n.note) as nombreNote')
                        ->Where("n.redaction = :idRedaction")
                        ->setParameter("idRedaction", $redaction->getId())
                        ->getQuery()
                        ->getResult();
            
            //Si pas de note, on évite la division par 0 !
            if ($sommeNote[0]["nombreNote"] > 0)
            {
                $noteFinale = round($sommeNote[0]["somme"] / $sommeNote[0]["nombreNote"], 1);
            }
            else
            {
                $noteFinale = 0;
            }
            
            $redaction->setScore($noteFinale);
            $em->persist( $redaction);
            $em->flush();
         }
   }

    //    /**
    //     * @return Notation[] Returns an array of Notation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Notation
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
