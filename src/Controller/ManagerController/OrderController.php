<?php

declare(strict_types=1);

namespace App\Controller\ManagerController;

use App\Repository\RestaurantRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mannager')]
#[IsGranted('ROLE_MANAGER')]
final class OrderController extends AbstractController
{
    #[Route('/show_restaurant_orders', name: 'show_restaurant_orders')]
    public function showOrders(RestaurantRepository $restaurantRepository): Response
    {
        $restaurant = $restaurantRepository->findOneBy(['user' => $this->getUser()->getId()]);
        $bookings = $restaurant->getBookings();

        return $this->render('BackOffice/ManagerAccount/Order/show_orders.html.twig', [
            'bookings' => $bookings
        ]);
    }
}