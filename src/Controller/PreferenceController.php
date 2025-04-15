<?php

namespace App\Controller;

use App\Entity\Preference;
use App\Form\PreferenceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PreferenceController extends AbstractController
{
    #[Route('/profil/preference', name: 'app_preference')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $preference = $user->getPreference();

        return $this->render('preferences/index.html.twig', [
            'preference' => $preference,
        ]);
    }

    #[Route('/profil/preference/modifier', name: 'app_preference_edit')]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $preference = $user->getPreference() ?? new Preference();
        $preference->setUser($user);

        $form = $this->createForm(PreferenceType::class, $preference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($preference);
            $em->flush();

            $this->addFlash('success', 'Préférences mises à jour ✅');
            return $this->redirectToRoute('app_preference');
        }

        // ✅ Toujours retourner une réponse ici :
        return $this->render('preferences/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
