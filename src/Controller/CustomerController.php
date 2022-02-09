<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/customer')]
final class CustomerController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/show_bookings', name:'show_bookings')]
    public function showBookings(): Response
    {
        // TODO get bookings by user parameter.
        $bookings = $this->entityManager->getRepository(Booking::class)->findBy([], ['bookingDate' => 'asc']);


        return $this->render('BackOffice/CustomerAccount/Booking/show_bookings.html.twig', [
            'bookings' => $bookings
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

            return $this->redirectToRoute('show_bookings');
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

        return $this->redirectToRoute('show_bookings');
    }

    #[Route('/show_bookingOrders', name: 'show_customer_orders')]
    public function showBookingOrders(BookingOrderRepository $bookingOrderRepository): Response
    {
        $orders = $bookingOrderRepository->findAllOrderByBookingDate();

        return $this->render('BackOffice/CustomerAccount/Booking/show_booking_orders.html.twig', [
            'orders' => $orders
        ]);
    }
}
