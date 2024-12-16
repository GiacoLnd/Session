<?php

namespace App\Repository;

use App\Entity\FormModule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FormModule>
 */
class FormModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormModule::class);
    }

    //    /**
    //     * @return FormModule[] Returns an array of FormModule objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?FormModule
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findModuleWithDuration(int $moduleId): ?array
    {
        return $this->createQueryBuilder('fm')
            ->select('fm', 'p.duree')
            ->leftJoin('fm.programmes', 'p')
            ->where('fm.id = :moduleId')
            ->setParameter('moduleId', $moduleId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
