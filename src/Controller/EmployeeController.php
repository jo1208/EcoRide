<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
use App\Repository\CovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/employee')]
class EmployeeController extends AbstractController
{


    #[Route('/covoiturages/problematiques', name: 'covoits_problematiques')]
    public function problematicCovoits(CovoiturageRepository $repo): Response
    {
        $covoits = $repo->findWithRefusedAvis(); // Cette fonction on va la créer juste après
        return $this->render('employee/covoits_problematiques.html.twig', [
            'covoiturages' => $covoits,
        ]);
    }


    // 📝 Liste des avis en attente
    #[Route('/reviews/pending', name: 'reviews_pending')]
    public function pendingReviews(AvisRepository $avisRepository): Response
    {
        $avisList = $avisRepository->findBy(['statut' => 'En attente validation']);

        return $this->render('employee/reviews_pending.html.twig', [
            'avisList' => $avisList,
        ]);
    }


    // ✅ Valider un avis
    #[Route('/review/{id}/validate', name: 'review_validate', methods: ['POST'])]
    public function validateReview(Avis $avis, EntityManagerInterface $em): Response
    {
        $avis->setStatut('Validé');
        $em->flush();

        $this->addFlash('success', 'Avis validé avec succès.');
        return $this->redirectToRoute('reviews_pending');
    }

    // ❌ Refuser un avis
    #[Route('/review/{id}/refuse', name: 'review_refuse', methods: ['POST'])]
    public function refuseReview(Avis $avis, EntityManagerInterface $em): Response
    {
        $em->remove($avis);
        $em->flush();

        $this->addFlash('warning', 'Avis supprimé avec succès.');
        return $this->redirectToRoute('reviews_pending');
    }
}
