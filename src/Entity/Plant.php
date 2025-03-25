<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\PlantRepository;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ApiResource(
    normalizationContext: ['groups' => ['plant:read']],
    denormalizationContext: ['groups' => ['plant:write']],
    paginationEnabled: true,
    paginationItemsPerPage: 10
)]
#[ORM\Entity(repositoryClass: PlantRepository::class)]
#[ORM\Table(name: 'plant')]
#[ORM\HasLifecycleCallbacks]
class Plant
{
    public const array TYPE_CHOICES = ['indoor', 'outdoor', 'succulent', 'flower', 'tree'];
    public const array SUNLIGHT_CHOICES = ['low', 'medium', 'high'];
    public const array HUMIDITY_CHOICES = ['low', 'medium', 'high'];
    #[Groups(['plant:read'])]
    public array $advice = [];
    #[Groups(['plant:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[Assert\Length(min: 3, minMessage: "Le nom doit contenir au moins 3 caractères.")]
    #[Groups(['plant:read', 'plant:write'])]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $variety = null;
    #[Groups(['plant:read', 'plant:write'])]
    #[ORM\Column(length: 20)]
    private ?string $plantType = null;
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTimeInterface $purchaseDate = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;
    #[Groups(['plant:read'])]
    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $createdAt = null;
    #[ORM\Column(type: 'integer')]
    private int $wateringFrequency = 7;
    #[ORM\Column(type: 'integer')]
    private int $fertilizingFrequency = 30;
    #[ORM\Column(type: 'integer')]
    private int $repottingFrequency = 365;
    #[ORM\Column(type: 'integer')]
    private int $pruningFrequency = 90;
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTimeInterface $lastWatering = null;
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTimeInterface $lastFertilizing = null;
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTimeInterface $lastRepotting = null;
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTimeInterface $lastPruning = null;
    #[ORM\Column(length: 10)]
    private string $sunlightLevel = 'medium';
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $temperature = null;
    #[ORM\Column(length: 10)]
    private string $humidityLevel = 'medium';
    #[ORM\OneToMany(targetEntity: PlantPhoto::class, mappedBy: 'plant', cascade: ['persist'], orphanRemoval: true)]
    private Collection $photos {
        get {
            return $this->photos;
        }
    }

    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    #[Groups(['plant:read'])]
    #[ApiProperty(readable: true)]
    public function getNextWatering(): ?DateTimeInterface
    {
        return $this->lastWatering?->add(new DateInterval("P{$this->wateringFrequency}D"));
    }

    #[Groups(['plant:read'])]
    public function getNextFertilizing(): ?DateTimeInterface
    {
        return $this->lastFertilizing?->add(new DateInterval("P{$this->fertilizingFrequency}D"));
    }

    #[Groups(['plant:read'])]
    public function getNextRepotting(): ?DateTimeInterface
    {
        return $this->lastRepotting?->add(new DateInterval("P{$this->repottingFrequency}D"));
    }

    #[Groups(['plant:read'])]
    public function getNextPruning(): ?DateTimeInterface
    {
        return $this->lastPruning?->add(new DateInterval("P{$this->pruningFrequency}D"));
    }

    #[Groups(['plant:read'])]
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getVariety(): ?string
    {
        return $this->variety;
    }

    public function setVariety(?string $variety): static
    {
        $this->variety = $variety;

        return $this;
    }

    public function getPlantType(): ?string
    {
        return $this->plantType;
    }

    public function setPlantType(string $plantType): static
    {
        $this->plantType = $plantType;

        return $this;
    }

    public function getPurchaseDate(): ?DateTimeInterface
    {
        return $this->purchaseDate;
    }

    public function setPurchaseDate(?DateTimeInterface $purchaseDate): static
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getWateringFrequency(): int
    {
        return $this->wateringFrequency;
    }

    public function setWateringFrequency(int $wateringFrequency): static
    {
        $this->wateringFrequency = $wateringFrequency;

        return $this;
    }

    public function getFertilizingFrequency(): int
    {
        return $this->fertilizingFrequency;
    }

    public function setFertilizingFrequency(int $fertilizingFrequency): static
    {
        $this->fertilizingFrequency = $fertilizingFrequency;

        return $this;
    }

    public function getRepottingFrequency(): int
    {
        return $this->repottingFrequency;
    }

    public function setRepottingFrequency(int $repottingFrequency): static
    {
        $this->repottingFrequency = $repottingFrequency;

        return $this;
    }

    public function getPruningFrequency(): int
    {
        return $this->pruningFrequency;
    }

    public function setPruningFrequency(int $pruningFrequency): static
    {
        $this->pruningFrequency = $pruningFrequency;

        return $this;
    }

    public function getLastWatering(): ?DateTimeInterface
    {
        return $this->lastWatering;
    }

    public function setLastWatering(?DateTimeInterface $lastWatering): static
    {
        $this->lastWatering = $lastWatering;

        return $this;
    }

    public function getLastFertilizing(): ?DateTimeInterface
    {
        return $this->lastFertilizing;
    }

    public function setLastFertilizing(?DateTimeInterface $lastFertilizing): static
    {
        $this->lastFertilizing = $lastFertilizing;

        return $this;
    }

    public function getLastRepotting(): ?DateTimeInterface
    {
        return $this->lastRepotting;
    }

    public function setLastRepotting(?DateTimeInterface $lastRepotting): static
    {
        $this->lastRepotting = $lastRepotting;

        return $this;
    }

    public function getLastPruning(): ?DateTimeInterface
    {
        return $this->lastPruning;
    }

    public function setLastPruning(?DateTimeInterface $lastPruning): static
    {
        $this->lastPruning = $lastPruning;

        return $this;
    }

    public function getSunlightLevel(): string
    {
        return $this->sunlightLevel;
    }

    public function setSunlightLevel(string $sunlightLevel): static
    {
        $this->sunlightLevel = $sunlightLevel;

        return $this;
    }

    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function setTemperature(?float $temperature): static
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getHumidityLevel(): string
    {
        return $this->humidityLevel;
    }

    public function setHumidityLevel(string $humidityLevel): static
    {
        $this->humidityLevel = $humidityLevel;

        return $this;
    }

    public function addPhoto(PlantPhoto $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setPlant($this);
        }

        return $this;
    }

    public function removePhoto(PlantPhoto $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            if ($photo->getPlant() === $this) {
                $photo->setPlant(null);
            }
        }

        return $this;
    }
}
