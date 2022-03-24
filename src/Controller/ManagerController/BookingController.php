<?php

declare(strict_types=1);

namespace App\Controller\ManagerController;

use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/manager')]
#[IsGranted('ROLE_MANAGER')]
final class BookingController extends AbstractController
{
    #[Route('/restaurant_bookings/show', name: 'show_restaurant_bookings')]
    public function showBookings(): Response
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

    #[Route('/restaurant_booking/delete/{id}', name: 'delete_booking')]
    public function deleteBooking(Booking $booking, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($booking);
        $entityManager->flush();

        $this->addFlash('success', 'Réservation supprimée avec succès.');
        
        return $this->redirectToRoute('show_restaurant_bookings');
    }
}