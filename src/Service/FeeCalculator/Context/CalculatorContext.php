<?php

declare(strict_types=1);

namespace App\Service\FeeCalculator\Context;

use App\Model\Input\Operation;
use App\Service\FeeCalculator\Exception\InvalidOperationTypeException;
use App\Service\FeeCalculator\Provider\CalculatorProviderInterface;
use App\ValueObject\Money;

class CalculatorContext
{
    /**
     * @var array<CalculatorProviderInterface>
     */
    private array $providers = [];

    /**
     * @uses \App\DependencyInjection\Compiler\LoadCalculatorsCompilerPass
     */
    public function addProvider(CalculatorProviderInterface $calculator): void
    {
        $this->providers[] = $calculator;
    }

    /**
     * @throws InvalidOperationTypeException
     */
    public function handleCalculate(Operation $operation): Money
    {
        foreach ($this->providers as $provider) {
            if ($provider->shouldUseCalculator($operation)) {
                return $provider->calculate($operation);
            }
        }

        throw new InvalidOperationTypeException();
    }
}
