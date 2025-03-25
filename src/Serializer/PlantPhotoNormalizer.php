<?php

namespace App\Serializer;

use ApiPlatform\State\ApiResource\Error;
use App\Entity\PlantPhoto;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

readonly class PlantPhotoNormalizer implements NormalizerInterface
{
    public function __construct(
        private NormalizerInterface $normalizer,
        private RequestStack $requestStack
    ) {
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if ($data instanceof Error) {
            return false;
        }

        return $format === 'json' && $data instanceof PlantPhoto;
    }

    public function normalize(
        mixed $object,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|null {
        /** @var PlantPhoto $object */
        $data = $this->normalizer->normalize($object, $format, $context);

        $request = $this->requestStack->getCurrentRequest();
        $baseUrl = $request ? $request->getSchemeAndHttpHost() : '';

        $data['plant_id'] = $object->getPlant()?->getId();
        $data['image'] = $object->getImage() ? $baseUrl.'/storage/plant_photos/'.$object->getImage() : null;
        $data['uploaded_at'] = $object->getUploadedAt()?->format(DATE_ATOM);

        return $data;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [PlantPhoto::class => true];
    }
}
