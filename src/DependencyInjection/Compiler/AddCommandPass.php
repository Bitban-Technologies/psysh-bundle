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
    /** {@inheritdoc} */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('psysh.shell')) {
            return;
        }

        $commands = [];

        foreach ($container->findTaggedServiceIds('psysh.command', true) as $id => $tags) {
            $commands[] = new Reference($id);
        }

        if (empty($commands)) {
            return;
        }

        $container->getDefinition('psysh.config')
            ->addMethodCall('addCommands', [$commands]);
    }
}
