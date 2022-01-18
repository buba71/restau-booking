<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Restaurant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CustomerController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/customer/booking_details', name:'booking_details')]
    public function showBookings(Request $request): Response
    {
        // TODO get restaurant as request parameter.
        $restaurant = $this->entityManager->getRepository(Restaurant::class)->findOneBy(['id' => 1]);

        $bookings = $restaurant->getBookings();


        return $this->render('BackOffice/CustomerAccount/Booking//booking_details.html.twig', [
            'bookings' => $bookings
        ]);
    }

    public function bookWithOrder(Request $request): Response
    {

    }
}
