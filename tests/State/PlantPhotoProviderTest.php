<?php

namespace State;

use ApiPlatform\Metadata\Operation;
use App\Entity\Plant;
use App\Entity\PlantPhoto;
use App\Repository\PlantPhotoRepository;
use App\Repository\PlantRepository;
use App\State\PlantPhotoProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PlantPhotoProviderTest extends TestCase
{
    public function testProvideThrowsIfPlantNotFound(): void
    {
        $plantRepo = $this->createMock(PlantRepository::class);
        $photoRepo = $this->createMock(PlantPhotoRepository::class);

        $plantRepo->method('find')->with(999)->willReturn(null);

        $provider = new PlantPhotoProvider($photoRepo, $plantRepo);
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Plante 999 inexistante');

        $provider->provide($this->createMock(Operation::class), ['plantId' => 999]);
    }

    public function testProvideReturnsPhotosForExistingPlant(): void
    {
        $plant = new Plant();
        $plant->setName('Cactus');

        $photo = new PlantPhoto();
        $photo->setPlant($plant);

        $plantRepo = $this->createMock(PlantRepository::class);
        $plantRepo->method('find')->with(1)->willReturn($plant);

        $photoRepo = $this->createMock(PlantPhotoRepository::class);
        $photoRepo->method('findBy')->with(['plant' => 1])->willReturn([$photo]);

        $provider = new PlantPhotoProvider($photoRepo, $plantRepo);
        $result = $provider->provide($this->createMock(Operation::class), ['plantId' => 1]);

        $this->assertIsIterable($result);
        $this->assertSame([$photo], iterator_to_array($result));
    }
}
