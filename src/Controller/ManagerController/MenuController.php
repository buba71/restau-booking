<?php

declare(strict_types=1);

namespace App\Controller\ManagerController;

use App\Entity\Menu;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/manager')]
final class MenuController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/show_menus', name: 'show_menus')]
    public function showDishes(Request $request, MenuRepository $menuRepository, MenuItemRepository $menuItemRepository, ValidatorInterface $validator): Response
    {
        $menus = $menuRepository->findAll();
        $menuItems = $menuItemRepository->findAll();  
        
        $errors = [];
        
        $token = $request->get('_csrf_token');
        
        if ($request->isMethod('POST') && $this->isCsrfTokenValid('token_id', $token)) {

            $formData = ($request->request->all())['menu'];            

            if (isset($formData['name'], $formData['description'], $formData['menuItems'], $formData['price'])) {
                
                $menu = new Menu();
                
                $menu->setName($formData['name']);
                $menu->setDescription($formData['description']);
                $menu->setPrice(floatval($formData['price']));
    
                foreach ($formData['menuItems'] as $menuItem) {
                    $menuItem = $menuItemRepository->findOneBy(['id' => $menuItem]);
                    $menu->addMenuItem($menuItem);
                }
    
                $errors = $validator->validate($menu);

                if (count($errors) === 0) {
                    $this->entityManager->persist($menu);
                    $this->entityManager->flush();
                    
                    return $this->redirectToRoute('show_menus');
                }
            }
            
        }
        

        return $this->render('BackOffice/ManagerAccount/menu/show_menus.html.twig', [
            'menus' => $menus,
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