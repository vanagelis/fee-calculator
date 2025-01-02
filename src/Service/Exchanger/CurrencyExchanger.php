<?php

declare(strict_types=1);

namespace App\Service\Exchanger;

class CurrencyExchanger
{
    public function __construct(
        private readonly CurrencyExchangerClient $exchangerClient,
    ) {
    }

    public function exchange(float $amount, string $currency): float
    {
        $rates = $this->exchangerClient->getRates();

        if (!$rates || !property_exists($rates, $currency)) {
            return $amount;
        }

        return round($amount / $rates->$currency, 2);
    }
}
