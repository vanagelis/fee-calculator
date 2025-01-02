<?php

declare(strict_types=1);

namespace App\Tests\Unit\Exchanger;

use App\Service\Exchanger\CurrencyExchanger;
use App\Service\Exchanger\CurrencyExchangerClient;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \App\Service\Exchanger\CurrencyExchanger
 */
class CurrencyExchangerTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            [110, 'USD', 100],
            [15800, 'JPY', 100],
            [4000, 'UAH', 100],
            [30, 'CAD', 30],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testExchange(float $amount, string $currency, float $expectedResult): void
    {
        $testRates = new stdClass();
        $testRates->USD = 1.1;
        $testRates->JPY = 158;
        $testRates->UAH = 40;

        $exchangerClientMock = $this->createMock(CurrencyExchangerClient::class);
        $exchangerClientMock->expects(self::atLeastOnce())->method('getRates')->willReturn($testRates);

        $currencyExchanger = new CurrencyExchanger($exchangerClientMock);
        $result = $currencyExchanger->exchange($amount, $currency);

        $this->assertSame($expectedResult, $result);
    }
}
