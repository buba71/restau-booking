<?php

declare(strict_types=1);

namespace App\Controller\CustomerController;

use App\Entity\OrderLine;
use App\Entity\User;
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
#[IsGranted('ROLE_CUSTOMER')]
final class BookingOrderController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager, private SessionInterface $session) {}

    #[Route('/show_booking_orders/{id}', name: 'show_customer_orders')]
    public function showBookingOrders(User $user, BookingRepository $bookingRepository): Response
    {
        $bookingWithOrders = $bookingRepository->findBookingWithOrdersByUSer($user->getId());

        $orders = array_map(fn($element) => $element->getBookingOrder(), $bookingWithOrders);

        return $this->render('BackOffice/CustomerAccount/Booking/show_booking_orders.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/order_summary', name: 'order_summary' )]
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
            $booking->setUser($this->getUser());

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

                return $this->redirectToRoute('show_customer_orders', ['id' => $this->getUser()->getId()]);
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