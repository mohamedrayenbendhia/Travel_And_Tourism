<?php

namespace App\Repository;

use App\Entity\Reclamation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

    /**
     * Méthode pour trouver les réclamations d'un utilisateur par statut
     *
     * @param string $statut
     * @param int $userId
     * @return Reclamation[]
     */
    public function findByStatutAndUser(string $statut, int $userId)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.statut = :statut')
            ->andWhere('r.user = :userId')
            ->setParameter('statut', $statut)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Méthode pour trouver les réclamations d'un utilisateur par son rôle
     *
     * @param string $role
     * @return Reclamation[]
     */
    public function findByRole(string $role)
    {
        return $this->createQueryBuilder('r')
            ->innerJoin(User::class, 'u', 'WITH', 'r.user = u.id')  // Jointure avec l'entité User
            ->where(':role MEMBER OF u.roles')  // Vérifie si le rôle est dans les rôles de l'utilisateur
            ->setParameter('role', $role)
            ->getQuery()
            ->getResult();
    }

    /**
     * Méthode pour trouver toutes les réclamations en cours
     *
     * @return Reclamation[]
     */
    public function findAllInProgress()
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.statut = :statut')
            ->setParameter('statut', 'en cours')
            ->orderBy('r.dateSoumission', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Méthode pour trouver une réclamation par son ID
     *
     * @param int $id
     * @return Reclamation|null
     */
    public function findOneById(int $id): ?Reclamation
    {
        return $this->find($id);
    }

    /**
     * Méthode pour compter les réclamations par statut
     *
     * @param string $statut
     * @return int
     */
    public function countByStatut(string $statut): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.statut = :statut')
            ->setParameter('statut', $statut)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // D'autres méthodes personnalisées pour vos besoins
}
