<?php

namespace Entity;

use App\Entity\Plant;
use App\Entity\PlantPhoto;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

class PlantPhotoTest extends TestCase
{
    public function testCanSetAndGetBasicData(): void
    {
        $plant = new Plant();
        $plant->setName('Aloe Vera');

        $photo = new PlantPhoto();
        $photo->setPlant($plant)
            ->setImage('aloe-vera.jpg')
            ->setCaption('Photo du jour');

        $this->assertSame($plant, $photo->getPlant());
        $this->assertEquals('aloe-vera.jpg', $photo->getImage());
        $this->assertEquals('Photo du jour', $photo->getCaption());
        $this->assertSame($plant->getId(), $photo->getPlantId()); // null en théorie
    }

    public function testSetImageFileUpdatesUploadedAt(): void
    {
        $photo = new PlantPhoto();
        $file = new File(__FILE__); // N'importe quel fichier pour le test

        $photo->setImageFile($file);
        $this->assertInstanceOf(DateTimeImmutable::class, $photo->getUploadedAt());
    }

    public function testSetUploadedAtManually(): void
    {
        $photo = new PlantPhoto();
        $date = new DateTimeImmutable('2025-03-26');

        $photo->setUploadedAtManually($date);
        $this->assertSame($date, $photo->getUploadedAt());
    }

    public function testToString(): void
    {
        $plant = new Plant();
        $plant->setName('Ficus');

        $photo = new PlantPhoto();
        $photo->setPlant($plant);
        $photo->setUploadedAtManually(new DateTimeImmutable('2025-03-26'));

        $this->assertStringContainsString('Photo de Ficus - 2025-03-26', (string)$photo);
    }
}
