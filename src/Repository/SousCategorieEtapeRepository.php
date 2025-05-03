<?php

namespace App\Repository;

use App\Entity\SousCategorieEtape;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SousCategorieEtape>
 */
class SousCategorieEtapeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SousCategorieEtape::class);
    }

    //Retourne la SousCategorie dont l'ID est passée en paramètre
    public function retournerSousCategorieEpreuve($value): ?SousCategorieEtape
    {
        return $this->createQueryBuilder('s')
                ->andWhere('s.id = :idVal')
                ->setParameter('idVal', $value)
                ->getQuery()
                ->getOneOrNullResult()
        ;
    }

    //Retourne le nbre de sous-catégories
    public function retournerNbreSousCategorie(): Int
    {
        return $this->createQueryBuilder('s')
        ->select('COUNT(s.id)')
        ->getQuery()
        ->getSingleScalarResult();
    }
    
    //Retourne toutes les sous-categories dans un tableau
    public function retournerToutesLesSousCategorie(): array
    {
        return $this->createQueryBuilder('s')
        ->orderBy('s.id', 'ASC')
        ->getQuery()
        ->getResult();
    }

    //Retourne les Id de toutes les sous-categories dans un tableau
    public function retournerIdToutesLesSousCategorie(): array
    {
        $resultat =  $this->createQueryBuilder('s')
        ->select('s.id')
        ->orderBy('s.id', 'ASC')
        ->getQuery()
        ->getResult();

         //Filtrage des résultats pour ne récupérer QUE les id:
         $listeIdSousCategorieDejaJoue=[];
         for ($i=0; $i < count($resultat); $i++)
         {
             array_push($listeIdSousCategorieDejaJoue, $resultat[$i]["id"]);
         }
         return $listeIdSousCategorieDejaJoue; 
    }
}
