<?php

namespace Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PlantControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/plant');

        self::assertResponseIsSuccessful();
    }
}
