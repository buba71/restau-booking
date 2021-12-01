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
        $data = null;
        $city = null;

        if ($request->getMethod() === 'GET') {
            
            $restaurants = $provider->serve($request);

            if ($restaurants !== null) {
                $data = $paginator->paginate($restaurants, $request->query->getInt('page', 1), 20);
            }

            $city = $request->query->get('city');
        }

        return $this->render('Frontend/Home/home.html.twig', ['restaurants' => $data,'city' => $city]);
    }
}
