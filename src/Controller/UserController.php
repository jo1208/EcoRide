<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType; // Formulaire
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface; // Injecter l'EntityManagerInterface

class UserController extends AbstractController
{
    // Injection de l'EntityManagerInterface dans le constructeur
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/user', name: 'app_user')]
    public function index(Request $request): Response
    {
        // Créer une nouvelle instance de User
        $user = new User();

        // Créer le formulaire
        $form = $this->createForm(RegistrationType::class, $user);

        // Traiter la soumission du formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder l'utilisateur dans la base de données
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Rediriger vers la page d'accueil ou une autre page après l'inscription
            return $this->redirectToRoute('app_home');
        }

        // Passer le formulaire à la vue
        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
