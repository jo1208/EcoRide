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
            ->where('c.date_depart >= :today')
            ->andWhere('c.statut IS NULL OR c.statut != :annule') // ✅ Ne pas afficher les trajets annulés
            ->setParameter('today', (new \DateTime())->setTime(0, 0))
            ->setParameter('annule', 'Annulé')
            ->orderBy('c.date_depart', 'ASC');

        if (!empty($filters['lieu_depart'])) {
            $qb->andWhere('c.lieu_depart LIKE :lieu_depart')
                ->setParameter('lieu_depart', '%' . $filters['lieu_depart'] . '%');
        }

        if (!empty($filters['lieu_arrivee'])) {
            $qb->andWhere('c.lieu_arrivee LIKE :lieu_arrivee')
                ->setParameter('lieu_arrivee', '%' . $filters['lieu_arrivee'] . '%');
        }

        if (!empty($filters['date'])) {
            $date = new \DateTime($filters['date']);
            $date->setTime(0, 0); // Mets aussi à 00:00 pour bien comparer
            $qb->andWhere('c.date_depart = :date')
                ->setParameter('date', $date);
        }

        if (!empty($filters['prix_max'])) {
            $qb->andWhere('c.prix_personne <= :prix_max')
                ->setParameter('prix_max', $filters['prix_max']);
        }

        if (!empty($filters['note_min'])) {
            $qb->join('c.conducteur', 'u')
                ->andWhere('u.note >= :note_min')
                ->setParameter('note_min', $filters['note_min']);
        }

        if (!empty($filters['ecologique'])) {
            $qb->andWhere('v.ecologique = true');
        }

        return $qb->getQuery()->getResult();
    }

    public function findFirstAvailable(): ?Covoiturage
    {
        return $this->createQueryBuilder('c')
            ->where('c.date_depart >= :now')
            ->andWhere('c.nb_place > 0')
            ->andWhere('c.statut IS NULL OR c.statut != :annule') // ✅ Important aussi ici !
            ->setParameter('now', new \DateTime())
            ->setParameter('annule', 'Annulé')
            ->orderBy('c.date_depart', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findFirstAvailableMatchingLocation(array $filters): ?Covoiturage
    {
        return $this->createQueryBuilder('c')
            ->where('c.lieu_depart = :lieu_depart')
            ->andWhere('c.lieu_arrivee = :lieu_arrivee')
            ->andWhere('c.date_depart > :today')
            ->andWhere('c.nb_place > 0')
            ->andWhere('c.statut IS NULL OR c.statut != :annule')
            ->setParameter('lieu_depart', $filters['lieu_depart'])
            ->setParameter('lieu_arrivee', $filters['lieu_arrivee'])
            ->setParameter('today', new \DateTime())
            ->setParameter('annule', 'Annulé')
            ->orderBy('c.date_depart', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
