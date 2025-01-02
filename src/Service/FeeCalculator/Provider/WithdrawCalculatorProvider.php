<?php

declare(strict_types=1);

namespace App\Service\FeeCalculator\Provider;

use App\Model\Input\Operation;
use App\Service\Exchanger\CurrencyExchanger;
use App\Service\FeeCalculator\Exception\InvalidUserTypeException;
use App\Service\Repository\UserOperationsRepository;
use App\ValueObject\Money;

class WithdrawCalculatorProvider extends AbstractCalculatorProvider implements CalculatorProviderInterface
{
    public function __construct(
        private readonly UserOperationsRepository $userOperationsRepository,
        private readonly CurrencyExchanger $currencyExchanger,
        private readonly int $allowedOperationsCountPerWeek,
        private readonly int $allowedOperationsTotalPerWeek,
        private readonly float $withdrawPrivatePercent,
        private readonly float $withdrawBusinessPercent,
    ) {
    }

    public function shouldUseCalculator(Operation $operation): bool
    {
        return $operation->getOperationType() === Operation::OPERATION_TYPE_WITHDRAW;
    }

    /**
     * @throws InvalidUserTypeException
     */
    public function calculate(Operation $operation): Money
    {
        return match ($operation->getUserType()) {
            Operation::USER_TYPE_PRIVATE => $this->getFeeForPrivateClient($operation),
            Operation::USER_TYPE_BUSINESS => $this->getFeeForBusinessClient($operation),
            default => throw new InvalidUserTypeException(),
        };
    }

    private function getFeeForPrivateClient(Operation $operation): Money
    {
        $userOperationsInWeek = $this->userOperationsRepository->findUserOperationsInWeek(
            $operation->getUserId(),
            $operation->getDate()
        );

        $userOperationsCountInWeek = count($userOperationsInWeek);
        $userOperationsTotalPerWeek = array_sum(array_map(
            fn (Operation $operation) => $this->currencyExchanger->exchange(
                $operation->getOperationAmount(),
                $operation->getCurrency()
            ),
            $userOperationsInWeek
        ));

        if (
            $userOperationsCountInWeek <= $this->allowedOperationsCountPerWeek
            && $userOperationsTotalPerWeek < $this->allowedOperationsTotalPerWeek
        ) {
            return new Money(0, $operation->getCurrency());
        }

        return $this->calculatePercentage(
            max($userOperationsTotalPerWeek - $this->allowedOperationsTotalPerWeek, 0),
            $this->withdrawPrivatePercent
        );
    }

    private function getFeeForBusinessClient(Operation $operation): Money
    {
        return $this->calculatePercentage(
            $operation->getOperationAmount(),
            $this->withdrawBusinessPercent,
            $operation->getCurrency()
        );
    }
}
