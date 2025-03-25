<?php

namespace App\Controller;

use App\Entity\Plant;
use App\Repository\PlantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/plants')]
final class PlantController extends AbstractController
{
    #[Route('', name: 'plant_list', methods: ['GET'])]
    public function list(
        Request $request,
        PlantRepository $plantRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        $page = max((int)$request->query->get('page', 1), 1);
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $plants = $plantRepository->findBy([], ['createdAt' => 'DESC'], $limit, $offset);

        $json = $serializer->serialize($plants, 'json', ['groups' => 'plant:read']);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'plant_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        $plant = $serializer->deserialize($request->getContent(), Plant::class, 'json', [
            'groups' => ['plant:write'],
        ]);

        $errors = $validator->validate($plant);
        if (count($errors) > 0) {
            return new JsonResponse((string)$errors, Response::HTTP_BAD_REQUEST);
        }

        $em->persist($plant);
        $em->flush();

        $json = $serializer->serialize($plant, 'json', ['groups' => 'plant:read']);

        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }

    #[Route('/{id}', name: 'plant_detail', methods: ['GET'])]
    public function detail(
        Plant $plant,
        SerializerInterface $serializer
    ): JsonResponse {
        $json = $serializer->serialize($plant, 'json', ['groups' => 'plant:read']);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'plant_update', methods: ['PUT', 'PATCH'])]
    public function update(
        Request $request,
        Plant $plant,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        $serializer->deserialize($request->getContent(), Plant::class, 'json', [
            'object_to_populate' => $plant,
            'groups' => ['plant:write'],
        ]);

        $errors = $validator->validate($plant);
        if (count($errors) > 0) {
            return new JsonResponse((string)$errors, Response::HTTP_BAD_REQUEST);
        }

        $em->flush();

        $json = $serializer->serialize($plant, 'json', ['groups' => 'plant:read']);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'plant_delete', methods: ['DELETE'])]
    public function delete(
        Plant $plant,
        EntityManagerInterface $em
    ): JsonResponse {
        $em->remove($plant);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
