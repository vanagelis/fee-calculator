<?php

declare(strict_types=1);

namespace App\Service\FeeCalculator;

use App\Model\Input\Operation;
use App\Service\FeeCalculator\Context\CalculatorContext;
use App\Service\FeeCalculator\Exception\InvalidOperationTypeException;
use App\Service\FeeCalculator\Exception\InvalidUserTypeException;
use App\ValueObject\Money;

class FeeCalculator
{
    public function __construct(private readonly CalculatorContext $calculatorContext)
    {
    }

    /**
     * @throws InvalidOperationTypeException
     * @throws InvalidUserTypeException
     */
    public function calculate(Operation $operation): Money
    {
        return $this->calculatorContext->handleCalculate($operation);
    }
}
