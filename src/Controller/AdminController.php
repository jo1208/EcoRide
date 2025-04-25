<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EmployeType;
use App\Repository\CovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/admin/employes', name: 'admin_employes')]
    public function manageEmployees(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(EmployeType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_EMPLOYE']);
            $user->setPassword(
                $passwordHasher->hashPassword($user, $user->getPassword())
            );

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Employé ajouté avec succès ✅');
            return $this->redirectToRoute('admin_employes');
        }

        return $this->render('admin/employes.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/statistiques', name: 'admin_stats')]
    public function statistiques(Request $request, CovoiturageRepository $repo): Response
    {
        $periode = $request->query->get('range', '30'); // Ex: 7, 30, all
        $covoiturages = $repo->findAll(); // On prend tout

        $stats = [];
        $revenus = [];

        $nbJours = $periode === 'all' ? null : (int) $periode;
        $limite = $nbJours ? new \DateTime("-$nbJours days") : null;

        foreach ($covoiturages as $trajet) {
            if ($trajet->getStatut() === 'Annulé') {
                continue;
            }

            $dateDepart = $trajet->getDateDepart()?->format('Y-m-d');
            $datePaiement = $trajet->getCreatedAt()?->format('Y-m-d');

            if (!$dateDepart || !$datePaiement) {
                continue;
            }

            // ➕ Graphique 1 : Nombre de trajets (filtrés par date de départ)
            if (!$nbJours || new \DateTime($dateDepart) >= $limite) {
                $stats[$dateDepart] = ($stats[$dateDepart] ?? 0) + 1;
            }

            // ➕ Graphique 2 : Crédits générés (filtrés par date de création/paiement)
            if (!$nbJours || new \DateTime($datePaiement) >= $limite) {
                $revenus[$datePaiement] = ($revenus[$datePaiement] ?? 0) + 2;
            }
        }

        ksort($stats);
        ksort($revenus);

        $allDates = array_unique(array_merge(array_keys($stats), array_keys($revenus)));
        sort($allDates); // trie les dates

        // on remplit les valeurs manquantes avec 0
        $finalStats = [];
        $finalRevenus = [];

        foreach ($allDates as $date) {
            $finalStats[] = $stats[$date] ?? 0;
            $finalRevenus[] = $revenus[$date] ?? 0;
        }

        return $this->render('admin/statistiques.html.twig', [
            'labels' => $allDates,
            'stats_values' => $finalStats,
            'revenus_values' => $finalRevenus,
            'periode' => $periode,
        ]);
    }

    #[Route('/admin/suspend/{id}', name: 'admin_suspend_user', methods: ['POST'])]
    public function suspendUser(User $user, EntityManagerInterface $em): Response
    {
        $user->setIsSuspended(true);
        $em->flush();

        $this->addFlash('warning', 'Compte suspendu avec succès.');
        return $this->redirectToRoute('admin_users'); // 🧭 à adapter selon ta page de gestion des utilisateurs
    }

    #[Route('/admin/reactivate/{id}', name: 'admin_reactivate_user')]
    public function reactivateUser(User $user, EntityManagerInterface $em): Response
    {
        $user->setIsSuspended(false);
        $em->flush();

        $this->addFlash('success', 'Compte réactivé avec succès.');
        return $this->redirectToRoute('admin_users');
    }


    #[Route('/admin/users', name: 'admin_users')]
    public function users(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }
}
