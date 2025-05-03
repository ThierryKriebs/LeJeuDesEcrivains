<?php

namespace App\Repository;

use App\Entity\PartieEtat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @extends ServiceEntityRepository<PartieEtat>
 */
class PartieEtatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartieEtat::class);
    }


    //Retourne l'ID correspondant à un Etat de la partie (nom d'un état)
    public function findIdEtatPartieByNom($nom): ?Int
    {
        $etat = $this->createQueryBuilder('e')
                ->andWhere('e.nom = :val')
                ->setParameter('val', $nom)
                ->getQuery()
                ->getOneOrNullResult();
        
        return $etat->getId();
    }

    //Retourne l'ID correspondant à un Etat de la partie (nom d'un état)
    public function findEtatPartieByNom($nom): ?PartieEtat
    {
        return $this->createQueryBuilder('e')
                ->andWhere('e.nom = :val')
                ->setParameter('val', $nom)
                ->getQuery()
                ->getOneOrNullResult();
    }

    //    /**
    //     * @return PartieEtat[] Returns an array of PartieEtat objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PartieEtat
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
