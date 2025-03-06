<?php

namespace App\Repository;

use App\Entity\EvaluationReclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EvaluationReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EvaluationReclamation::class);
    }

    // Méthode pour trouver une évaluation par la réclamation et l'utilisateur
    public function findByReclamationAndUser($reclamation, $user)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.reclamation = :reclamation')
            ->andWhere('e.voyageur = :user')
            ->setParameter('reclamation', $reclamation)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // Ajoute d'autres méthodes personnalisées selon les besoins
}
