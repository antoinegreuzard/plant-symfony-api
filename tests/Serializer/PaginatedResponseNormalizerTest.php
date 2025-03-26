<?php

namespace Serializer;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\PaginatorInterface;
use App\Serializer\PaginatedResponseNormalizer;
use PHPUnit\Framework\TestCase;
use stdClass;
use Stub\StubPaginator;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PaginatedResponseNormalizerTest extends TestCase
{
    public function testSupportsNormalizationOnlyForPaginatorAndJson(): void
    {
        $decorated = $this->createMock(NormalizerInterface::class);
        $normalizer = new PaginatedResponseNormalizer($decorated);

        $paginator = $this->createMock(PaginatorInterface::class);

        $this->assertTrue($normalizer->supportsNormalization($paginator, 'json'));
        $this->assertFalse($normalizer->supportsNormalization(new stdClass(), 'json'));
        $this->assertFalse($normalizer->supportsNormalization($paginator, 'xml'));
    }

    public function testNormalizeReturnsDecoratedOutputIfContextAlreadyCalled(): void
    {
        $decorated = $this->createMock(NormalizerInterface::class);
        $decorated->method('normalize')->willReturn(['data' => 'test']);

        $normalizer = new PaginatedResponseNormalizer($decorated);
        $result = $normalizer->normalize([], 'json', ['already_called' => true]);

        $this->assertSame(['data' => 'test'], $result);
    }

    public function testNormalizeReturnsPaginatedStructure(): void
    {
        $decorated = $this->createMock(NormalizerInterface::class);
        $decorated->method('normalize')->willReturnCallback(fn($data) => ['normalized' => $data]);

        $paginator = new StubPaginator();

        $operation = $this->createMock(Operation::class);
        $context = [
            'operation' => $operation,
            'request_uri' => '/api/plants?page=2',
        ];

        $normalizer = new PaginatedResponseNormalizer($decorated);
        $result = $normalizer->normalize($paginator, 'json', $context);

        $this->assertEquals(100, $result['count']);
        $this->assertEquals('/api/plants?page=3', $result['next']);
        $this->assertEquals('/api/plants?page=1', $result['previous']);
        $this->assertCount(2, $result['results']);
    }
}
