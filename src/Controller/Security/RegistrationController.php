<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\CustomerType;
use App\Form\ManagerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

final class RegistrationController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/register_customer', name: 'register_customer')]
    public function registerCustomer(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();

        $form = $this->createForm(CustomerType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {            

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()

                )
            );
            $user->setRoles(['ROLE_CUSTOMER']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->renderForm('Security/Register/Customer/index.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/register_manager', name: 'register_manager')]
    public function registerManager(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();

        $form = $this->createForm(ManagerType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {            

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()

                )
            );
            $user->setRoles(['ROLE_MANAGER']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->renderForm('Security/Register/Manager/index.html.twig', [
            'form' => $form
        ]);
    }
}