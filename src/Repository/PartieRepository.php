<?php

namespace App\Repository;

use App\Entity\Partie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\PartieEtatRepository;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

/**
 * @extends ServiceEntityRepository<Partie>
 */
class PartieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private ContainerBagInterface $parameterBag, private PartieEtatRepository $ReposPartieEtat)
    {
        parent::__construct($registry, Partie::class);
    }

    //Renvoie un objet partie en fonction de son code unique de partie.
    public function getPartieByCode($code): ?Partie 
    { 
      $unePartie = $this->createQueryBuilder('p')
        ->andWhere('p.code_connexion = :val')
        ->setParameter('val', $code)
        ->getQuery()
        ->getOneOrNullResult();
              
        return $unePartie;
    }

    //Renvoie true si le joueur est déjà inscrit dans une partie avec le statut "En cours de connexion" (peu importe qu'il soit le créateur ou non)
    public function JoueurADejaUnePartieEnCoursDeConnexion ($idJoueur):array
    {
        $codeEtatPartieEnCours = $this->ReposPartieEtat->findIdEtatPartieByNom($this->parameterBag->get('CONST_ETAT_PARTIE__EN_COURS_DE_CONNEXION')); //plus tard il faudra gérer d'autres états (en cours!!)

        $resultat = $this->createQueryBuilder('p')
        ->select('p', 'pe', 'pj')
        ->leftJoin('p.etat', 'pe')
        ->leftJoin('p.partieJoueurs', 'pj')

        ->andWhere('pj.Joueur = :variableidJoueur')
        ->setParameter('variableidJoueur', $idJoueur)

        ->andWhere('p.etat = :codePartieEnCours')
        ->setParameter('codePartieEnCours', $codeEtatPartieEnCours)
        
        ->setMaxResults(1)
        ->getQuery()
        ->getResult();

        return $resultat;
    }

     //Renvoie true si le joueur est déjà inscrit dans une partie qui a pour type $typePartie (peu importe qu'il soit le créateur ou non)
     public function JoueurADejaUnePartieDeCeType ($idJoueur, $typePartie):array
     {
        $codeEtatPartieEnCours = $this->ReposPartieEtat->findIdEtatPartieByNom("En cours");

         $resultat = $this->createQueryBuilder('p')
         ->select('p', 'pe', 'pj')
         ->leftJoin('p.etat', 'pe')
         ->leftJoin('p.partieJoueurs', 'pj')
 
         ->andWhere('pj.Joueur = :variableidJoueur')
         ->setParameter('variableidJoueur', $idJoueur)
 
         ->andWhere('p.etat = :codePartieEnCours')
         ->setParameter('codePartieEnCours', $codeEtatPartieEnCours)
         
         ->setMaxResults(1)
         ->getQuery()
         ->getResult();
 
         return $resultat;
     }
}
