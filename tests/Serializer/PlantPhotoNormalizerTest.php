<?php

namespace Serializer;

use App\Entity\Plant;
use App\Entity\PlantPhoto;
use App\Serializer\PlantPhotoNormalizer;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PlantPhotoNormalizerTest extends TestCase
{
    public function testSupportsNormalization(): void
    {
        $normalizer = new PlantPhotoNormalizer(
            $this->createMock(NormalizerInterface::class),
            new RequestStack()
        );

        $photo = $this->createMock(PlantPhoto::class);

        $this->assertTrue($normalizer->supportsNormalization($photo, 'json'));
        $this->assertFalse($normalizer->supportsNormalization($photo, 'xml'));
        $this->assertFalse($normalizer->supportsNormalization(new stdClass(), 'json'));
    }

    public function testNormalize(): void
    {
        $plant = $this->createMock(Plant::class);
        $plant->method('getId')->willReturn(42);

        $photo = $this->createMock(PlantPhoto::class);
        $photo->method('getPlant')->willReturn($plant);
        $photo->method('getImage')->willReturn('photo.jpg');
        $photo->method('getUploadedAt')->willReturn(new DateTimeImmutable('2024-03-26 14:30'));

        $decorated = $this->createMock(NormalizerInterface::class);
        $decorated->method('normalize')->willReturn(['caption' => 'Test caption']);

        $request = new Request([], [], [], [], [], ['HTTP_HOST' => 'localhost', 'REQUEST_SCHEME' => 'http']);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $normalizer = new PlantPhotoNormalizer($decorated, $requestStack);

        $normalized = $normalizer->normalize($photo, 'json');

        $this->assertEquals([
            'caption' => 'Test caption',
            'plant_id' => 42,
            'image' => 'http://localhost/storage/plant_photos/photo.jpg',
            'uploaded_at' => '2024-03-26T14:30:00+00:00',
        ], $normalized);
    }
}
