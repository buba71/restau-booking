<?php

declare(strict_types=1);

namespace App\Controller\ManagerController;

use App\Entity\User;
use App\Form\ManagerType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/manager')]
#[IsGranted('ROLE_MANAGER')]
final class ProfileController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/show_profile', name: 'manager_profile')]
    public function showProfile(): Response
    {
        return $this->render('BackOffice/ManagerAccount/Profile/manager_profile_show.html.twig');
    }

    #[Route('/edit/{id}', name: 'manager_edit')]
    public function editProfile(User $user, Request $request): Response
    {
        $form = $this->createForm(ManagerType::class, $user);
        $form->remove('restaurant');
        $form->remove('password');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->entityManager->flush($user);
            $this->addFlash('success', 'Votre profil a été mis à jour.');

            return $this->redirectToRoute('manager_profile');
        }
        

        return $this->renderForm('BackOffice/ManagerAccount/Profile/manager_profile_edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/manager_delete/{id}', name: 'manager_delete')]
    public function deleteCustomer(User $user, TokenStorageInterface $security): Response
    {
        $security->setToken(null);
        
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->addFlash('success', 'compte supprimé'); 

        return $this->redirectToRoute('home');
    }
}

