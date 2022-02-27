<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BookingOrder;
use App\Entity\Restaurant;
use App\Form\OrderFirstStepType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/store')]
final class StoreController extends AbstractController
{
    #[Route('/index/{id}', name:'order')]
    public function index(Restaurant $restaurant, Request $request, SessionInterface $session): Response
    {
        $order = new BookingOrder();

        $form = $this->createForm(OrderFirstStepType::class, $order);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $session->set('order', $order);

            if ('card' === $form->getClickedButton()->getName()) {
                
                return $this->redirectToRoute('card_store', ['id' => $restaurant->getId()]);
            }

            return $this->redirectToRoute('menu_store', ['id' => $restaurant->getId()]);
        }

        return $this->renderForm('FrontOffice/Restaurant/Store/index.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/card_store/{id}', name: 'card_store')]
    public function selectCardItem(Restaurant $restaurant): Response
    {
        $menuItems = $restaurant->getMenuItems();

        return $this->render('FrontOffice/Restaurant/Store/product.html.twig', [
            'restaurant' => $restaurant,
            'menuItems' => $menuItems,
        ]);
    }

    #[Route('/menu_store/{id}', name: 'menu_store')]
    public function selectMenu(Restaurant $restaurant): Response
    {
        $menus = $restaurant->getMenus();

        return $this->render('FrontOffice/Restaurant/Store/menu.html.twig', [
            'restaurant' => $restaurant,
            'menus' => $menus
        ]);
    }
}