<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BookingOrder;
use App\Entity\OrderLine;
use App\Form\OrderFirstStepType;
use App\Form\OrderLastStepType;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/order')]
final class OrderController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager, private SessionInterface $session) {}

    #[Route('/index', name:'order')]
    public function index(Request $request): Response
    {
        $order = new BookingOrder();

        $form = $this->createForm(OrderFirstStepType::class, $order);

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
    public function orderCard(MenuItemRepository $menuItemRepository): Response
    {
        $menuItems = $menuItemRepository->findAll();

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

    #[Route('/recap', name: 'order_recap' )]
    public function recapOrder(
        Request $request,
        RestaurantRepository $restaurantRepository,
        MenuItemRepository $menuItemRepository,
        MenuRepository $menuRepository,
        SessionInterface $session
        ): Response {

        $session->set('isNewCartSession', true);

        if ($session->has('booking') && $session->has('order') && $session->has('cart')) {
            
            $booking = $session->get('booking');

            // TODO This would be dynamique on the next version.

            $restaurant = $restaurantRepository->findOneBy(['id' => 1]);

            $booking->setRestaurant($restaurant);

            $order = $session->get('order');
            $cart = $session->get('cart');
            $amount = 0;

            $order->setBooking($session->get('booking'));

            // If have previous session cart, clear previous order lines.
            if ($session->get('isNewCartSession')) {
                foreach ($order->getOrderLines() as $orderLine) {
                    $order->removeOrderLine($orderLine);
                }
            };            
        

            if (isset($cart['menu'])) {

                foreach ($cart['menu'] as $id => $quantity) {
                    
                    $menu = $menuRepository->findOneBy(['id' => $id]);
                    $orderLine = new OrderLine();
                    $orderLine->setName($menu->getName());
                    $orderLine->setQuantity($quantity);
                    $orderLine->setPrice($menu->getPrice() * $quantity);
                    
                    $amount += $orderLine->getPrice();

                    $order->addOrderline($orderLine);
                }
            }

            if (isset($cart['menuItem'])) {

                foreach ($cart['menuItem'] as $id => $quantity) {

                    $menu = $menuItemRepository->findOneBy(['id' => $id]);
                    $orderLine = new OrderLine();
                    $orderLine->setName($menu->getName());
                    $orderLine->setQuantity($quantity);
                    $orderLine->setPrice($menu->getPrice() * $quantity);

                    $amount += $orderLine->getPrice();

                    $order->addOrderline($orderLine);
                }
            }
            
            $order->setAmount($amount);            

            $form = $this->createForm(OrderLastStepType::class, $order);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $this->entityManager->persist($order);
                $this->entityManager->flush();

                $this->addFlash('success', 'Votre commande a bien été enregistrée.');

                return $this->redirectToRoute('show_customer_orders');
            } 
            
        } else {
            throw new Exception('Session expired! Please retry the booking order process.');
        }
        

        return $this->renderForm('BackOffice/CustomerAccount/Booking/order_summary.html.twig', [
            'order' => $order,
            'form' => $form
        ]);
    }
}