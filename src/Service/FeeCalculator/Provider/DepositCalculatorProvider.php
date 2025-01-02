<?php

declare(strict_types=1);

namespace App\Service\FeeCalculator\Provider;

use App\Model\Input\Operation;
use App\ValueObject\Money;

class DepositCalculatorProvider extends AbstractCalculatorProvider implements CalculatorProviderInterface
{
    public function __construct(
        private readonly float $depositPercent,
    ) {
    }

    public function shouldUseCalculator(Operation $operation): bool
    {
        return $operation->getOperationType() === Operation::OPERATION_TYPE_DEPOSIT;
    }

    public function calculate(Operation $operation): Money
    {
        return $this->calculatePercentage(
            $operation->getOperationAmount(),
            $this->depositPercent,
            $operation->getCurrency()
        );
    }
}
