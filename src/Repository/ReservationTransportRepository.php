<?php

namespace App\Repository;

use App\Entity\ReservationTransport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReservationTransport>
 */
class ReservationTransportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservationTransport::class);
    }

    public function getTransportAvailability(int $transportId): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.start_date, r.end_date')
            ->where('r.transport_id = :transportId')
            ->setParameter('transportId', $transportId)
            ->getQuery()
            ->getResult();
    }


    public function getTransportAvailabilityForCurrentDate(int $transportId): bool
    {
        $currentDate = new \DateTime();
        $currentDate = $currentDate->format('Y-m-d H:i:s');

        $result = $this->createQueryBuilder('r')
            ->select('r.start_date, r.end_date')
            ->where('r.transport_id = :transportId')
            ->andWhere('r.start_date <= :currentDate')
            ->andWhere('r.end_date >= :currentDate')
            ->setParameter('transportId', $transportId)
            ->setParameter('currentDate', $currentDate)
            ->getQuery()
            ->getResult();

        return count($result) > 0;
    }
    

//    /**
//     * @return ReservationTransport[] Returns an array of ReservationTransport objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ReservationTransport
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
