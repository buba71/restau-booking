<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart')]
final class CartController extends AbstractController
{
    public function __construct(private RequestStack $request) {}

    #[Route('/index', name: 'cart_index')]
    public function index(MenuItemRepository $menuItemRepository, MenuRepository $menuRepository, SessionInterface $session): Response
    { 
        //$cart = $session->set('cart', []);
        $cart = $session->get('cart', []);
        
        $cartData = [];
        $menuData =[];
        $menuItemData = [];
        $total = 0;
        
        if (isset($cart['menu'])) {
            foreach ($cart['menu'] as $id => $quantity) {
                $menuData[] = [
                    'product' => $menuRepository->findOneBy(['id' => $id]),
                    'quantity' => $quantity
                ];
            }

            // Sort Menus by name asc.
            uasort($menuData, fn ($a, $b) => $a['product']->getName() > $b['product']->getName());
        }

        if (isset($cart['menuItem'])) {
            foreach ($cart['menuItem'] as $id => $quantity) {
                $menuItemData[] = [
                    'product' => $menuItemRepository->findOneBy(['id' => $id]),
                    'quantity' => $quantity
                ];
            }

            // Sort menuItem by name asc.
            uasort($menuItemData, fn ($a, $b) => $a['product']->getName() > $b['product']->getName());
        }  
        
        $cartData = [...$menuItemData, ...$menuData];
        
        $total = 0;

        foreach ($cartData as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }
            

        return $this->render('FrontOffice/Restaurant/Cart/cart.html.twig', [
            'cart' => $cartData,
            'total' => $total
        ]);    
    }

    /**
     * Add Menu to cart line with id = $id.
     */
    #[Route('/menu/add/{id}', name: 'add_menu_to_cart')]
    public function addMenu(int $id, SessionInterface $session): Response
    {      
        if ($this->request->getCurrentRequest()->isMethod('POST')) {

            $sessionCart = $session->get('cart', []);

            if(!empty($sessionCart['menu'][$id])) {            

                $sessionCart['menu'][$id]++;

            } else {

                $sessionCart['menu'][$id] = 1;
            }

            $session->set('cart', $sessionCart); 

        }             
        
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/menu/remove/{id}', name: 'remove_menu_from_cart')]
    public function removeMenu(int $id, SessionInterface $session): Response
    {
        if ($this->request->getCurrentRequest()->isMethod('POST')) {
            
            $sessionCart = $session->get('cart', []);

            if($sessionCart['menu'][$id] && $sessionCart['menu'][$id] > 2) {
                 
                $sessionCart['menu'][$id]--;
            
            } else {
                 
                $sessionCart['menu'][$id] = 1;
            }
             
            $session->set('cart', $sessionCart); 

        }
               
         
        return $this->redirectToRoute('cart_index');
    }

    /**
     * Add MenuItem to cart line with id = $id.
     */
    #[Route('/menu-item/add/{id}', name: 'add_menu_item_to_cart')]
    public function addMenuItem(int $id, SessionInterface $session): Response
    {   
        if ($this->request->getCurrentRequest()->isMethod('POST')) {

            $sessionCart = $session->get('cart', []);

            if(!empty($sessionCart['menuItem'][$id])) {            

                $sessionCart['menuItem'][$id]++;

            } else {

                $sessionCart['menuItem'][$id] = 1;
            }

            $session->set('cart', $sessionCart);  
        }            
        
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/menu-item/remove/{id}', name: 'remove_menu_item_from_cart')]
    public function removeMenuItem(int $id, SessionInterface $session): Response
    {
        if ($this->request->getCurrentRequest()->isMethod('POST')) {

            $sessionCart = $session->get('cart', []);

            if($sessionCart['menuItem'][$id] && $sessionCart['menuItem'][$id] > 2) {

                $sessionCart['menuItem'][$id]--;
            } else {

                $sessionCart['menuItem'][$id] = 1;
            }

            $session->set('cart', $sessionCart);  

        }              
        
        return $this->redirectToRoute('cart_index');
    }

    /**
     * Delete Menu cart line where menu id = $id.
     */
    #[Route('/menu/remove_cartLine/{id}', name: 'remove_menu_cart_line')]
    public function deleteMenu(int $id, SessionInterface $session): Response
    {
        if ($this->request->getCurrentRequest()->isMethod('POST')) {

            $sessionCart = $session->get('cart', []);

            if(isset($sessionCart['menu']) && $sessionCart['menu'][$id]) {
                unset($sessionCart['menu'][$id]);
            }

            $session->set('cart', $sessionCart); 
        }               

        return $this->redirectToRoute('cart_index');
    }

    /**
     * Delete all MenuItem cart line where menuItem id = $id.
     */
    #[Route('/menu-item/remove_cartLine/{id}', name: 'remove_menuItem_cart_line')]
    public function deleteMenuItem(int $id, SessionInterface $session): Response
    {
        if ($this->request->getCurrentRequest()->isMethod('POST')) {

            $sessionCart = $session->get('cart', []);

            if(isset($sessionCart['menuItem']) && $sessionCart['menuItem'][$id]) {
                unset($sessionCart['menuItem'][$id]);
            }

            $session->set('cart', $sessionCart);
        }                

        return $this->redirectToRoute('cart_index');  
    }
}
