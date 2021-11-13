<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Restaurant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 *
 * @package App\Controller
 */
final class HomeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $restaurant = $this->getDoctrine()->getRepository(Restaurant::class)->findBy(['id' => 1]);
        dd($restaurant);

        return $this->render('base.html.twig');
    }
}
