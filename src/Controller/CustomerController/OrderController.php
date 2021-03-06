<?php

declare(strict_types=1);

namespace App\Controller\CustomerController;

use App\Entity\OrderLine;
use App\Form\OrderLastStepType;
use App\Repository\BookingRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/customer')]
final class OrderController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/show_booking_orders', name: 'show_customer_orders')]
    #[IsGranted('ROLE_CUSTOMER')]
    public function showBookingOrders(BookingRepository $bookingRepository): Response
    {
        $bookingWithOrders = $bookingRepository->findBookingWithOrdersByUSer($this->getUser()->getId());

        $orders = array_map(fn($element) => $element->getBookingOrder(), $bookingWithOrders);

        return $this->render('BackOffice/CustomerAccount/Booking/show_booking_orders.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/order_summary', name: 'order_summary' )]
    public function summarizeOrder(
        Request $request,
        RestaurantRepository $restaurantRepository,
        MenuItemRepository $menuItemRepository,
        MenuRepository $menuRepository,
        SessionInterface $session
        ): Response {

        // First cart has been validated.
        $session->set('isNewCartSession', true);

        if ($session->has('booking') && $session->has('order') && $session->has('cart')) {
            
            $booking = $session->get('booking');
            $cart = $session->get('cart');
            $order = $session->get('order');     
            $order->setBooking($booking);              
            
            $amount = 0;
            

            // If have previous session cart, clear previous order lines.
            if ($session->get('isNewCartSession')) {
                foreach ($order->getOrderLines() as $orderLine) {
                    $order->removeOrderLine($orderLine);
                }
            }

            if (isset($cart['menu']) && is_array($cart['menu'])) {

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

            if (isset($cart['menuItem']) && is_array($cart['menuItem'])) {

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

            // User can add comments for current order.
            $form = $this->createForm(OrderLastStepType::class, $order);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                
                // Client must be authenticated at this point.
                $this->denyAccessUnlessGranted('ROLE_CUSTOMER'); 
                
                $restaurant = $restaurantRepository->findOneBy(['id' => $booking->getRestaurant()->getId()]);

                $booking->setUser($this->getUser());
                $booking->setBookingOrder($order);
                $restaurant->addBooking($booking);       
                
                $this->entityManager->flush();
                
                $session->remove('booking');
                $session->remove('cart');
                $session->remove('order');

                $this->addFlash('success', 'Votre commande a bien ??t?? enregistr??e.');

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