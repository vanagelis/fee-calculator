<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\Input\Operation;
use App\Service\Deserializer\Csv\CsvDeserializer;
use App\Service\FeeCalculator\Exception\InvalidOperationTypeException;
use App\Service\FeeCalculator\Exception\InvalidUserTypeException;
use App\Service\FeeCalculator\FeeCalculator;
use App\Service\Storage\StorageInterface;
use App\Validator\File\Csv\CsvFile;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'commission:calculate',
    description: 'Calculate commission fee',
)]
class CalculateCommissionCommand extends Command
{
    private readonly OutputInterface $output;

    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly CsvDeserializer $csvDeserializer,
        private readonly FeeCalculator $feeCalculator,
        private readonly StorageInterface $storage,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $filePath = $input->getArgument('file');

        if ($this->validator->validate($filePath, new CsvFile())->count()) {
            throw new InvalidArgumentException('Invalid file');
        }

        array_map(
            fn (Operation $operation) => $this->calculate($operation),
            $this->csvDeserializer->deserialize($filePath),
        );

        return Command::SUCCESS;
    }

    private function calculate(Operation $operation): void
    {
        try {
            $this->storage->add($operation);
            $fee = $this->feeCalculator->calculate($operation);
            $this->writeSuccess($fee->format());
        } catch (InvalidOperationTypeException|InvalidUserTypeException $exception) {
            $this->writeError($exception->getMessage());
        } catch (Exception $exception) {
            $this->writeError('Something Went Wrong [' . $exception->getMessage() . ']');
        }
    }

    private function writeSuccess(string $message): void
    {
        $this->output->writeln($message);
    }

    private function writeError(string $message): void
    {
        $outputStyle = new OutputFormatterStyle('red', '#ff0', ['bold', 'blink']);
        $this->output->getFormatter()->setStyle('fire', $outputStyle);

        $this->output->writeln($message);
    }
}
