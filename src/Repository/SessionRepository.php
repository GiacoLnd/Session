<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    //    /**
    //     * @return Session[] Returns an array of Session objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Session
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findCurrentSession(){
        $currentDate = new \datetime();
        return $this->createQueryBuilder('s')
            ->where('s.dateDebutSession <= :date')
            ->andWhere('s.dateFinSession >= :date')
            ->setParameter('date', $currentDate)
            ->getQuery()
            ->getResult();
    }
    public function findComingSession(){
        $currentDate = new \datetime();
        return $this->createQueryBuilder('s')
            ->where('s.dateDebutSession > :date')
            ->setParameter('date', $currentDate)
            ->getQuery()
            ->getResult();
    }
    public function findPassedSession(){
        
        $currentDate = new \datetime();
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateFinSession < :date')
            ->setParameter('date', $currentDate)
            ->getQuery()
            ->getResult();
    }



}
