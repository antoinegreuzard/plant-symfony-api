<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PlantPhotoRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ApiResource(
    normalizationContext: ['groups' => ['photo:read']],
    denormalizationContext: ['groups' => ['photo:write']]
)]
#[ORM\Entity(repositoryClass: PlantPhotoRepository::class)]
#[ORM\Table(name: 'plant_photo')]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class PlantPhoto
{
    #[Groups(['photo:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Plant::class, inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Plant $plant = null;

    #[Groups(['photo:read'])]
    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[Groups(['photo:read', 'photo:write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $caption = null;

    #[Groups(['photo:read'])]
    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $uploadedAt = null {
        get {
            return $this->uploadedAt;
        }
        set {
            $this->uploadedAt = new DateTimeImmutable();
        }
    }

    #[Vich\UploadableField(mapping: 'plant_photos', fileNameProperty: 'image')]
    private ?File $imageFile = null;

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function __toString(): string
    {
        return sprintf(
            "Photo de %s - %s",
            $this->plant?->getName() ?? 'plante inconnue',
            $this->uploadedAt?->format('Y-m-d') ?? 'inconnue'
        );
    }

    #[Groups(['photo:read'])]
    public function getPlantId(): ?int
    {
        return $this->plant?->getId();
    }

    #[Groups(['plant:read'])]
    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(['photo:read'])]
    #[SerializedName('image')]
    public function getImageUrl(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();
        $baseUrl = $request ? $request->getSchemeAndHttpHost() : '';

        return $this->image ? $baseUrl.'/storage/plant_photos/'.$this->image : null;
    }

    public function getPlant(): ?Plant
    {
        return $this->plant;
    }

    public function setPlant(?Plant $plant): static
    {
        $this->plant = $plant;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(?string $caption): static
    {
        $this->caption = $caption;

        return $this;
    }

    public function setUploadedAtManually(?DateTimeInterface $uploadedAt): static
    {
        $this->uploadedAt = $uploadedAt;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): static
    {
        $this->imageFile = $imageFile;

        if ($imageFile !== null) {
            // 🟡 Mise à jour de la date si on change le fichier
            $this->uploadedAt = new DateTimeImmutable();
        }

        return $this;
    }
}
