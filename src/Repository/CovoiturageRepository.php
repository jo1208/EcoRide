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
            ->andWhere('c.statut IS NULL') // ✅ Ne pas afficher les trajets annulés
            ->setParameter('today', (new \DateTime())->setTime(0, 0))
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
            $date->setTime(0, 0);
            $qb->andWhere('c.date_depart = :date')
                ->setParameter('date', $date);
        }

        if (!empty($filters['prix_max'])) {
            $qb->andWhere('c.prix_personne <= :prix_max')
                ->setParameter('prix_max', $filters['prix_max']);
        }

        if (!empty($filters['ecologique'])) {
            $qb->andWhere('v.ecologique = true');
        }

        // 👉 On récupère d'abord tous les covoiturages filtrés "classiques"
        $covoiturages = $qb->getQuery()->getResult();

        // 👉 Maintenant, filtrage PHP pour la note du conducteur
        if (!empty($filters['note_min'])) {
            $noteMin = (float) $filters['note_min'];

            $covoiturages = array_filter($covoiturages, function ($covoiturage) use ($noteMin) {
                $conducteur = $covoiturage->getConducteur();
                if (!$conducteur) {
                    return false;
                }
                $noteMoyenne = $conducteur->getNoteMoyenne();
                return $noteMoyenne !== null && $noteMoyenne >= $noteMin;
            });
        }

        return $covoiturages;
    }


    public function findFirstAvailableMatchingLocation(array $filters): ?Covoiturage
    {
        return $this->createQueryBuilder('c')
            ->where('c.lieu_depart = :lieu_depart')
            ->andWhere('c.lieu_arrivee = :lieu_arrivee')
            ->andWhere('c.date_depart > :today')
            ->andWhere('c.nb_place > 0')
            ->andWhere('c.statut IS NULL')
            ->setParameter('lieu_depart', $filters['lieu_depart'])
            ->setParameter('lieu_arrivee', $filters['lieu_arrivee'])
            ->setParameter('today', new \DateTime())
            ->orderBy('c.date_depart', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
