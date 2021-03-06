<?php

declare(strict_types=1);

namespace App\Controller\CustomerController;

use App\Entity\Booking;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/customer')]
final class BookingController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    /**
     * Excluded from authentication.
     */
    #[Route('/booking/{id}', name: 'booking')]
    public function bookTable(Request $request, Restaurant $restaurant, SessionInterface $session): Response
    {
        $booking = new Booking();

        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {

            $restaurant->addBooking($booking);  
            $session->set('booking', $booking); 

            if($form->getClickedButton()->getName() === 'booking') {                

                // Client must be authenticated at this point.
                $this->denyAccessUnlessGranted('ROLE_CUSTOMER'); 

                if (($this->getUser() instanceOf User) && $this->getUser() !== null) {
                    $booking->setUser($this->getUser()); 
                }                   

                $this->entityManager->persist($booking);
                $this->entityManager->flush();
                
                $session->remove('booking');

                $this->addFlash('success', 'Votre réservation a bien été prise en compte');
                
                return $this->redirectToRoute('show_customer_bookings');
            } 

            return $this->redirectToRoute('order', ['id' => $restaurant->getId()]);
            
        }
        
        return $this->renderForm('FrontOffice/Restaurant/restaurant_booking_index.html.twig', [ 
            'restaurant' => $restaurant,
            'form' => $form
        ]);
    }

    #[Route('/show_bookings', name:'show_customer_bookings')]
    #[IsGranted('ROLE_CUSTOMER')]
    public function showBookings(BookingRepository $bookingRepository): Response
    {
        $bookings = $bookingRepository->findBookingWithoutOrdersByUSer($this->getUser()->getId());

        return $this->render('BackOffice/CustomerAccount/Booking/show_bookings.html.twig', [
            'bookings' => $bookings
        ]);
    }    

    #[Route('/edit_booking/{id}', name:'edit_booking')]
    #[IsGranted('ROLE_CUSTOMER')]
    public function editBooking(Request $request, Booking $booking): Response
    {
        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->entityManager->persist($booking);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre réservation a été modifiée');

            return $this->redirectToRoute('show_customer_bookings');
        }

        return $this->renderForm('BackOffice/CustomerAccount/Booking/edit_booking.html.twig', [
            'booking' => $booking,
            'form' => $form
        ]);
    }

    #[Route('/delete_booking/{id}', name:'delete_booking')]
    #[IsGranted('ROLE_CUSTOMER')]
    public function deleteBooking(Booking $booking): Response
    {        
        $this->entityManager->remove($booking);
        $this->entityManager->flush();

        $this->addFlash('success', 'Votre réservation a été annulée');

        return $this->redirectToRoute('show_customer_bookings',);
    }
}
