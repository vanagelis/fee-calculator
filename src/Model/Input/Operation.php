<?php

declare(strict_types=1);

namespace App\Model\Input;

class Operation
{
    public const OPERATION_TYPE_DEPOSIT = 'deposit';
    public const OPERATION_TYPE_WITHDRAW = 'withdraw';

    public const USER_TYPE_PRIVATE = 'private';
    public const USER_TYPE_BUSINESS = 'business';

    private ?\DateTime $date = null;
    private ?int $userId = null;
    private ?string $userType = null;
    private ?string $operationType = null;
    private ?float $operationAmount = null;
    private ?string $currency = null;

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUserType(): ?string
    {
        return $this->userType;
    }

    public function setUserType(string $userType): self
    {
        $this->userType = $userType;

        return $this;
    }

    public function getOperationType(): ?string
    {
        return $this->operationType;
    }

    public function setOperationType(string $operationType): self
    {
        $this->operationType = $operationType;

        return $this;
    }

    public function getOperationAmount(): ?float
    {
        return $this->operationAmount;
    }

    public function setOperationAmount(float $operationAmount): self
    {
        $this->operationAmount = $operationAmount;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }
}
