<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\DependencyInjection;

use AlexMasterov\PsyshBundle\Command\ShellCommand;
use Psy\{
    Command\Command,
    Configuration,
    Shell
};
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Definition,
    Reference
};
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class Extension extends ConfigurableExtension
{
    /**
     * @inheritDoc
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $configId = 'psysh.config';
        $container->setDefinition($configId, $this->configDefinition($mergedConfig));

        $definition = (new Definition(Shell::class))
            ->setShared(false)
            ->addArgument(new Reference($configId));

        $container->setDefinition('psysh.shell', $definition);

        $container->register('psysh.command.shell_command', ShellCommand::class)
            ->setPublic(false)
            ->addArgument(new Reference('psysh.shell'))
            ->setAutoconfigured(true);

        $container->registerForAutoconfiguration(Command::class)
            ->addTag('psysh.command');
    }

    private function configDefinition(array $config): Definition
    {
        $definition = (new Definition(Configuration::class))
            ->setShared(false)
            ->setPublic(false)
            ->addArgument($config);

        if (isset($config['historyFile'])) {
            $definition->addMethodCall('setHistoryFile', [$config['historyFile']]);
        }

        return $definition;
    }
}
