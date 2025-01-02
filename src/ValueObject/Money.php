<?php

declare(strict_types=1);

namespace App\ValueObject;

class Money
{
    public const CURRENCY_EUR = 'EUR';
    public const DEFAULT_CURRENCY = self::CURRENCY_EUR;

    public function __construct(
        private readonly float $amount,
        private readonly string $currency = self::DEFAULT_CURRENCY,
    ) {
    }

    public function format(): string
    {
        return number_format($this->amount, 2);
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
