<?php

namespace App\Repository;

use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Avis>
 */
class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }

    public function findTrajetsProblemes(): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.trajet', 't')
            ->join('a.user', 'u')
            ->join('a.conducteur', 'c')
            ->where('a.trajetBienPasse = false')
            ->getQuery()
            ->getResult();
    }
}
