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

        $container->getDefinition('psysh.shell')
            ->addMethodCall('setScopeVariables', [$this->scopeVariables($services)]);
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
