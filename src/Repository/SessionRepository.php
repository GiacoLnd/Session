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
        $currentDate = new \DateTime();
        return $this->createQueryBuilder('s')
            ->where('s.dateDebutSession > :date')
            ->setParameter('date', $currentDate)
            ->getQuery()
            ->getResult();
    }
    public function findPassedSession(){
        
        $currentDate = new \DateTime();
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateFinSession < :date')
            ->setParameter('date', $currentDate)
            ->getQuery()
            ->getResult();
    }
    // public function isStagiaireInSession($idSession, $idStagiaire)
    // {
    //     return $this->createQueryBuilder('sess')
    //         ->leftJoin('sess.stagiaires', 'stag')
    //         ->andWhere('sess.id = :idSession')
    //         ->andWhere('stag.id = :idStagiaire')
    //         ->setParameter('idSession', $idSession)
    //         ->setParameter('idStagiaire', $idStagiaire)
    //         ->getQuery()
    //         ->getOneOrNullResult();
    // }
    public function findNonInscrits($session_id){
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;

        $qb->select('s')
            ->from('App\Entity\Stagiaire', 's')
            ->leftJoin('s.sessions', 'se')
            ->where('se.id = :id');

        $sub = $em->createQueryBuilder();
        $sub->select('st')
            ->from('App\Entity\Stagiaire', 'st')
            ->where($sub->expr()->notIn('st.id', $qb->getDQL()))
            ->setParameter('id', $session_id)
            ->orderBy('st.nom');

        $query = $sub->getQuery();
        return $query->getResult();
    }
    public function findNonProgramme($session_id){
        $em = $this->getEntityManager();
        
        $qb = $em->createQueryBuilder();
        $qb->select('pm.id')
            ->from('App\Entity\Programme', 'p')
            ->leftJoin('p.formModule', 'pm') 
            ->where('p.session = :id');
    
        $sub = $em->createQueryBuilder();
        $sub->select('fm')
            ->from('App\Entity\FormModule', 'fm')
            ->where($sub->expr()->notIn('fm.id', $qb->getDQL())) 
            ->setParameter('id', $session_id)
            ->orderBy('fm.moduleName'); 
    
        
        $query = $sub->getQuery();
        return $query->getResult();
    }
}
