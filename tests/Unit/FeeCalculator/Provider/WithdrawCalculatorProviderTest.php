<?php

declare(strict_types=1);

namespace App\Tests\Unit\FeeCalculator\Provider;

use App\Model\Input\Operation;
use App\Service\Exchanger\CurrencyExchanger;
use App\Service\FeeCalculator\Provider\WithdrawCalculatorProvider;
use App\Service\Repository\UserOperationsRepository;
use App\ValueObject\Money;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Service\FeeCalculator\Provider\WithdrawCalculatorProvider
 */
class WithdrawCalculatorProviderTest extends TestCase
{
    public function testShouldUseCalculator(): void
    {
        $withdrawCalculatorProvider = $this->getWithdrawCalculatorProvider();

        $operation = new Operation();
        $operation->setOperationType(Operation::OPERATION_TYPE_DEPOSIT);
        $result = $withdrawCalculatorProvider->shouldUseCalculator($operation);
        $this->assertFalse($result);

        $operation->setOperationType(Operation::OPERATION_TYPE_WITHDRAW);
        $result = $withdrawCalculatorProvider->shouldUseCalculator($operation);
        $this->assertTrue($result);

        $operation->setOperationType('unknown');
        $result = $withdrawCalculatorProvider->shouldUseCalculator($operation);
        $this->assertFalse($result);
    }

    public function privateClientsDataProvider(): array
    {
        $operation = $this->getOperation(Operation::USER_TYPE_PRIVATE);

        return [
            [
                $operation,
                [$operation],
                500,
                new Money(0.0, 'EUR'),
            ],
            [
                $operation,
                [$operation, $operation],
                500,
                new Money(0.0, 'EUR'),
            ],
            [
                $operation,
                [
                    $operation->setOperationAmount(200),
                    $operation->setOperationAmount(200),
                    $operation->setOperationAmount(200),
                ],
                200,
                new Money(0.0, 'EUR'),

            ],
            [
                $operation,
                [$operation, $operation, $operation],
                500,
                new Money(1.5, 'EUR'),
            ],
            [
                $operation,
                [$operation, $operation, $operation, $operation],
                500,
                new Money(3.0, 'EUR'),
            ],
        ];
    }

    public function businessClientsDataProvider(): array
    {
        return [
            [$this->getOperation(Operation::USER_TYPE_BUSINESS), new Money(2.5, 'EUR')],
            [
                $this->getOperation(Operation::USER_TYPE_BUSINESS)->setOperationAmount(10000),
                new Money(50.0, 'EUR'),
            ],
        ];
    }

    /**
     * @dataProvider privateClientsDataProvider
     */
    public function testCalculateForPrivateClient(Operation $operation, array $operations, float $exchangeItemValue, Money $expected): void
    {
        $userOperationsRepositoryMock = $this->createMock(UserOperationsRepository::class);
        $userOperationsRepositoryMock->expects(self::atLeastOnce())->method('findUserOperationsInWeek')->willReturn($operations);

        $currencyExchangerMock = $this->createMock(CurrencyExchanger::class);
        $currencyExchangerMock->expects(self::atLeastOnce())->method('exchange')->willReturn($exchangeItemValue);

        $withdrawCalculatorProvider = $this->getWithdrawCalculatorProvider($userOperationsRepositoryMock, $currencyExchangerMock);
        $result = $withdrawCalculatorProvider->calculate($operation);

        $this->assertInstanceOf(Money::class, $result);
        $this->assertSame($expected->getAmount(), $result->getAmount());
        $this->assertSame($expected->getCurrency(), $result->getCurrency());
    }

    /**
     * @dataProvider businessClientsDataProvider
     */
    public function testCalculateForBusinessClient(Operation $operation, Money $expected): void
    {
        $withdrawCalculatorProvider = $this->getWithdrawCalculatorProvider();
        $result = $withdrawCalculatorProvider->calculate($operation);

        $this->assertInstanceOf(Money::class, $result);
        $this->assertSame($expected->getAmount(), $result->getAmount());
        $this->assertSame($expected->getCurrency(), $result->getCurrency());
    }

    private function getOperation(string $operationType): Operation
    {
        return (new Operation())
            ->setUserType($operationType)
            ->setOperationAmount(500)
            ->setCurrency(Money::DEFAULT_CURRENCY)
            ->setUserId(1)
            ->setDate(new \DateTime('2023-10-19'))
        ;
    }

    private function getWithdrawCalculatorProvider(
        MockObject $userOperationsRepositoryMock = null,
        MockObject $currencyExchangerMock = null,
    ): WithdrawCalculatorProvider {
        if (!$userOperationsRepositoryMock) {
            $userOperationsRepositoryMock = $this->createMock(UserOperationsRepository::class);
        }

        if (!$currencyExchangerMock) {
            $currencyExchangerMock = $this->createMock(CurrencyExchanger::class);
        }

        return new WithdrawCalculatorProvider(
            $userOperationsRepositoryMock,
            $currencyExchangerMock,
            3,
            1000,
            0.003,
            0.005
        );
    }
}
