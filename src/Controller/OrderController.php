<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BookingOrder;
use App\Form\OrderFirstStepType;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/order')]
final class OrderController extends AbstractController
{
    #[Route('/index', name:'order')]
    public function index(Request $request, SessionInterface $session): Response
    {
        $order = new BookingOrder();

        $form = $this->createForm(OrderFirstStepType::class, $order);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('order', $order);

            if ('card' === $form->getClickedButton()->getName()) {
                
                return $this->redirectToRoute('order_card');
            }

            return $this->redirectToRoute('order_menu');
        }

        return $this->renderForm('FrontOffice/booking_restaurant_order.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/card_order', name: 'order_card')]
    public function orderCard(MenuItemRepository $menuItemRepository): Response
    {
        $menuItems = $menuItemRepository->findAll();

        return $this->render('FrontOffice/add_menuItem_order.html.twig', [
            'menuItems' => $menuItems,
        ]);
    }

    #[Route('/menu_order', name: 'order_menu')]
    public function orderMenu(MenuRepository $repository): Response
    {
        $menus = $repository->findAll();

        return $this->render('FrontOffice/add_menu_order.html.twig', [
            'menus' => $menus
        ]);
    }
}