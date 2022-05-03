<?php

declare(strict_types=1);

namespace App\Controller\ManagerController;

use App\Entity\Menu;
use App\Repository\MenuItemRepository;
use App\Services\MenuServices\RegisterMenu;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * class MenuController
 * 
 * Manage Menus(CRUD menus)
 */
#[Route('/manager')]
#[IsGranted('ROLE_MANAGER')]
final class MenuController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    #[Route('/menus/show', name: 'show_menus')]
    public function addMenu(Request $request, RegisterMenu $registerMenu): Response
    {
        $restaurant = $this->getUser()->getRestaurant();
        $menus = $restaurant->getMenus();
        $menuItems = $restaurant->getMenuItems();
        $errors = [];
        
        $token = $request->get('_csrf_token');
        
        if ($request->isMethod('POST') && $this->isCsrfTokenValid('token_id', $token)) {
            
            $formData = ($request->request->all())['menu'];    
            
            $response = $registerMenu->save($formData);

            if ($response instanceof Response) {
                return $response;
            }

            $errors = $response;
        }

        return $this->render('BackOffice/ManagerAccount/Menu/show_menus.html.twig', [
            'menus' => $menus,
            'menuItems' => $menuItems,
            'errors' => $errors
        ]);
    }

    #[Route('/menu/show/{id}', name: 'show_menu_details')]
    public function showMenuDetails(Menu $menu)
    {
        return $this->render('BackOffice/ManagerAccount/Menu/show_menu_details.html.twig', [
            'menu' => $menu
        ]);
    }

    #[Route('/menu/edit/{id}', name: 'edit_menu')]
    public function editMenu(Menu $menu, RegisterMenu $registerMenu, Request $request): Response
    {
        $restaurant = $this->getUser()->getRestaurant();
        $menuItems = $restaurant->getMenuItems();
        $errors = [];

        $token = $request->get('_csrf_token');

        if ($request->isMethod('POST') && $this->isCsrfTokenValid('token_id', $token)) {
            
            $formData = $request->request->all()['menu'];

            // Reset previous menuItems of menu.
            $menu->getMenuItems()->clear();

            $response = $registerMenu->save($formData, $menu);

            if ($response instanceof Response) {
                return $response;
            }

            $errors = $response;
        }

        return $this->renderForm('BackOffice/ManagerAccount/Menu/edit_menu.html.twig', [
            'menu' => $menu,
            'menuItems' => $menuItems, 
            'errors' => $errors
        ]);
    }

    #[Route('/delete_menu/{id}', name: 'delete_menu')]
    public function deleteMenu(Menu $menu): Response
    {
        $this->entityManager->remove($menu);
        $this->entityManager->flush();
        $this->addFlash('success', 'Menu supprimé.');

        return $this->redirectToRoute('show_menus');
    }
}