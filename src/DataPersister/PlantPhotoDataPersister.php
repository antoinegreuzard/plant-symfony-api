<?php

namespace App\DataPersister;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\PlantPhoto;
use App\Repository\PlantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class PlantPhotoDataPersister implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private PlantRepository $plantRepository
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof PlantPhoto) {
            return $data;
        }

        $plantId = $uriVariables['plantId'] ?? null;

        if (!$plantId) {
            throw new NotFoundHttpException('Missing plant ID in URI.');
        }

        $plant = $this->plantRepository->find($plantId);
        if (!$plant) {
            throw new NotFoundHttpException("Plant with id $plantId not found.");
        }

        $data->setPlant($plant);

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}
