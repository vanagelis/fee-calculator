<?php

declare(strict_types=1);

namespace App\Service\Deserializer\Csv;

use App\Model\Input\Operation;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CsvDeserializer
{
    public function deserialize(string $filePath): array
    {
        $encoders = [new CsvEncoder()];
        $normalizers = [
            new ArrayDenormalizer(),
            new DateTimeNormalizer(),
            new ObjectNormalizer(
                propertyTypeExtractor: new ReflectionExtractor()
            ),
        ];
        $serializer = new Serializer($normalizers, $encoders);

        $csvContent = 'date,userId,userType,operationType,operationAmount,currency' . PHP_EOL . file_get_contents($filePath);

        return $serializer->deserialize($csvContent, Operation::class.'[]','csv');
    }
}
