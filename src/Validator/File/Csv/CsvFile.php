<?php

declare(strict_types=1);

namespace App\Validator\File\Csv;

use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\FileValidator;

class CsvFile extends File
{
    public function __construct()
    {
        parent::__construct(
            mimeTypes: ['csv' => 'text/csv']
        );
    }

    public function validatedBy(): string
    {
        return FileValidator::class;
    }
}
