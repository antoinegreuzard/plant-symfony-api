<?php

namespace App\Controller;

use App\Entity\Plant;
use App\Entity\PlantPhoto;
use App\Repository\PlantPhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/plants')]
final class PlantPhotoController extends AbstractController
{
    #[Route('/{id}/upload-photo', name: 'plant_upload_photo', methods: ['POST'])]
    public function upload(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        /** @var UploadedFile|null $file */
        $file = $request->files->get('image');
        $caption = $request->request->get('caption');

        if (!$file instanceof UploadedFile) {
            return new JsonResponse(['error' => 'Aucun fichier image envoyé.'], Response::HTTP_BAD_REQUEST);
        }

        /** @var Plant|null $plant */
        $plant = $em->getRepository(Plant::class)->find($id);
        if (!$plant) {
            throw new NotFoundHttpException("Plante introuvable.");
        }

        $photo = new PlantPhoto();
        $photo->setPlant($plant);
        $photo->setImageFile($file);
        $photo->setCaption($caption);

        $errors = $validator->validate($photo);
        if (count($errors) > 0) {
            return new JsonResponse((string)$errors, Response::HTTP_BAD_REQUEST);
        }

        $em->persist($photo);
        $em->flush();

        $json = $serializer->serialize($photo, 'json', ['groups' => 'photo:read']);

        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }

    #[Route('/{id}/photos', name: 'plant_photos_list', methods: ['GET'])]
    public function list(
        int $id,
        PlantPhotoRepository $photoRepo,
        SerializerInterface $serializer
    ): JsonResponse {
        $photos = $photoRepo->findBy(['plant' => $id], ['uploadedAt' => 'DESC']);
        $json = $serializer->serialize($photos, 'json', ['groups' => 'photo:read']);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
}
