<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\{
    Compiler\CompilerPassInterface,
    ContainerBuilder,
    Reference
};

class AddCommandPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->has('psysh.shell')) {
            return;
        }

        $services = $container->findTaggedServiceIds('psysh.command', true);
        if (empty($services)) {
            return;
        }

        $commands = \array_map(
            static function ($id) { return new Reference($id); },
            \array_keys($services)
        );

        $container->getDefinition('psysh.config')
            ->addMethodCall('addCommands', [$commands]);
    }
}
