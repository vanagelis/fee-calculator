<?php

declare(strict_types=1);

namespace App\Service\FeeCalculator\Provider;

use App\ValueObject\Money;

abstract class AbstractCalculatorProvider
{
    protected function calculatePercentage(
        float $amount,
        float $percent,
        string $currency = Money::DEFAULT_CURRENCY
    ): Money {
        return new Money(round($amount * $percent, 2), $currency);
    }
}
