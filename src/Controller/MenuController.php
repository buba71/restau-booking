<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\MenuItem;
use App\Form\MenuType;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/manager')]
final class MenuController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/show_menus', name: 'show_menus')]
    public function showDishes(Request $request): Response
    {
        $menus = $this->entityManager->getRepository(Menu::class)->findAll();

        $menu = new Menu();

        $form = $this->createForm(MenuType::class, $menu);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($menu);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre menu a été créé!');

            return $this->redirectToRoute('show_menus');
        }

        return $this->renderForm('BackOffice/ManagerAccount/menu/show_menus.html.twig', [
            'menus' => $menus,
            'form' => $form
        ]);
    }

    #[Route('/delete_menu/{id}', name: 'delete_menu')]
    public function deleteMenu(Menu $menu)
    {
        $this->entityManager->remove($menu);
        $this->entityManager->flush();
        $this->addFlash('success', 'Menu supprimé.');

        return $this->redirectToRoute('show_menus');
    }
}