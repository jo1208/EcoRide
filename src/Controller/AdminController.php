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
use App\Document\ConnectionLog;
use Doctrine\ODM\MongoDB\DocumentManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
        $periode = $request->query->get('range', '30');
        $covoiturages = $repo->findAll();

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

            //  Graphique 1 : Nombre de trajets (filtrés par date de départ)
            if (!$nbJours || new \DateTime($dateDepart) >= $limite) {
                $stats[$dateDepart] = ($stats[$dateDepart] ?? 0) + 1;
            }

            // Graphique 2 : Crédits générés (filtrés par date de création/paiement)
            if (!$nbJours || new \DateTime($datePaiement) >= $limite) {
                $revenus[$datePaiement] = ($revenus[$datePaiement] ?? 0) + 2;
            }
        }

        ksort($stats);
        ksort($revenus);

        $allDates = array_unique(array_merge(array_keys($stats), array_keys($revenus)));
        sort($allDates);


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
        return $this->redirectToRoute('admin_users');
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

    #[Route('/admin/credits', name: 'admin_credits')]
    public function credits(CovoiturageRepository $repo): Response
    {
        $totalCredits = $repo->getTotalCreditsGagnes();

        return $this->render('admin/credits.html.twig', [
            'totalCredits' => $totalCredits,
        ]);
    }


    #[Route('/admin/logs', name: 'admin_logs')]
    public function logs(DocumentManager $dm, Request $request, PaginatorInterface $paginator): Response
    {
        $query = $dm->getRepository(ConnectionLog::class)
            ->createQueryBuilder()
            ->sort('timestamp', 'DESC')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('admin/logs.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/admin/logs/{id}/delete', name: 'admin_logs_delete', methods: ['POST'])]
    public function delete(string $id, DocumentManager $dm): Response
    {
        $log = $dm->getRepository(ConnectionLog::class)->find($id);

        if (!$log) {
            throw $this->createNotFoundException('Log non trouvé.');
        }

        $dm->remove($log);
        $dm->flush();

        $this->addFlash('success', '🗑️ Log supprimé.');
        return $this->redirectToRoute('admin_logs');
    }

    #[Route('/admin/logs/{id}/edit', name: 'admin_logs_edit')]
    public function edit(string $id, Request $request, DocumentManager $dm): Response
    {
        $log = $dm->getRepository(ConnectionLog::class)->find($id);

        if (!$log) {
            $this->addFlash('danger', 'Log introuvable.');
            return $this->redirectToRoute('admin_logs');
        }

        $form = $this->createFormBuilder($log)
            ->add('username', TextType::class, ['label' => 'Email'])
            ->add('success', ChoiceType::class, [
                'choices' => ['Succès' => true, 'Échec' => false],
                'label' => 'Statut'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dm->flush();
            $this->addFlash('success', 'Log mis à jour ✅');
            return $this->redirectToRoute('admin_logs');
        }

        return $this->render('admin/logs_edit.html.twig', [
            'form' => $form->createView(),
            'log' => $log,
        ]);
    }
}
