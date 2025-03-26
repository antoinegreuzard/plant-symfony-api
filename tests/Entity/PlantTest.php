<?php

namespace Entity;

use App\Entity\Plant;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PlantTest extends TestCase
{
    public function testPlantNameAccessors(): void
    {
        $plant = new Plant();
        $plant->setName('Aloe Vera');

        $this->assertSame('Aloe Vera', $plant->getName());
    }

    public function testNextWateringCalculation(): void
    {
        $plant = new Plant();
        $lastWatering = new DateTimeImmutable('2025-03-01');
        $plant->setLastWatering($lastWatering);
        $plant->setWateringFrequency(7);

        $expected = $lastWatering->add(new DateInterval('P7D'));

        $this->assertEquals($expected, $plant->getNextWatering());
    }

    public function testNextFertilizingCalculation(): void
    {
        $plant = new Plant();
        $lastFertilizing = new DateTimeImmutable('2025-03-01');
        $plant->setLastFertilizing($lastFertilizing);
        $plant->setFertilizingFrequency(30);

        $expected = $lastFertilizing->add(new DateInterval('P30D'));

        $this->assertEquals($expected, $plant->getNextFertilizing());
    }

    public function testNextRepottingCalculation(): void
    {
        $plant = new Plant();
        $lastRepotting = new DateTimeImmutable('2025-03-01');
        $plant->setLastRepotting($lastRepotting);
        $plant->setRepottingFrequency(365);

        $expected = $lastRepotting->add(new DateInterval('P365D'));

        $this->assertEquals($expected, $plant->getNextRepotting());
    }

    public function testNextPruningCalculation(): void
    {
        $plant = new Plant();
        $lastPruning = new DateTimeImmutable('2025-03-01');
        $plant->setLastPruning($lastPruning);
        $plant->setPruningFrequency(90);

        $expected = $lastPruning->add(new DateInterval('P90D'));

        $this->assertEquals($expected, $plant->getNextPruning());
    }

    public function testSunlightLevelDefault(): void
    {
        $plant = new Plant();

        $this->assertSame('medium', $plant->getSunlightLevel());
    }

    public function testHumidityLevelDefault(): void
    {
        $plant = new Plant();

        $this->assertSame('medium', $plant->getHumidityLevel());
    }
}
