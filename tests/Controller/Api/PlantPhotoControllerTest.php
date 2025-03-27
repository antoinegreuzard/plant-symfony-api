<?php

namespace Controller\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class PlantPhotoControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function testListPhotos(): void
    {
        $token = $this->getToken();

        $this->client->request('GET', '/api/plants/1/photos', [], [], [
            'HTTP_Authorization' => 'Bearer '.$token,
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(200);
        self::assertJson($this->client->getResponse()->getContent());
    }

    private function getToken(): string
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

    public function testUploadPhoto(): void
    {
        $token = $this->getToken();

        // Génére un faux fichier JPEG temporaire
        $tempFilePath = tempnam(sys_get_temp_dir(), 'test');
        imagejpeg(imagecreatetruecolor(10, 10), $tempFilePath); // Crée un petit carré noir

        $file = new UploadedFile(
            $tempFilePath,
            'test.jpg',
            'image/jpeg',
            null,
            true
        );

        $this->client->request(
            'POST',
            '/api/plants/1/upload-photo/',
            ['caption' => 'Test caption'],
            ['imageFile' => $file],
            ['HTTP_Authorization' => 'Bearer '.$token]
        );

        self::assertResponseStatusCodeSame(201);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('id', $data);
        self::assertSame('Test caption', $data['caption']);
    }

    public function testUploadPhotoWithoutFile(): void
    {
        $token = $this->getToken();

        $this->client->request(
            'POST',
            '/api/plants/1/upload-photo/',
            ['caption' => 'No file'],
            [],
            ['HTTP_Authorization' => 'Bearer '.$token]
        );

        self::assertResponseStatusCodeSame(400);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = PlantPhotoControllerTest::createClient();
    }
}
