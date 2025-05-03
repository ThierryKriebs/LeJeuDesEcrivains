<?php

namespace App\Repository;

use App\Entity\EpreuveEtat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EpreuveEtat>
 */
class EpreuveEtatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EpreuveEtat::class);
    }

    //Retourne un EpreuveEtat en fonction de son nom
    public function findOneEpreuveEtat($nom): ?EpreuveEtat
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.nom = :val')
            ->setParameter('val', $nom)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //    /**
    //     * @return EpreuveEtat[] Returns an array of EpreuveEtat objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?EpreuveEtat
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
