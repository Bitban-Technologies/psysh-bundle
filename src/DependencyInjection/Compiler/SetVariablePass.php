<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\{
    Compiler\CompilerPassInterface,
    ContainerBuilder,
    Definition,
    Reference
};

class SetVariablePass implements CompilerPassInterface
{
    /** @const */
    const METHOD = 'setScopeVariables';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('psysh.shell')) {
            return;
        }

        $variables = [];

        foreach ($container->findTaggedServiceIds('psysh.variable', true) as $id => [$attributes]) {
            $variable = $attributes['var'] ?? (\class_exists($id) ? $this->classify($id) : $id);
            $variables[$variable] = new Reference($id);
        }

        if (empty($variables)) {
            return;
        }

        $definition = $container->getDefinition('psysh.shell');

        if ($definition->hasMethodCall(self::METHOD)) {
            $this->mergeMethodCall($definition, $variables);

            return;
        }

        $definition->addMethodCall(self::METHOD, [$variables]);
    }

    // NameSpace\SomeName -> nameSpaceSomeName
    private function classify(string $spec): string
    {
        $parts = \explode('\\', $spec);

        if (!empty($parts[1])) {
            $parts[0] = \strtolower($parts[0]);
        }

        return \implode($parts);
    }

    private function mergeMethodCall(Definition $definition, array $variables): void
    {
        $calls = $definition->getMethodCalls();

        foreach ($calls as $call => [$method, $arguments]) {
            if (self::METHOD === $method) {
                foreach ($arguments as $argument) {
                    $variables += $argument;
                }
                unset($calls[$call]);
            }
        }

        $calls += [
            [self::METHOD, [$variables]],
        ];

        $definition->setMethodCalls($calls);
    }
}
