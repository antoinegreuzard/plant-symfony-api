<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Plant;
use App\Service\PlantAdviceService;

readonly class PlantStateProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $decorated,
        private PlantAdviceService $adviceService,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $result = $this->decorated->provide($operation, $uriVariables, $context);

        if ($result instanceof Plant) {
            $result->advice = $this->adviceService->getPersonalizedAdvice($result);
        }

        return $result;
    }
}
