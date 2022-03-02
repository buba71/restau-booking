<?php

declare(strict_types=1);

namespace App\Controller\ManagerController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/manager')]
#[IsGranted('ROLE_MANAGER')]
final class BookingController extends AbstractController
{
    #[Route('/show_restaurant_bookings', name: 'show_restaurant_bookings')]
    public function showOrders(): Response
    {
        $restaurant = $this->getUser()->getRestaurant();

        $bookings = $restaurant->getBookings();
        
        // Get only bookings without order.
        $bookings = $bookings->filter(function($element) {
            return $element->getBookingOrder() === null;
        });
        
        return $this->render('BackOffice/ManagerAccount/Booking/show_bookings.html.twig', [
            'bookings' => $bookings
        ]);
    }
}