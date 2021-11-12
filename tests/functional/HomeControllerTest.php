<?php

declare(strict_types=1);

namespace tests\functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class HomeControllerTest
 * @package tests\functional
 */
class HomeControllerTest extends WebTestCase
{
    public function testHome(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        static::assertResponseIsSuccessful();
    }
}
