<?php

declare(strict_types=1);

namespace App\Controller\ManagerController;

use App\Form\RestaurantType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/manager')]
#[IsGranted('ROLE_MANAGER')]
final class RestaurantController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/show_restaurant', name: 'show_restaurant_details')]
    public function showDetails(): Response
    {
        $restaurant = $this->getUser()->getRestaurant();

        return $this->render('BackOffice/ManagerAccount/Restaurant/restaurant_details_show.html.twig', [
            'restaurant' => $restaurant
        ]);
    }

    #[Route('/restaurant_edit', name: 'restaurant_edit')]
    public function editRestaurant(Request $request): Response
    {
        $restaurant = $this->getUser()->getRestaurant();

        $form = $this->createForm(RestaurantType::class, $restaurant);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->entityManager->persist($restaurant);
            $this->entityManager->flush();

            $this->addFlash('success', 'Les informations du restaurant ont été modifiées.');

            return $this->redirectToRoute('show_restaurant_details');

        }
        return $this->renderForm('BackOffice/ManagerAccount/Restaurant/restaurant_edit.html.twig', [
            'form' => $form
        ]);
    }
}