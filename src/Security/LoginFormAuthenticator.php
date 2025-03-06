<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email'); // Correction ici
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(    
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password')), // Correction ici
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')), // Correction ici
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?RedirectResponse
    {
        $user = $token->getUser();
        $roles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $roles)) {
            return new RedirectResponse($this->urlGenerator->generate('admin_vlogs'));
        } elseif (in_array('ROLE_HOTE', $roles)) { // Correction ici
            return new RedirectResponse($this->urlGenerator->generate('nos_vlog'));
        } elseif (in_array('ROLE_VOYAGEUR', $roles)) { // Correction ici
            // return new RedirectResponse($this->urlGenerator->generate('nos_vlog'));
            return new RedirectResponse($this->urlGenerator->generate('app_home'));
        }elseif (in_array('ROLE_TRANSPORTEUR', $roles)) { // Correction ici
            return new RedirectResponse($this->urlGenerator->generate('app_transport_index'));
        } elseif (in_array('ROLE_RESTAURANT', $roles)) { // Correction ici
            return new RedirectResponse($this->urlGenerator->generate('app_restaurant_index'));
        }

        // Redirection par défaut si l'utilisateur n'a pas un rôle connu
        return new RedirectResponse(
            $this->urlGenerator->generate(self::LOGIN_ROUTE, [
                'error' => 'Accès non autorisé.'
            ])
        );
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
