<?php

namespace App\Repository;

use App\Entity\LongueurPartie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LongueurPartie>
 */
class LongueurPartieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LongueurPartie::class);
    }

    public function findLongueurPartieByNom($nom): ?LongueurPartie
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.nom = :val')
            ->setParameter('val', $nom)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //    /**
    //     * @return LongueurPartie[] Returns an array of LongueurPartie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?LongueurPartie
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
