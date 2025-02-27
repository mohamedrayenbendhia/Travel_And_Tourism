<?php
// src/Repository/VlogRepository.php
namespace App\Repository;

use App\Entity\Vlog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VlogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vlog::class);
    }

    // Your custom methods go here
}
