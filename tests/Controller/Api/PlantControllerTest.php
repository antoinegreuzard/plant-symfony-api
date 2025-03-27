<?php

namespace Controller\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PlantControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private string $token;

    public function testIndexWithoutSlash(): void
    {
        $this->client->request('GET', '/api/plants', [], [], [
            'HTTP_Authorization' => 'Bearer '.$this->token,
        ]);

        self::assertResponseIsSuccessful();
        self::assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testPostPlant(): void
    {
        $payload = [
            'name' => 'Plante_'.uniqid(),
            'plant_type' => 'indoor',
            'location' => 'Salon',
            'description' => 'Test description',
        ];

        $this->client->request('POST', '/api/plants/', [], [], [
            'HTTP_Authorization' => 'Bearer '.$this->token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($payload));

        self::assertResponseStatusCodeSame(201);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('id', $responseData);
    }
    
    protected function setUp(): void
    {
        $this->client = PlantControllerTest::createClient();
        $this->token = $this->authenticate();
    }

    private function authenticate(): string
    {
        $this->client->request(
            'POST',
            '/api/token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'antoine.greuzard',
                'password' => 'antoine',
            ])
        );

        self::assertResponseIsSuccessful();

        $data = json_decode($this->client->getResponse()->getContent(), true);

        return $data['access'] ?? '';
    }
}
