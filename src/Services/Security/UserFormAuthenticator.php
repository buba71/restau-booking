<?php

declare(strict_types=1);

namespace App\Services\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

final class UserFormAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait;

    public function __construct(private RouterInterface $router, private SessionInterface $session) {}

    public function supports(Request $request): ?bool
    {
        return ($request->getPathInfo() === '/login' && $request->isMethod('POST'));
    }

    public function authenticate(Request $request): PassportInterface
    {
        $email = $request->get('_username');
        $password = $request->get('_password');

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password)
        );
    }
    
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Set target Url from session(SecurityController) where redirect user.
        $targetUrl = $this->session->get('_security_referer_url');

        if ($targetUrl) {
            return new RedirectResponse($targetUrl);
        }

        return new RedirectResponse(
            $this->router->generate('home')
        );

    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse('/login');        
    }    
}
