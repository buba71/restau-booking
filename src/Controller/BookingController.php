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
use Symfony\Component\Routing\Annotation\Route;

final class BookingController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/booking/{id}', name: 'booking')]
    /**
     * 
     * @return Response
     */
    public function bookTable(Request $request, Restaurant $restaurant): Response
    {
        $booking = new Booking();

        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);
        //dd($form);

        if ($form->isSubmitted() && $form->isValid()) {
            $restaurant->addBooking($booking);
            $this->entityManager->persist($restaurant);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre réservation a bien été prise en compte');
            
            return $this->redirectToRoute('booking_details');
        }
        
        return $this->renderForm('FrontOffice/booking.html.twig', [ 
            'restaurant' => $restaurant,
            'form' => $form
        ]);
    }
}
