<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Restaurant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BookingController extends AbstractController
{
    #[Route('/booking/{id}', name: 'booking')]
    /**
     * 
     * @return Response
     */
    public function bookTable(Restaurant $restaurant): Response
    {
        return $this->render('FrontOffice/booking.html.twig', [ 'restaurant' => $restaurant ]);
    }
}