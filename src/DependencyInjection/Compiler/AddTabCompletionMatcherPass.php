<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\{
    Compiler\CompilerPassInterface,
    ContainerBuilder,
    Reference
};

class AddTabCompletionMatcherPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->has('psysh.shell')) {
            return;
        }

        $services = $container->findTaggedServiceIds('psysh.matcher', true);
        if (empty($services)) {
            return;
        }

        $matchers = \array_map(
            static function ($id) { return new Reference($id); },
            \array_keys($services)
        );

        $container->getDefinition('psysh.config')
            ->addMethodCall('addTabCompletionMatchers', [$matchers]);
    }
}
