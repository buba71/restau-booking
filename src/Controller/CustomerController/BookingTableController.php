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
#[IsGranted('ROLE_CUSTOMER')]
final class BookingTableController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/show_bookings/{id}', name:'show_bookings')]
    public function showBookings(User $user, BookingRepository $bookingRepository): Response
    {
        $bookings = $bookingRepository->findBookingWithoutOrdersByUSer($user->getId());

        return $this->render('BackOffice/CustomerAccount/Booking/show_bookings.html.twig', [
            'bookings' => $bookings
        ]);
    }

    #[Route('/booking/{id}', name: 'booking')]
    public function bookTable(Request $request, Restaurant $restaurant, SessionInterface $session): Response
    {
        $booking = new Booking();

        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            if('booking' === $form->getClickedButton()->getName()) {

                $this->denyAccessUnlessGranted('ROLE_CUSTOMER');
                $booking->setUser($this->getUser());            
                $restaurant->addBooking($booking);

                $this->entityManager->persist($restaurant);
                $this->entityManager->flush();

                $this->addFlash('success', 'Votre réservation a bien été prise en compte');
                
                return $this->redirectToRoute('show_bookings', ['id' => $this->getUser()->getId()]);
            } 

            $session->set('booking', $booking);

            return $this->redirectToRoute('order');
            
        }
        
        return $this->renderForm('FrontOffice/booking_restaurant.html.twig', [ 
            'restaurant' => $restaurant,
            'form' => $form
        ]);
    }

    #[Route('/edit_booking/{id}', name:'edit_booking')]
    public function editBooking(Request $request, Booking $booking): Response
    {
        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->entityManager->persist($booking);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre réservation a été modifiée');

            return $this->redirectToRoute('show_bookings', ['id' => $this->getUser()->getId()]);
        }

        return $this->renderForm('BackOffice/CustomerAccount/Booking/edit_booking.html.twig', [
            'booking' => $booking,
            'form' => $form
        ]);
    }

    #[Route('/delete_booking/{id}', name:'delete_booking')]
    public function deleteBooking(Request $request, Booking $booking): Response
    {        
        $this->entityManager->remove($booking);
        $this->entityManager->flush();

        $this->addFlash('success', 'Votre réservation a été annulée');

        return $this->redirectToRoute('show_bookings', ['id' => $this->getUser()->getId()]);
    }
}
