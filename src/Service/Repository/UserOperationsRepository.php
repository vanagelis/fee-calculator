<?php

declare(strict_types=1);

namespace App\Service\Repository;

use App\Model\Input\Operation;
use App\Service\Storage\InMemoryStorage;

class UserOperationsRepository
{
    public function __construct(private readonly InMemoryStorage $storage)
    {
    }

    /**
     * @return array<Operation>
     */
    public function findUserOperationsInWeek(int $userId, \DateTime $date): array
    {
        return array_filter(
            $this->storage->get($userId),
            fn (Operation $operation) => $operation->getDate()->format('W') === $date->format('W')
        );
    }
}
