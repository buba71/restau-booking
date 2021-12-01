<?php

declare(strict_types=1);

namespace tests\functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class HomeControllerTest
 * @package tests\functional
 */
final class HomeControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testHome(): void
    {

        $this->client->request('GET', '/');

        static::assertResponseIsSuccessful();
    }

    public function testIfFindRestaurantByName(): void
    {
        $crawler = $this->client->request('GET', '/');

        $form = $crawler->selectButton('rechercher')->form();
        
        $form['restaurant'] = 'Le Restau 10';
        $this->client->submit($form);

        static::assertResponseIsSuccessful();
        static::assertSelectorTextContains('h2', 'Il éxiste 1 restaurant');
    }

    public function testIfFindRestaurantBySpeciality(): void
    {
        $crawler = $this->client->request('GET', '/');

        $form = $crawler->selectButton('rechercher')->form();
        
        $form['speciality'] = 'Cuisine française';
        $this->client->submit($form);

        static::assertResponseIsSuccessful();
        static::assertSelectorTextContains('h2', 'Il éxiste 9 restaurants');
    }

    public function testIfFindRestaurantByCity(): void
    {
        $crawler = $this->client->request('GET', '/');

        $form = $crawler->selectButton('rechercher')->form();
        
        $form['city'] = 'Paris';
        $this->client->submit($form);

        static::assertResponseIsSuccessful();
        static::assertSelectorTextContains('h2', 'Il éxiste 11 restaurants');
    }

    public function testIfFindRestaurantBySpecialityAndCity(): void
    {
        $crawler = $this->client->request('GET', '/');

        $form = $crawler->selectButton('rechercher')->form();

        $form['speciality'] = 'Cuisine Asiatique';
        $form['city'] = 'Paris';
        $this->client->submit($form);

        static::assertResponseIsSuccessful();
        static::assertSelectorTextContains('h2', 'Il éxiste 11 restaurants');
    }
}
