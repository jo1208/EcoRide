<?php

namespace App\Controller;

use App\Entity\Covoiturage;
use App\Form\CovoiturageType;
use App\Repository\CovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CovoiturageController extends AbstractController
{
    #[Route('/covoiturage', name: 'app_covoiturage')]
    public function index(Request $request, CovoiturageRepository $repo): Response
    {
        $filters = [
            'lieu_depart' => $request->query->get('lieu_depart'),
            'lieu_arrivee' => $request->query->get('lieu_arrivee'),
            'date' => $request->query->get('date'),
            'prix_max' => $request->query->get('prix_max'),
            'duree_max' => $request->query->get('duree_max'),
            'note_min' => $request->query->get('note_min'),
            'ecologique' => $request->query->get('ecologique'),
        ];

        $trajets = [];
        $formIncomplete = false;
        $propositionNouvelleDate = null;

        if ($filters['lieu_depart'] && $filters['lieu_arrivee'] && $filters['date']) {
            $trajets = $repo->findWithFilters($filters);

            // ✅ Filtrage durée_max en PHP
            if (!empty($filters['duree_max'])) {
                $trajets = array_filter($trajets, function ($trajet) use ($filters) {
                    $depart = $trajet->getHeureDepart();
                    $arrivee = $trajet->getHeureArrivee();

                    if (!$depart || !$arrivee) {
                        return false;
                    }

                    $minutesDepart = ($depart->format('H') * 60) + $depart->format('i');
                    $minutesArrivee = ($arrivee->format('H') * 60) + $arrivee->format('i');
                    $duree = $minutesArrivee - $minutesDepart;

                    return $duree <= $filters['duree_max'];
                });
            }

            // ✅ Si aucun trajet, proposer une nouvelle date
            if (empty($trajets)) {
                $firstAvailable = $repo->findFirstAvailable();
                if ($firstAvailable) {
                    $propositionNouvelleDate = $firstAvailable->getDateDepart();
                }
            }
        } else {
            $formIncomplete = true;
        }

        return $this->render('covoiturage/index.html.twig', [
            'trajets' => $trajets,
            'formIncomplete' => $formIncomplete,
            'propositionNouvelleDate' => $propositionNouvelleDate,
        ]);
    }




    #[Route('/covoiturage/nouveau', name: 'covoiturage_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user || !in_array('ROLE_CHAUFFEUR', $user->getRoles())) {
            $this->addFlash('danger', 'Vous devez être chauffeur pour créer un trajet.');
            return $this->redirectToRoute('app_profil');
        }

        // Vérifier les crédits
        if ($user->getCredits() < 2) {
            $this->addFlash('danger', 'Vous n\'avez pas assez de crédits pour créer un trajet. (2 crédits requis)');
            return $this->redirectToRoute('app_profil');
        }

        $trajet = new Covoiturage();
        $form = $this->createForm(CovoiturageType::class, $trajet, [
            'user' => $this->getUser(), // ✅ Passe l'utilisateur connecté ici !
        ]);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trajet->setConducteur($user);

            // Retirer 2 crédits
            $user->setCredits($user->getCredits() - 2);

            $em->persist($trajet);
            $em->flush();

            $this->addFlash('success', 'Trajet créé avec succès ✅ (2 crédits ont été déduits)');
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('covoiturage/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/profil/historique', name: 'app_historique')]
    public function historique(): Response
    {
        $user = $this->getUser();
        $trajetsConducteur = $user->getCovoituragesConduits();
        $trajetsPassager = $user->getCovoituragesEnPassager();

        return $this->render('profil/historique.html.twig', [
            'trajetsConducteur' => $trajetsConducteur,
            'trajetsPassager' => $trajetsPassager,
        ]);
    }

    #[Route('/profil/mes-trajets', name: 'app_mes_trajets')]
    public function trajetsAVenir(CovoiturageRepository $repo): Response
    {
        $user = $this->getUser();

        // Trajets à venir comme conducteur
        $trajetsConducteur = $repo->createQueryBuilder('c')
            ->where('c.conducteur = :user')
            ->andWhere('c.date_depart >= :now')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTime())
            ->orderBy('c.date_depart', 'ASC')
            ->getQuery()
            ->getResult();

        // Trajets à venir comme passager
        $trajetsPassager = $repo->createQueryBuilder('c')
            ->join('c.passagers', 'p')
            ->where('p = :user')
            ->andWhere('c.date_depart >= :now')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTime())
            ->orderBy('c.date_depart', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('profil/mes_trajets.html.twig', [
            'trajetsConducteur' => $trajetsConducteur,
            'trajetsPassager' => $trajetsPassager,

        ]);
    }

    #[Route('/trajet/{id}/annuler', name: 'app_annuler_trajet_conducteur', methods: ['POST', 'GET'])]
    public function annulerTrajetConducteur(Covoiturage $trajet, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if ($trajet->getConducteur() !== $user) {
            $this->addFlash('danger', 'Vous ne pouvez pas annuler ce trajet.');
            return $this->redirectToRoute('app_mes_trajets');
        }

        $em->remove($trajet);
        $em->flush();

        $this->addFlash('success', 'Trajet annulé avec succès ✅');
        return $this->redirectToRoute('app_mes_trajets');
    }

    #[Route('/trajet/{id}/quitter', name: 'app_annuler_trajet_passager', methods: ['POST', 'GET'])]
    public function quitterTrajetPassager(Covoiturage $trajet, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$trajet->getPassagers()->contains($user)) {
            $this->addFlash('danger', 'Vous ne participez pas à ce trajet.');
            return $this->redirectToRoute('app_mes_trajets');
        }

        $trajet->removePassager($user);
        $em->flush();

        $this->addFlash('success', 'Vous avez quitté le trajet avec succès ✅');
        return $this->redirectToRoute('app_mes_trajets');
    }

    #[Route('/covoiturage/{id}/participer', name: 'app_participer_covoiturage', methods: ['POST'])]
    public function participer(Covoiturage $covoiturage, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'Veuillez vous connecter pour participer.');
            return $this->redirectToRoute('app_login');
        }

        if ($covoiturage->getPassagers()->contains($user)) {
            $this->addFlash('warning', 'Vous participez déjà à ce trajet.');
        } elseif ($covoiturage->getNbPlace() <= 0) {
            $this->addFlash('danger', 'Ce trajet est complet.');
        } elseif ($covoiturage->getConducteur() === $user) {
            $this->addFlash('danger', 'Vous êtes le conducteur de ce trajet.');
        } elseif ($user->getCredits() < $covoiturage->getPrixPersonne()) {
            $this->addFlash('danger', 'Vous n\'avez pas assez de crédits pour participer à ce trajet.');
        } else {
            // Déduire le prix demandé par le chauffeur
            $prix = $covoiturage->getPrixPersonne();
            $user->setCredits($user->getCredits() - $prix);

            // Ajouter le passager
            $covoiturage->addPassager($user);

            // Diminuer le nombre de places
            $covoiturage->setNbPlace($covoiturage->getNbPlace() - 1);

            $em->persist($user);
            $em->persist($covoiturage);
            $em->flush();

            $this->addFlash('success', 'Vous avez rejoint ce trajet 🚗 (' . $prix . ' crédits ont été utilisés)');
        }

        return $this->redirectToRoute('app_covoiturage');
    }



    #[Route('/covoiturage/annuler/{id}', name: 'covoiturage_annuler')]
    public function annulerParticipation(Covoiturage $covoiturage, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if ($covoiturage->getPassagers()->contains($user)) {
            $covoiturage->removePassager($user);
            $covoiturage->setNbPlace(($covoiturage->getNbPlace() ?? 0) + 1);

            // 🔍 Debug : vérifier les données avant la sauvegarde


            $em->persist($covoiturage);
            $em->flush();

            $this->addFlash('success', 'Vous avez annulé votre participation. ✅');
        } else {
            $this->addFlash('danger', 'Vous ne participez pas à ce trajet.');
        }

        return $this->redirectToRoute('app_mes_trajets');
    }

    #[Route('/covoiturage/{id}', name: 'covoiturage_show')]
    public function show(Covoiturage $covoiturage): Response
    {
        $conducteur = $covoiturage->getConducteur();
        $voiture = $covoiturage->getVoiture();
        $avis = $conducteur->getAvis();
        $preference = $conducteur->getPreference();

        return $this->render('covoiturage/show.html.twig', [
            'covoiturage' => $covoiturage,
            'conducteur' => $conducteur,
            'voiture' => $voiture,
            'avis' => $avis,
            'preference' => $preference,
        ]);
    }
}
