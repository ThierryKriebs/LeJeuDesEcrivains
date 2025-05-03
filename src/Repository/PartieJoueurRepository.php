<?php

namespace App\Repository;

use App\Entity\PartieJoueur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PartieJoueur>
 */
class PartieJoueurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartieJoueur::class);
    }
    
        //Renvoie la liste des joueurs pour une partie
        public function findJoueurByIdpartie($idPartie): array
        {
            $listeJoueurs = $this->createQueryBuilder('pj')
            ->select('pj','u')
            ->leftJoin('pj.Joueur', 'u')
            ->andWhere('pj.Partie = :idPartie')
            ->setParameter('idPartie', $idPartie)
            ->orderBy('u.login', 'ASC')  
            ->getQuery()
            ->getResult()
            ;   

            return $listeJoueurs;
        }

        //Compte la liste des joueurs pour une partie
        public function countJoueurByIdpartie($idPartie): int
        {
            $nbreJoueurs= $this->createQueryBuilder('pj')
            ->select('count(pj)')
            ->andWhere('pj.Partie = :idPartie')
            ->setParameter('idPartie', $idPartie)
            ->getQuery()
            ->getSingleScalarResult()
            ;   

            return (int) $nbreJoueurs;
        }

        //Renvoie le créateur de la partie
        public function findCreateurByIdpartie($idPartie): ?PartieJoueur //array
        {
            return $this->createQueryBuilder('pj')
            ->select('pj','u')
            ->leftJoin('pj.Joueur', 'u')
            ->andWhere('pj.Partie = :idPartie')
            ->setParameter('idPartie', $idPartie)
            
            ->andWhere('pj.estCreateur = true')
           
            ->orderBy('u.login', 'ASC')  
            ->getQuery()
            ->getOneOrNullResult()
            ;   
        }

        //Renvoie true si le joueur est déjà enregistré dans la partie
        public function JoueurDejaEnregistreDansPartie ($idJoueur, $idPartie):bool
        {
            $resultat = $this->createQueryBuilder('pj')
            ->select('pj','u')
            ->leftJoin('pj.Joueur', 'u')

            ->andWhere('pj.Partie = :idPartie')
            ->setParameter('idPartie', $idPartie)
            
            ->andWhere('pj.Joueur = :idJoueur')
            ->setParameter('idJoueur', $idJoueur)

            ->orderBy('u.login', 'ASC')  
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

            if (empty($resultat)) return false;
            else return true;
        }

        
        //Cette méthode renvoie true si un joueur est le créateur de la partie. False sinon
        public function estCreateur ($idPartie, $idJoueur):bool
        {
            $resultat = $this->createQueryBuilder('pj')
            ->select('pj')
            
            ->andWhere('pj.Partie = :idPartie')
            ->setParameter('idPartie', $idPartie)
            
            ->andWhere('pj.estCreateur = true')
           
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
            
            if (empty($resultat)) return false;
            else if ($resultat[0]->getJoueur()->getId() != $idJoueur) return false;
            else return true;
        }

        //Permet de sélectionner un enregistrement dans la table PartieJoueur
        public function findEntityByIdpartieAndIdjoueur($idPartie, $idJoueur)
        {
            $resultat = $this->createQueryBuilder('pj')
            ->select('pj')
            
            ->andWhere('pj.Partie = :idPartie')
            ->setParameter('idPartie', $idPartie)
            
            ->andWhere('pj.Joueur = :idJoueur')
            ->setParameter('idJoueur', $idJoueur)
           
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

           return $resultat[0];
        }

        //Calcul du classement final + MAJ table partie_joueur
        public function calculClassementFinal($partie,$em):void
        {
            $listeScoreFinaux = $this->createQueryBuilder('pj')
                             ->select('pj.id, pj.score')
                             ->where('pj.Partie = :partieId')
                             ->setParameter('partieId', $partie)
                             ->orderBy('pj.score', 'DESC')
                             ->getQuery()
                             ->getResult();
          
            $classement = 0;
            $scoreOld = -1 ; //Si jamais 2 joueurs ont le même score => même classement

            //Enregistrement du classement dans la table partie_joueur
            for ($i=0; $i < count($listeScoreFinaux); $i++)
            {
               $idjoueur = $listeScoreFinaux[$i]["id"];
               $scoreCourant = $listeScoreFinaux[$i]["score"];

               if ($scoreCourant != $scoreOld)
               {
                  $classement++;
               }

               $unePartieJoueur = $em->getRepository(PartieJoueur::class)->find($idjoueur);
               $unePartieJoueur->setClassement($classement);

               $em->persist( $unePartieJoueur);
               $em->flush();

               $scoreOld = $scoreCourant;
            }
      }

      //Renvoie le classement final de la partie
      public function getClassementFinal($partie,$em): array
      {
          $classementFinal = $this->createQueryBuilder(('pj'))
                             ->select('j.login, pj.score, pj.classement')
                             ->leftJoin('pj.Joueur', 'j')
                             
                             ->where('pj.Partie = :partieId')
                             ->setParameter('partieId', $partie)
                             
                             ->OrderBy('pj.classement', 'ASC')
                             ->addOrderBy('j.login', 'ASC')
                             
                             ->getQuery()
                             ->getResult()
                             ;
        
        return $classementFinal;
     }
}
