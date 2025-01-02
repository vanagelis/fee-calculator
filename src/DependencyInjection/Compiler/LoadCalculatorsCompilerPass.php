<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Service\FeeCalculator\Context\CalculatorContext;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class LoadCalculatorsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $context = $container->findDefinition(CalculatorContext::class);
        $taggedServices = $container->findTaggedServiceIds('app.calculator');

        foreach ($taggedServices as $id => $service) {
            $context->addMethodCall('addProvider', [new Reference($id)]);
        }
    }
}
