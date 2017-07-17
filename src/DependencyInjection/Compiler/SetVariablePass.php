<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\{
    Compiler\CompilerPassInterface,
    ContainerBuilder,
    Reference
};

class SetVariablePass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->has('psysh.shell')) {
            return;
        }

        $services = $container->findTaggedServiceIds('psysh.variable', true);
        if (empty($services)) {
            return;
        }

        $this->registerScopeVariables($services, $container);
    }

    private function registerScopeVariables(array $services, ContainerBuilder $container): void
    {
        $definition = $container->getDefinition('psysh.shell');

        if ($definition->hasMethodCall('setScopeVariables')) {
            $calls = $this->mergeScopeVariables(
                $definition->getMethodCalls(),
                $this->scopeVariables($services)
            );
            $definition->setMethodCalls($calls);
        } else {
            $definition->addMethodCall('setScopeVariables', [$this->scopeVariables($services)]);
        }
    }

    private function mergeScopeVariables(array $calls, array $variables): array
    {
        foreach ($calls as $i => [$method, $args]) {
            if ('setScopeVariables' === $method) {
                foreach ($args as $arg) {
                    $variables += $arg;
                }
                unset($calls[$i]);
            }
        }

        return $calls + [
            ['setScopeVariables', [$variables]],
        ];
    }

    private function scopeVariables(array $services): array
    {
        // NameSpace\SomeName -> nameSpaceSomename
        $classify = static function (string $spec): string {
            return \str_replace('\\', '', \lcfirst(\ucwords(\strtolower($spec), '\\')));
        };

        $scopeVariables = [];
        foreach ($services as $id => $attributes) {
            $variable = $attributes[0]['name']
                ?? (\class_exists($id) ? $classify($id) : $id);
            $scopeVariables[$variable] = new Reference($id);
        }

        return $scopeVariables;
    }
}
