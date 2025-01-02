<?php

declare(strict_types=1);

namespace App\Tests\Integration\Command;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CalculateCommissionCommandTest extends KernelTestCase
{
    public function testCommand(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('commission:calculate');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['file' => './tests/data/test.csv']);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertSame("0.60\n3.60\n6.60\n0.06\n1.50\n87.60\n90.60\n90.90\n91.20\n3.00\n0.00\n0.00\n8,997.00\n", $output);
    }
}
