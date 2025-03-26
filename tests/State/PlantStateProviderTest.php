<?php

namespace State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Plant;
use App\Service\PlantAdviceService;
use App\State\PlantStateProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class PlantStateProviderTest extends TestCase
{
    public function testItDelegatesIfNotPlant(): void
    {
        $decorated = $this->createMock(ProviderInterface::class);
        $adviceService = $this->createMock(PlantAdviceService::class);

        $dummyObject = new stdClass();

        $decorated->method('provide')->willReturn($dummyObject);

        $provider = new PlantStateProvider($decorated, $adviceService);
        $result = $provider->provide($this->createMock(Operation::class), [], []);

        $this->assertSame($dummyObject, $result);
    }

    public function testItAddsAdviceToPlant(): void
    {
        $plant = new Plant();
        $expectedAdvice = ['Boire de l’eau', 'Éviter la lumière directe'];

        $decorated = $this->createMock(ProviderInterface::class);
        $decorated->method('provide')->willReturn($plant);

        $adviceService = $this->createMock(PlantAdviceService::class);
        $adviceService->method('getPersonalizedAdvice')->with($plant)->willReturn($expectedAdvice);

        $provider = new PlantStateProvider($decorated, $adviceService);
        $result = $provider->provide($this->createMock(Operation::class), [], []);

        $this->assertInstanceOf(Plant::class, $result);
        $this->assertSame($expectedAdvice, $result->advice);
    }
}
