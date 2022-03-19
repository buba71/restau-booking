<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BookingOrder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class OrderStatusController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private NormalizerInterface $normalizer,
        private SerializerInterface $serializer
    ) {}
    
    #[Route('/order/validate/{id}', name: 'valid_order')]
    public function validOrder(BookingOrder $bookingOrder): JsonResponse
    {
        $bookingOrder->setStatus(BookingOrder::ORDER_VALIDATED);

        $this->entityManager->flush();
        $data = $this->serializer->serialize($bookingOrder, 'json', ['groups' => 'order:read']);        

        return new JsonResponse($data, 201);
    }
}