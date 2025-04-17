<?php

namespace App\Repository;

use App\Entity\Covoiturage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CovoiturageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Covoiturage::class);
    }

    /**
     * Recherche des trajets selon les filtres de recherche
     */
    public function findWithFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.Voiture', 'v')
            ->addSelect('v')
            ->where('c.date_depart >= :today') // ✅ N'afficher que les trajets futurs
            ->setParameter('today', new \DateTime())
            ->orderBy('c.date_depart', 'ASC'); // ✅ Tri du plus proche au plus lointain

        if (!empty($filters['lieu_depart'])) {
            $qb->andWhere('c.lieu_depart LIKE :lieu_depart')
                ->setParameter('lieu_depart', '%' . $filters['lieu_depart'] . '%');
        }

        if (!empty($filters['lieu_arrivee'])) {
            $qb->andWhere('c.lieu_arrivee LIKE :lieu_arrivee')
                ->setParameter('lieu_arrivee', '%' . $filters['lieu_arrivee'] . '%');
        }

        if (!empty($filters['date'])) {
            $qb->andWhere('c.date_depart = :date')
                ->setParameter('date', new \DateTime($filters['date']));
        }

        if (!empty($filters['prix_max'])) {
            $qb->andWhere('c.prix_personne <= :prix_max')
                ->setParameter('prix_max', $filters['prix_max']);
        }

        if (!empty($filters['duree_max'])) {
            $qb->andWhere('(HOUR(c.heure_arrivee) * 60 + MINUTE(c.heure_arrivee)) - (HOUR(c.heure_depart) * 60 + MINUTE(c.heure_depart)) <= :duree_max')
                ->setParameter('duree_max', $filters['duree_max']);
        }

        if (!empty($filters['note_min'])) {
            $qb->andWhere('v.note >= :note_min')
                ->setParameter('note_min', $filters['note_min']);
        }

        if (!empty($filters['ecologique'])) {
            $qb->andWhere('v.ecologique = true');
        }

        return $qb->getQuery()->getResult();
    }
}
