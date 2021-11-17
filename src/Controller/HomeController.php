<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\RestaurantSearch\RestaurantProvider;
use Knp\Component\Pager\PaginatorInterface;
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
    public function index(Request $request, RestaurantProvider $provider, PaginatorInterface $paginator): Response
    {
        $restaurants = null;

        if ($request->getMethod() === 'GET' && (!empty($request->query->get('restaurant_text')) or !empty($request->query->get('city_text')))) {
            $results = $provider->serve($request);

            $restaurants = $paginator->paginate(
                $results,
                $request->query->getInt('page', 1),
                20
            );

        }

        return $this->render('Frontend/Home/home.html.twig', ['restaurants' => $restaurants]);
    }
}
