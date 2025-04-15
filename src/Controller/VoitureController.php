<?php

// src/Controller/VoitureController.php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Voiture;
use App\Form\VoitureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class VoitureController extends AbstractController
{
    #[Route('/profil/vehicule', name: 'app_voiture')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $voitures = $user->getVoitures();

        return $this->render('voiture/index.html.twig', [
            'voitures' => $voitures,
        ]);
    }



    #[Route('/profil/vehicule/ajouter', name: 'app_voiture_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $voiture = new Voiture();
        $form = $this->createForm(VoitureType::class, $voiture);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $voiture->setUser($this->getUser());
            $em->persist($voiture);
            $em->flush();

            $this->addFlash('success', 'Véhicule ajouté avec succès ✅');
            return $this->redirectToRoute('app_voiture');
        }

        return $this->render('voiture/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profil/vehicule/{id}/modifier', name: 'app_voiture_edit')]
    public function edit(Request $request, Voiture $voiture, EntityManagerInterface $em): Response
    {
        // Vérification que l'utilisateur peut modifier cette voiture
        if ($voiture->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Véhicule modifié avec succès ✅');
            return $this->redirectToRoute('app_voiture');
        }

        return $this->render('voiture/edit.html.twig', [
            'form' => $form->createView(),
            'voiture' => $voiture,
        ]);
    }

    #[Route('/profil/vehicule/supprimer/{id}', name: 'app_voiture_delete', methods: ['POST'])]
    public function delete(Request $request, Voiture $voiture, EntityManagerInterface $em): Response
    {
        // Vérifie que l'utilisateur est bien propriétaire du véhicule
        if ($voiture->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Accès refusé");
        }

        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $voiture->getId(), $submittedToken)) {
            $em->remove($voiture);
            $em->flush();
            $this->addFlash('success', 'Véhicule supprimé avec succès ✅');
        }

        return $this->redirectToRoute('app_voiture');
    }
}
