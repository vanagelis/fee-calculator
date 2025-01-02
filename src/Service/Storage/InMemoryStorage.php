<?php

declare(strict_types=1);

namespace App\Service\Storage;

use App\Model\Input\Operation;

class InMemoryStorage implements StorageInterface
{
    private array $userOperations = [];

    public function add(Operation $operation): void
    {
        $this->userOperations[$operation->getUserId()][] = $operation;
    }

    public function get(int $userId): array
    {
        return $this->userOperations[$userId] ?? [];
    }
}
