<?php

namespace App\Controller\Api;

use App\Entity\Plant;
use App\Entity\PlantPhoto;
use App\Repository\PlantPhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/plants')]
class PlantPhotoController extends AbstractController
{
    #[Route('/{id}/photos/', name: 'api_plant_photos_list', methods: ['GET'])]
    public function listPhotosForPlant(
        Plant $plant,
        PlantPhotoRepository $photoRepo,
        SerializerInterface $serializer
    ): JsonResponse {
        $photos = $photoRepo->findBy(['plant' => $plant], ['uploadedAt' => 'DESC']);
        $json = $serializer->serialize($photos, 'json', ['groups' => 'photo:read']);

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/{id}/upload-photo/', name: 'api_plant_photo_upload', methods: ['POST'])]
    public function uploadPhotoForPlant(
        Plant $plant,
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse {
        /** @var UploadedFile|null $imageFile */
        $imageFile = $request->files->get('imageFile');
        $caption = $request->request->get('caption');

        if (!$imageFile) {
            return $this->json(['error' => 'imageFile is required'], 400);
        }

        $photo = new PlantPhoto();
        $photo->setPlant($plant);
        $photo->setCaption($caption);
        $photo->setImageFile($imageFile);

        $errors = $validator->validate($photo);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $em->persist($photo);
        $em->flush();

        return $this->json($photo, 201, [], ['groups' => 'photo:read']);
    }
}
