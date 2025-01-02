<?php

declare(strict_types=1);

namespace App\Service\FeeCalculator\Exception;

class InvalidOperationTypeException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Invalid operation type');
    }
}
