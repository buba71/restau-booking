<?php

declare(strict_types=1);

namespace App\Controller\ManagerController;

use App\Entity\Restaurant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mannager')]
final class OrderController extends AbstractController
{
    #[Route('/show_restaurant_orders/{id}', name: 'show_restaurant_orders')]
    public function showOrders(Restaurant $restaurant): Response
    {
        $bookings = $restaurant->getBookings();
        return $this->render('BackOffice/ManagerAccount/Order/show_orders.html.twig', [
            'bookings' => $bookings
        ]);
    }
}