<?php

namespace App\Controller;


use App\Form\EditProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profil/index.html.twig', [
            'user' => $user,
        ]);
    }


    #[Route('/profil/modifier', name: 'app_profil_edit')]
    public function edit(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        /** @var \App\Entity\User $user */

        $form = $this->createForm(EditProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('plainPassword')->getData();
            if ($newPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            $em->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès ✅');
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profil/roles', name: 'app_choix_role', methods: ['POST'])]
    public function updateRoles(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $roles = $request->request->all('roles');

        // Si l'utilisateur coche "chauffeur" mais n'a pas de voiture
        if (in_array('ROLE_CHAUFFEUR', $roles) && $user->getVoitures()->isEmpty()) {
            $this->addFlash('danger', 'Vous devez enregistrer au moins un véhicule pour devenir chauffeur.');
            return $this->redirectToRoute('app_profil');
        }

        $user->setRoles($roles);
        $em->flush();

        $this->addFlash('success', 'Rôles mis à jour avec succès ✅');
        return $this->redirectToRoute('app_profil');
    }
}
