<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\PlantPhotoRepository;
use App\Repository\PlantRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PlantPhotoProvider implements ProviderInterface
{
    public function __construct(
        private readonly PlantPhotoRepository $photoRepo,
        private readonly PlantRepository $plantRepo
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        $plantId = $uriVariables['plantId'] ?? null;

        if (!$plantId || !$this->plantRepo->find($plantId)) {
            throw new NotFoundHttpException("Plante $plantId inexistante");
        }

        return $this->photoRepo->findBy(['plant' => $plantId]);
    }
}
