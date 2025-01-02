<?php

declare(strict_types=1);

namespace App\Service\Storage;

use App\Model\Input\Operation;

interface StorageInterface
{
    public function add(Operation $operation): void;

    /**
     * @return array<Operation>
     */
    public function get(int $userId): array;
}
