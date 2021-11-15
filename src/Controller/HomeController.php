<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\RestaurantSearch\RestaurantProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 *
 * @package App\Controller
 */
final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, RestaurantProvider $provider): Response
    {
        $restaurants = null;

        if ($request->getMethod() === 'POST') {
            $restaurants = $provider->serve($request);
        }

        return $this->render('Frontend/Home/home.html.twig', ['restaurants' => $restaurants]);
    }
}
