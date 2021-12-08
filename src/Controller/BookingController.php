<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Restaurant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class BookingController extends AbstractController
{
    #[Route('/booking/{id}', name: 'booking')]
    public function bookTable(Restaurant $restaurant)
    {
        return $this->render('Frontend/booking.html.twig', [ 'restaurant' => $restaurant ]);
    }
}