<?php

namespace App\Serializer;

use ArrayObject;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

readonly class SnakeCaseNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function __construct(
        private NormalizerInterface $normalizer,
        private CamelCaseToSnakeCaseNameConverter $nameConverter = new CamelCaseToSnakeCaseNameConverter()
    ) {
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $this->normalizer->supportsNormalization($data, $format);
    }

    public function normalize(
        mixed $object,
        ?string $format = null,
        array $context = []
    ): array|ArrayObject|string|int|float|bool|null {
        $data = $this->normalizer->normalize($object, $format, $context);

        if (!is_array($data)) {
            return $data;
        }

        $converted = [];
        foreach ($data as $key => $value) {
            $converted[$this->nameConverter->normalize($key)] = $value;
        }

        return $converted;
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = []
    ): bool {
        return $this->normalizer->supportsDenormalization($data, $type, $format);
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (!is_array($data)) {
            return $data;
        }

        $converted = [];
        foreach ($data as $key => $value) {
            $converted[$this->nameConverter->denormalize($key)] = $value;
        }

        return $this->normalizer->denormalize($converted, $type, $format, $context);
    }

    public function getSupportedTypes(?string $format): array
    {
        return ['object' => true];
    }
}
