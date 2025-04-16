<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route('/user', name: 'app_user')]
    public function index(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe AVANT enregistrement
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profil/update-roles', name: 'app_update_roles', methods: ['POST'])]
    public function updateRoles(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $roles = [];

        if ($request->request->get('isChauffeur')) {
            $roles[] = 'ROLE_CHAUFFEUR';
        }

        if ($request->request->get('isPassager')) {
            $roles[] = 'ROLE_PASSAGER';
        }

        if (empty($roles)) {
            $roles[] = 'ROLE_USER'; // rôle de base
        }

        $user->setRoles($roles);
        $em->flush();

        $this->addFlash('success', 'Vos rôles ont été mis à jour ✅');

        return $this->redirectToRoute('app_profil');
    }
}
