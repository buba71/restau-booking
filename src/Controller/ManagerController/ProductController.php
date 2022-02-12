<?php

declare(strict_types=1);

namespace App\Controller\ManagerController;

use App\Entity\MenuItem;
use App\Form\MenuItemType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/manager')]
final class ProductController extends AbstractController
{    
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/show_products', name: 'show_products')]
    public function showDishes(Request $request): Response
    {
        $products = $this->entityManager->getRepository(MenuItem::class)->findBy([], ['name' => 'asc']);

        $product = new MenuItem();

        $form = $this->createForm(MenuItemType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre produit a été ajouté.');

            return $this->redirectToRoute('show_products');
        }


        return $this->renderForm('BackOffice/ManagerAccount/product/show_products.html.twig', [
            'dishes' => $products,
            'form' => $form
        ]);
    }
}