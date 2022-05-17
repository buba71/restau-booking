<?php

declare(strict_types=1);

namespace App\Tests\Functional\ManagerAccount;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BookinControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();        
    }

    public function testShowBookings(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => 'johnDoe@test.com']);

        $this->client->loginUser($user);

        $this->client->request('GET', '/manager/restaurant_bookings/show');

        static::assertResponseIsSuccessful();
    }
}