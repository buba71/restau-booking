<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\MenuItem;
use App\Form\MenuItemType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/manager')]
final class DishController extends AbstractController
{    
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/show_dishes', name: 'show_dishes')]
    public function showDishes(Request $request): Response
    {
        $dishes = $this->entityManager->getRepository(MenuItem::class)->findAll();

        $dish = new MenuItem();

        $form = $this->createForm(MenuItemType::class, $dish);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($dish);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre produit a été ajouté.');

            return $this->redirectToRoute('show_dishes');
        }


        return $this->renderForm('BackOffice/ManagerAccount/dish/show_dishes.html.twig', [
            'dishes' => $dishes,
            'form' => $form
        ]);
    }
}