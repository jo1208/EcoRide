<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;  // Ajout de l'importation du manager CSRF

class LoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private CsrfTokenManagerInterface $csrfTokenManager;

    // Injection de CsrfTokenManagerInterface dans le constructeur
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager // Injection du service
    ) {
        $this->csrfTokenManager = $csrfTokenManager; // Initialisation du gestionnaire de CSRF
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $csrfToken = $request->request->get('_csrf_token');  // Récupère le token CSRF

        // Vérifie les valeurs des tokens (affiche dans les logs ou la réponse pour débogage)
        error_log('Generated CSRF token: ' . $this->csrfTokenManager->getToken('authenticate')->getValue());
        error_log('Submitted CSRF token: ' . $csrfToken);

        if (!$this->csrfTokenManager->isTokenValid(new \Symfony\Component\Security\Csrf\CsrfToken('authenticate', $csrfToken))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
                new RememberMeBadge(),
            ]
        );
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Exemple de redirection après authentification réussie
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath); // Redirige vers la page d'origine demandée
        }

        return new RedirectResponse('/profil'); // Redirige vers /profil si aucune cible spécifiée
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
