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

        $trajets = $repo->findWithFilters($filters);

        return $this->render('covoiturage/index.html.twig', [
            'trajets' => $trajets, // ✅ Transmis à la vue Twig
        ]);
    }

    #[Route('/covoiturage/nouveau', name: 'covoiturage_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Vérifie que l'utilisateur est bien connecté et a le rôle "ROLE_CHAUFFEUR"
        if (!$user || !in_array('ROLE_CHAUFFEUR', $user->getRoles())) {
            $this->addFlash('danger', 'Accès réservé aux chauffeurs.');
            return $this->redirectToRoute('app_profil');
        }

        $trajet = new Covoiturage();
        $form = $this->createForm(CovoiturageType::class, $trajet, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trajet->setConducteur($user);
            $em->persist($trajet);
            $em->flush();

            $this->addFlash('success', 'Trajet créé avec succès ✅');
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
}
