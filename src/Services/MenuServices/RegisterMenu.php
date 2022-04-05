<?php

declare(strict_types=1);

namespace App\Services\MenuServices;

use App\Entity\Menu;
use App\Repository\MenuItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

#[IsGranted('ROLE_MANAGER')]
final class RegisterMenu 
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MenuItemRepository $menuItemRepository,
        private RouterInterface $router,
        private Security $security,
        private SessionInterface $session,
        private ValidatorInterface $validator        
        ) {}

    public function save(array $formData = null, Menu $menu = null): mixed
    {
        $restaurant = $this->security->getUser()->getRestaurant();
        $message = 'Menu modifié avec succès.';

        // If new menu.
        if ($menu === null ) {

            $menu = new Menu();
            $message = 'Menu ajouté à votre restaurant.';
        }

        if (isset($formData['name'], $formData['description'], $formData['price'])) {

            // Check if at least one product was to be selected.
            if (!isset($formData['menuItems'])) {
                $formData['menuItems'] = '';
            }
            
            $menu->setName($formData['name']);
            $menu->setDescription($formData['description']);
            $menu->setPrice(floatval($formData['price']));

            if (is_array($formData['menuItems'])) {

                foreach ($formData['menuItems'] as $menuItem) {
                    $menuItem = $this->menuItemRepository->findOneBy(['id' => $menuItem]);
                    $menu->addMenuItem($menuItem);
                }
            }

            $errors = $this->validate($menu);

                if (count($errors) === 0) {

                    $restaurant->addMenu($menu);

                    $this->entityManager->persist($menu);
                    $this->entityManager->flush();

                    $this->session->getFlashBag()->add('success', $message);

                    return new RedirectResponse($this->router->generate('show_menus'));
                }

            return $errors;
        }
    }

    private function validate(Menu $menu)
    {
        return $this->validator->validate($menu);
    }
}