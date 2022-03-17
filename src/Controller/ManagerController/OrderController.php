<?php

declare(strict_types=1);

namespace App\Controller\ManagerController;

use App\Entity\BookingOrder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mannager')]
#[IsGranted('ROLE_MANAGER')]
final class OrderController extends AbstractController
{
    #[Route('/restaurant_orders/show', name: 'show_orders')]
    public function showOrders(): Response
    {
        $restaurant = $this->getUser()->getRestaurant();
        
        $bookings = $restaurant->getBookings();
        $bookings = $bookings->filter(function($booking) {
            return $booking->getBookingOrder() !== null;
        });

        return $this->render('BackOffice/ManagerAccount/Order/show_orders.html.twig', [
            'bookings' => $bookings
        ]);
    }

    #[Route('restaurant_order/show/{id}', name: 'show_order_details')]
    public function showOrderDetails(BookingOrder $order)
    {
        return $this->render('BackOffice/ManagerAccount/Order/show_order_details.html.twig', [
            'booking' => $order->getBooking(),
            'order' => $order
        ]);
    }
}