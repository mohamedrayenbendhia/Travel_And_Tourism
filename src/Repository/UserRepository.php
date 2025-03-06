<?php
// src/Repository/UserRepository.php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    // Créer une méthode save() dans le repository si vous le souhaitez
    public function save(User $user, bool $flush = true): void
    {
        $this->_em->persist($user);  // Persister l'utilisateur
        if ($flush) {
            $this->_em->flush();  // Enregistrer dans la base de données
        }
    }
    public function findByRole(string $role): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"'.$role.'"%') // Cherche le rôle dans le tableau
            ->getQuery()
            ->getResult();
    }
    
}
