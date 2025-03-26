<?php

namespace Serializer;

use App\Serializer\SnakeCaseNormalizer;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SnakeCaseNormalizerTest extends TestCase
{
    public function testNormalizeConvertsKeysToSnakeCase(): void
    {
        $innerNormalizer = $this->createMock(NormalizerInterface::class);
        $innerNormalizer->method('supportsNormalization')->willReturn(true);
        $innerNormalizer->method('normalize')->willReturn([
            'camelCaseKey' => 'value',
            'anotherKeyHere' => 'test',
        ]);

        $normalizer = new SnakeCaseNormalizer($innerNormalizer);

        $result = $normalizer->normalize(new stdClass(), 'json');

        $this->assertArrayHasKey('camel_case_key', $result);
        $this->assertArrayHasKey('another_key_here', $result);
        $this->assertEquals('value', $result['camel_case_key']);
        $this->assertEquals('test', $result['another_key_here']);
    }

    public function testDenormalizeConvertsKeysToCamelCase(): void
    {
        $mockNormalizer = new class implements NormalizerInterface, DenormalizerInterface {
            public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
            {
                return true;
            }

            public function normalize(mixed $object, ?string $format = null, array $context = []): array
            {
                return [];
            }

            public function supportsDenormalization(
                mixed $data,
                string $type,
                ?string $format = null,
                array $context = []
            ): bool {
                return true;
            }

            public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
            {
                $obj = new stdClass();
                foreach ($data as $key => $value) {
                    $obj->{$key} = $value;
                }

                return $obj;
            }

            public function getSupportedTypes(?string $format): array
            {
                return ['object' => true];
            }
        };

        $normalizer = new SnakeCaseNormalizer($mockNormalizer);

        $result = $normalizer->denormalize([
            'camel_case_key' => 'value',
            'another_key_here' => 'test',
        ], stdClass::class, 'json');

        $this->assertInstanceOf(stdClass::class, $result);
        $this->assertEquals('value', $result->camelCaseKey);
        $this->assertEquals('test', $result->anotherKeyHere);
    }
}
