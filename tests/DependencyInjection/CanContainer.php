<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait CanContainer
{
    private function container(): ContainerBuilder
    {
        return new ContainerBuilder();
    }

    private function hasDefinitionMethodCall(
        string $definition,
        string $method,
        ContainerBuilder $container
    ): bool {
        return $container->hasDefinition($definition)
            && $container->getDefinition($definition)->hasMethodCall($method);
    }

    private function getDefinitionMethodArguments(
        string $definition,
        string $method,
        ContainerBuilder $container
    ): array {
        $calls = $container->getDefinition($definition)->getMethodCalls();

        static $arguments = [];
        foreach ($calls as $i => [$name, $args]) {
            if ($name === $method) {
                foreach ($args as $arg) {
                    $arguments += $arg;
                }
            }
        }

        return $arguments;
    }
}
