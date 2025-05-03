<?php

namespace App\Repository;

use App\Entity\GenreLitteraire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GenreLitteraire>
 */
class GenreLitteraireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GenreLitteraire::class);
    }

    public function getGenreLitteraireByNom(): array
    {
      return $this->createQueryBuilder('g')
      ->orderBy('g.nom', 'ASC')
      ->getQuery()
      ->getResult();
    }

    //Liste des genres litteraires actifs (pas désactité par les admin)
    public function getGenreLitteraireActifByNom(): array
    {
      return $this->createQueryBuilder('g')
      ->andWhere('g.est_active = True')
      ->orderBy('g.nom', 'ASC')
      ->getQuery()
      ->getResult();
    }

    //Retour le genre littéraire par défaut
    public function getGenreLitteraireParDefautByNom($nom): ?GenreLitteraire
    {
      return $this->createQueryBuilder('g')
      ->andWhere('g.nom = :leNom')
      ->setParameter('leNom', $nom)
      ->getQuery()
      ->getOneOrNullResult();
    }


    //    /**
    //     * @return GenreLitteraire[] Returns an array of GenreLitteraire objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?GenreLitteraire
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

}
