<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Restaurant;
use App\Form\BookingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

final class BookingRestaurantController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/booking/{id}', name: 'booking')]
    /**
     * 
     * @return Response
     */
    public function bookTable(Request $request, Restaurant $restaurant, SessionInterface $session): Response
    {
        $booking = new Booking();

        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);

        

        if ($form->isSubmitted() && $form->isValid()) {
            if('booking' === $form->getClickedButton()->getName()) {
                $restaurant->addBooking($booking);
                $this->entityManager->persist($restaurant);
                $this->entityManager->flush();

                $this->addFlash('success', 'Votre réservation a bien été prise en compte');
                
                return $this->redirectToRoute('show_bookings');
            } 

            $session->set('booking', $booking);

            return $this->redirectToRoute('order');
            
        }
        
        return $this->renderForm('FrontOffice/booking_restaurant.html.twig', [ 
            'restaurant' => $restaurant,
            'form' => $form
        ]);
    }
}
