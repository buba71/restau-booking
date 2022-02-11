<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\MenuItem;
use App\Form\MenuType;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use DateTime;
use DateTimeImmutable;
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
        $formData = $request->request->all();
        $errors = [];

        $menu = new Menu();

        if ($request->isMethod('POST')) {

            if (isset($formData['menu']['name']) && isset($formData['menu']['description']) && isset($formData['menu']['menuItems'])) {
                    
                $menu->setName($formData['menu']['name']);
                $menu->setDescription($formData['menu']['description']);

                $price = 0;
    
                foreach ($formData['menu']['menuItems'] as $menuItem) {
                    $menuItem = $menuItemRepository->findOneBy(['id' => $menuItem]);
                    $menu->addMenuItem($menuItem);
                    $price += $menuItem->getPrice();
                }  
                
                $menu->setPrice($price);
    
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