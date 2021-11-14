<?php

declare(strict_types=1);

namespace App\Controller;

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
    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        if ($request->getMethod() === 'POST' && $request->get('restaurant')) {
            echo 'it\'s ok ';
        }

        return $this->render('Home/home.html.twig');
    }
}
