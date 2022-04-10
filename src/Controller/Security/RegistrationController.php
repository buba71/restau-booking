<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Entity\TimeSlot;
use App\Entity\User;
use App\Form\CustomerType;
use App\Form\ManagerType;
use App\Services\UploadFilesServices\ImageUpLoaderHelper;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

final class RegistrationController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        date_default_timezone_set('Europe/Paris');
    }

    #[Route('/register_customer', name: 'register_customer')]
    public function registerCustomer(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();

        $form = $this->createForm(CustomerType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {            

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $user->setRoles(['ROLE_CUSTOMER']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->renderForm('Security/Register/Customer/index.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/register_manager', name: 'register_manager')]
    public function registerManager(ImageUpLoaderHelper $imageUpLoaderHelper, Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();

        $form = $this->createForm(ManagerType::class, $user);
        $form['restaurant']->remove('orderEnabled'); // remove field
        $form['restaurant']->remove('bookingEnabled'); // remove field

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $restaurant = $user->getRestaurant();

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $user->setRoles(['ROLE_MANAGER']);
           
            // Upload image background
            $uploadedFile = $form['restaurant']['imageFile']->getData();
            
            if ($uploadedFile) {
                $newFileName = $imageUpLoaderHelper->uploadRestaurantImage($uploadedFile);
                $restaurant->setImageFilePath($this->getParameter('image_directory') . $newFileName);
            }
            
            // TODO refacto set default timeSlots to unix timestamp with timeslot time.
            
            $startAtAm = new DateTime('@-0');
            $startAtAm->setTime(10, 00);
            $closeAtAm = new DateTime('@-0');
            $closeAtAm->setTime(14, 00);
            $startAtPm = new DateTime('@-0');
            $startAtPm->setTime(18, 00);
            $closeAtPm = new DateTime('@-0');
            $closeAtPm->setTime(22, 00);
            
            $user->getRestaurant()->setUser($user);

            for ($i = 1; $i < 8; $i++) {

                $timeSlot = new TimeSlot();
                $timeSlot->setServiceStartAtAm($startAtAm);
                $timeSlot->setServiceCloseAtAm($closeAtAm);
                $timeSlot->setServiceStartAtPm($startAtPm);
                $timeSlot->setServiceCloseAtPm($closeAtPm);
                $timeSlot->setIntervalTime(30);

                $timeSlot->setDayOfWeek($i);
                $restaurant->addTimeSlot($timeSlot);
            }
            $restaurant->getTimeSlots()->last()->setDayOfWeek(0);

            // Make class

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->renderForm('Security/Register/Manager/index.html.twig', [
            'form' => $form
        ]);
    }
}