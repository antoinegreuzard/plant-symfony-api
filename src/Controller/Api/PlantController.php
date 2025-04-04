<?php

namespace App\Controller\Api;

use App\Entity\Plant;
use App\Service\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/plants')]
class PlantController extends AbstractController
{
    #[Route(['', '/'], name: 'api_plants_list', methods: ['GET'])]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        Paginator $paginator
    ): JsonResponse {
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = 10;

        $query = $em->createQueryBuilder()
            ->select('p')
            ->from(Plant::class, 'p')
            ->where('p.user = :user')
            ->setParameter('user', $this->getUser())
            ->orderBy('p.createdAt', 'DESC');

        $data = $paginator->paginate($query, $page, $limit, 'api_plants_list');

        $json = $serializer->serialize($data['results'], 'json', ['groups' => 'plant:read']);

        return $this->json([
            'count' => $data['count'],
            'next' => $data['next'],
            'previous' => $data['previous'],
            'results' => json_decode($json),
        ]);
    }

    #[Route(['', '/'], methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser();

        $plant = $serializer->deserialize($request->getContent(), Plant::class, 'json', ['groups' => 'plant:write']);
        $plant->setUser($user);

        $em->persist($plant);
        $em->flush();

        return $this->json($plant, 201, [], ['groups' => 'plant:read']);
    }

    #[Route(['/{id}', '/{id}/'], methods: ['GET'])]
    public function show(Plant $plant): JsonResponse
    {
        if ($plant->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Non autorisé'], 403);
        }

        return $this->json($plant, 200, [], ['groups' => 'plant:read']);
    }

    #[Route(['/{id}', '/{id}/'], methods: ['PUT'])]
    public function update(
        Request $request,
        Plant $plant,
        EntityManagerInterface $em,
        SerializerInterface $serializer
    ): JsonResponse {
        if ($plant->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Non autorisé'], 403);
        }

        $serializer->deserialize($request->getContent(), Plant::class, 'json', [
            'object_to_populate' => $plant,
            'groups' => 'plant:write',
        ]);

        $em->flush();

        return $this->json($plant, 200, [], ['groups' => 'plant:read']);
    }

    #[Route(['/{id}', '/{id}/'], methods: ['DELETE'])]
    public function delete(Plant $plant, EntityManagerInterface $em): JsonResponse
    {
        if ($plant->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Non autorisé'], 403);
        }

        $em->remove($plant);
        $em->flush();

        return new JsonResponse(null, 204);
    }
}
