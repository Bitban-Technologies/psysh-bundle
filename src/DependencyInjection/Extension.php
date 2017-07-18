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
        $this->registerShell($mergedConfig, $container);
        $this->registerCommand($container);
    }

    private function registerShell(array $config, ContainerBuilder $container): void
    {
        $configId = 'psysh.config';
        $container->setDefinition($configId, $this->configDefinition($config));

        $definition = (new Definition(Shell::class))
            ->setPublic(false)
            ->addArgument(new Reference($configId));

        if (isset($config['variables'])) {
            $definition->addMethodCall('setScopeVariables', [
                $this->scopeVariables($config['variables']),
            ]);
        }

        $container->setDefinition('psysh.shell', $definition);
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

    private function scopeVariables(array $variables): array
    {
        foreach ($variables as &$spec) {
            if (\is_string($spec) && '@' === $spec[0]) {
                $spec = new Reference(\substr($spec, 1));
            }
        }

        return $variables;
    }

    private function registerCommand(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(Command::class)
            ->addTag('psysh.command');

        $container->register('psysh.command.shell_command', ShellCommand::class)
            ->setPublic(false)
            ->addArgument(new Reference('psysh.shell'))
            ->setAutoconfigured(true);
    }
}
