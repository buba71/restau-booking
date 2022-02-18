<?php

declare(strict_types=1);

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUserName = $authenticationUtils->getLastUsername();


        return $this->render('Security/Login/index.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $lastUserName,
            'error'         => $error
        ]);
    }
}