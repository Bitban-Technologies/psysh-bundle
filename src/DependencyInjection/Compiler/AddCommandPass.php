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
     * @inheritDoc
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

        $container->getDefinition('psysh.shell')
            ->addMethodCall('addCommands', [$this->commands($services)]);
    }

    private function commands(array $services): array
    {
        $commands = [];
        foreach (\array_keys($services) as $id) {
            $commands[] = new Reference($id);
        }

        return $commands;
    }
}
