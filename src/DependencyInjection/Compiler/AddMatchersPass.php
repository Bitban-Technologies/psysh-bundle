<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\{
    Compiler\CompilerPassInterface,
    ContainerBuilder,
    Reference
};

class AddMatchersPass implements CompilerPassInterface
{
    /** {@inheritdoc} */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('psysh.shell')) {
            return;
        }

        $matchers = [];

        foreach ($container->findTaggedServiceIds('psysh.matcher', true) as $id => $tags) {
            $matchers[] = new Reference($id);
        }

        if (empty($matchers)) {
            return;
        }

        $container->getDefinition('psysh.config')
            ->addMethodCall('addMatchers', [$matchers]);
    }
}
