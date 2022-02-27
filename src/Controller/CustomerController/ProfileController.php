<?php

declare(strict_types=1);

namespace App\Controller\CustomerController;

use App\Entity\User;
use App\Form\CustomerType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/customer')]
#[IsGranted('ROLE_CUSTOMER')]
final class ProfileController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/show_profile', name: 'customer_profile')]
    public function showProfile(): Response
    {
        return $this->render('BackOffice/CustomerAccount/Profile/customer_profile_show.html.twig');
    }  
    
    #[Route('/edit/{id}', name: 'customer_edit')]
    public function editProfile(User $user, Request $request): Response
    {
        $form = $this->createForm(CustomerType::class, $user);
        $form->remove('password');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre profil a été mis à jour.');

            return $this->redirectToRoute('customer_profile');
        }
        

        return $this->renderForm('BackOffice/CustomerAccount/Profile/customer_profile_edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/customer_delete/{id}', name: 'customer_delete')]
    public function deleteCustomer(User $user, TokenStorageInterface $security): Response
    {
        $security->setToken(null);

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->addFlash('success', 'compte supprimé'); 

        return $this->redirectToRoute('home');
    }
}