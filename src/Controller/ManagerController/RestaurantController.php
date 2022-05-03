<?php

declare(strict_types=1);

namespace App\Controller\ManagerController;

use App\Form\RestaurantType;
use App\Services\UploadFilesServices\ImageUpLoaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/manager')]
#[IsGranted('ROLE_MANAGER')]
final class RestaurantController extends AbstractController
{
    public function __construct(private KernelInterface $kernel, private EntityManagerInterface $entityManager) {}

    #[Route('/show_restaurant', name: 'show_restaurant_details')]
    public function showDetails(): Response
    {
        $restaurant = $this->getUser()->getRestaurant();

        return $this->render('BackOffice/ManagerAccount/Restaurant/restaurant_details_show.html.twig', [
            'restaurant' => $restaurant
        ]);
    }

    #[Route('/restaurant_edit', name: 'restaurant_edit')]
    public function editRestaurant(ImageUpLoaderHelper $imageUpLoaderHelper, Request $request): Response
    {
        $restaurant = $this->getUser()->getRestaurant();

        $restaurantImage = $restaurant->getImageFilePath();

        $form = $this->createForm(RestaurantType::class, $restaurant);

        if($restaurantImage !== null) {
            // Set ImageFile field.
            $oldFileUploadedPath = $this->getParameter('kernel.project_dir') . '/public' . $restaurantImage;
            $form->get('imageFile')->setData(new File($oldFileUploadedPath));
        }        

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $uploadedFile = $form['imageFile']->getData();

            if ($uploadedFile) {
                $newFileName = $imageUpLoaderHelper->uploadRestaurantImage($uploadedFile);
                $restaurant->setImageFilePath($this->getParameter('image_directory') . $newFileName);
                unlink($oldFileUploadedPath);
            }
            
            $this->entityManager->flush();

            $this->addFlash('success', 'Les informations du restaurant ont été modifiées.');

            return $this->redirectToRoute('show_restaurant_details');

        }
        return $this->renderForm('BackOffice/ManagerAccount/Restaurant/restaurant_edit.html.twig', [
            'form' => $form
        ]);
    }
}