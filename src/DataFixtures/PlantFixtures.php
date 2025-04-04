<?php

// src/DataFixtures/PlantFixtures.php

namespace App\DataFixtures;

use App\Entity\Plant;
use App\Entity\PlantPhoto;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PlantFixtures extends Fixture
{
    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('antoine.greuzard')
            ->setEmail('antoine@example.com')
            ->setRoles(['ROLE_USER']);

        $hashedPassword = $this->hasher->hashPassword($user, 'secret123');
        $user->setPassword($hashedPassword);

        $types = Plant::TYPE_CHOICES;
        $sunlights = Plant::SUNLIGHT_CHOICES;
        $humidities = Plant::HUMIDITY_CHOICES;

        for ($i = 1; $i <= 20; $i++) {
            $plant = new Plant();
            $plant->setUser($user)
                ->setName("Plante $i")
                ->setVariety("Variété $i")
                ->setPlantType($types[array_rand($types)])
                ->setPurchaseDate(new DateTimeImmutable('-'.rand(0, 365).' days'))
                ->setLocation("Emplacement $i")
                ->setDescription("Ceci est une description pour la plante $i.")
                ->setWateringFrequency(rand(3, 10))
                ->setFertilizingFrequency(rand(20, 60))
                ->setRepottingFrequency(rand(180, 400))
                ->setPruningFrequency(rand(60, 180))
                ->setLastWatering(new DateTimeImmutable('-'.rand(1, 7).' days'))
                ->setLastFertilizing(new DateTimeImmutable('-'.rand(1, 30).' days'))
                ->setLastRepotting(new DateTimeImmutable('-'.rand(1, 365).' days'))
                ->setLastPruning(new DateTimeImmutable('-'.rand(1, 90).' days'))
                ->setSunlightLevel($sunlights[array_rand($sunlights)])
                ->setHumidityLevel($humidities[array_rand($humidities)])
                ->setTemperature(rand(15, 28));

            // Ajoute 1 photo fictive
            $photo = new PlantPhoto();
            $photo->setPlant($plant)
                ->setCaption("Photo de la plante $i");
            $photo->setUploadedAtManually(new DateTimeImmutable());

            // Copie une image dummy (à créer si besoin)
            $filesystem = new Filesystem();
            $targetDir = $this->kernel->getProjectDir().'/public/storage/plant_photos';
            $filesystem->mkdir($targetDir);

            $dummyImage = __DIR__.'/dummy.jpg';
            $destImage = $targetDir."/plant_$i.jpg";

            if (!$filesystem->exists($destImage)) {
                $filesystem->copy($dummyImage, $destImage);
            }

            $photo->setImage("plant_$i.jpg");
            $plant->addPhoto($photo);

            $manager->persist($plant);
            $manager->persist($photo);
        }

        $manager->flush();
    }
}
