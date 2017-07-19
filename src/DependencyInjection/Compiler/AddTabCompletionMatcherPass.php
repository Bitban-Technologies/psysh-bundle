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

        $container->getDefinition('psysh.config')
            ->addMethodCall('addTabCompletionMatchers', [$this->matchers($services)]);
    }

    private function matchers(array $services): array
    {
        $matchers = [];
        foreach (\array_keys($services) as $id) {
            $matchers[] = new Reference($id);
        }

        return $matchers;
    }
}
