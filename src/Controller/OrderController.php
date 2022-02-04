<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\FormModel\BookingOrderModel;
use App\Form\OrderStepOneType;
use App\Repository\CategoryRepository;
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
    public function __construct(private SessionInterface $session) {}

    #[Route('/index', name:'order')]
    public function index(Request $request): Response
    {
        $order = new BookingOrderModel();

        $form = $this->createForm(OrderStepOneType::class, $order);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->session->set('order', $order);

            if ('card' === $form->getClickedButton()->getName()) {
                
                return $this->redirectToRoute('order_card');
            }

            return $this->redirectToRoute('order_menu');
        }

        return $this->renderForm('FrontOffice/booking_order.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/card_order', name: 'order_card')]
    public function orderCard(CategoryRepository $categoryRepository, MenuItemRepository $menuItemRepository): Response
    {
        $menuItems = $menuItemRepository->findAll();
        $categories = $categoryRepository->findAll();

        return $this->render('FrontOffice/card_booking_order.html.twig', [
            'menuItems' => $menuItems,
        ]);
    }

    #[Route('/menu_order', name: 'order_menu')]
    public function orderMenu(MenuRepository $repository): Response
    {
        $menus = $repository->findAll();

        return $this->render('FrontOffice/menu_booking_order.html.twig', [
            'menus' => $menus
        ]);
    }
}