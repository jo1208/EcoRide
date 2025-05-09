<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupère l'erreur d'authentification (si elle existe)
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupère le dernier nom d'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        // Affiche la page de connexion
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
}
