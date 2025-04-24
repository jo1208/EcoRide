<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EmployeType;
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
}
