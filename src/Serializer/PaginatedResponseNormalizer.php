<?php

namespace App\Serializer;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ApiResource\Error;
use ApiPlatform\State\Pagination\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

readonly class PaginatedResponseNormalizer implements NormalizerInterface
{
    public function __construct(private NormalizerInterface $decorated)
    {
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        if (($context['already_called'] ?? false) === true) {
            return $this->decorated->normalize($data, $format, $context);
        }

        $context['already_called'] = true;

        $normalized = $this->decorated->normalize($data, $format, $context);

        if (
            !isset($context['operation']) ||
            !$context['operation'] instanceof Operation ||
            !$data instanceof PaginatorInterface ||
            !is_array($normalized)
        ) {
            return $normalized;
        }

        return [
            'count' => $data->getTotalItems(),
            'next' => $this->getNextPageUrl($data, $context),
            'previous' => $this->getPreviousPageUrl($data, $context),
            'results' => array_map(
                fn($item) => $this->decorated->normalize($item, $format, $context),
                iterator_to_array($data)
            ),
        ];
    }

    private function getNextPageUrl(PaginatorInterface $paginator, array $context): ?string
    {
        if ($paginator->getCurrentPage() < $paginator->getLastPage()) {
            return $this->buildPageUrl($paginator->getCurrentPage() + 1, $context);
        }

        return null;
    }

    private function buildPageUrl(int $page, array $context): string
    {
        $uri = $context['request_uri'] ?? '';
        $separator = str_contains($uri, '?') ? '&' : '?';

        // Supprime une éventuelle page=X existante
        $uri = preg_replace('/[?&]page=\d+/', '', $uri);

        return $uri.$separator.'page='.$page;
    }

    private function getPreviousPageUrl(PaginatorInterface $paginator, array $context): ?string
    {
        if ($paginator->getCurrentPage() > 1) {
            return $this->buildPageUrl($paginator->getCurrentPage() - 1, $context);
        }

        return null;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if ($data instanceof Error) {
            return false;
        }
        
        return $format === 'json' && $data instanceof PaginatorInterface;
    }

    public function getSupportedTypes(?string $format): array
    {
        return ['object' => true];
    }
}
