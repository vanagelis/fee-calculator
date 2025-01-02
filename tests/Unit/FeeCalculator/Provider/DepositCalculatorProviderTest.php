<?php

declare(strict_types=1);

namespace App\Tests\Unit\FeeCalculator\Provider;

use App\Model\Input\Operation;
use App\Service\FeeCalculator\Provider\DepositCalculatorProvider;
use App\ValueObject\Money;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Service\FeeCalculator\Provider\DepositCalculatorProvider
 */
class DepositCalculatorProviderTest extends TestCase
{
    public function testShouldUseCalculator(): void
    {
        $depositCalculatorProvider = new DepositCalculatorProvider(3);

        $operation = new Operation();
        $operation->setOperationType(Operation::OPERATION_TYPE_DEPOSIT);
        $result = $depositCalculatorProvider->shouldUseCalculator($operation);
        $this->assertTrue($result);

        $operation->setOperationType(Operation::OPERATION_TYPE_WITHDRAW);
        $result = $depositCalculatorProvider->shouldUseCalculator($operation);
        $this->assertFalse($result);

        $operation->setOperationType('unknown');
        $result = $depositCalculatorProvider->shouldUseCalculator($operation);
        $this->assertFalse($result);
    }

    public function calculateDataProvider(): array
    {
        return [
            [$this->getOperation(), new Money(0.15, 'EUR')],
            [$this->getOperation()->setOperationAmount(1500), new Money(0.45, 'EUR')],
        ];
    }

    /**
     * @dataProvider calculateDataProvider
     */
    public function testCalculate(Operation $operation, Money $expected): void
    {
        $depositCalculatorProvider = new DepositCalculatorProvider(0.0003);
        $result = $depositCalculatorProvider->calculate($operation);

        $this->assertInstanceOf(Money::class, $result);
        $this->assertSame($expected->getAmount(), $result->getAmount());
        $this->assertSame($expected->getCurrency(), $result->getCurrency());
    }

    private function getOperation(): Operation
    {
        return (new Operation())
            ->setOperationAmount(500)
            ->setCurrency(Money::DEFAULT_CURRENCY)
        ;
    }
}
