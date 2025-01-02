<?php

declare(strict_types=1);

namespace App\Service\FeeCalculator\Provider;

use App\Model\Input\Operation;
use App\ValueObject\Money;

interface CalculatorProviderInterface
{
    public function shouldUseCalculator(Operation $operation): bool;

    public function calculate(Operation $operation): Money;
}
