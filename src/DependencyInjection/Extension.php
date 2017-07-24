<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\DependencyInjection;

use AlexMasterov\PsyshBundle\Command\ShellCommand;
use Psy\{
    Command\Command,
    Configuration,
    Matcher\AbstractMatcher,
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
     * @codeCoverageIgnore
     */
    public function getAlias()
    {
        return 'psysh';
    }

    /**
     * @inheritDoc
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $this->registerTags($container);
        $this->registerCommands($container);
        $this->registerConfig($mergedConfig, $container);
        $this->registerShell($mergedConfig, $container);
    }

    private function registerTags(ContainerBuilder $container): void
    {
        static $tags = [
            'psysh.command' => Command::class,
            'psysh.matcher' => AbstractMatcher::class,
        ];

        foreach ($tags as $tag => $spec) {
            $container->registerForAutoconfiguration($spec)->addTag($tag);
        }
    }

    private function registerCommands(ContainerBuilder $container): void
    {
        $container->register('psysh.command.shell_command', ShellCommand::class)
            ->setPublic(false)
            ->addArgument(new Reference('psysh.shell'))
            ->setAutoconfigured(true);
    }

    private function registerConfig(array $config, ContainerBuilder $container): void
    {
        $config = $this->registerConfigurationTags($config, $container);

        $definition = (new Definition(Configuration::class))
            ->setShared(false)
            ->setPublic(false)
            ->addArgument($config);

        if (isset($config['historyFile'])) {
            $definition->addMethodCall('setHistoryFile', [$config['historyFile']]);
        }

        $container->setDefinition('psysh.config', $definition);
    }

    private function registerConfigurationTags(array $config, ContainerBuilder $container): array
    {
        $configurator = function (array $services, ContainerBuilder $container): void {
            foreach ($services as $service) {
                $container->hasDefinition($service) ?: $container->register($service)->setPublic(false);
                $container->getDefinition($service)->setAutoconfigured(true);
            }
        };

        foreach (['commands', 'tabCompletionMatchers'] as $option) {
            if (isset($config[$option])) {
                $configurator($config[$option], $container);
                unset($config[$option]);
            }
        }

        return $config;
    }

    private function registerShell(array $config, ContainerBuilder $container): void
    {
        $definition = (new Definition(Shell::class))
            ->setPublic(false)
            ->addArgument(new Reference('psysh.config'));

        if (isset($config['variables'])) {
            $definition->addMethodCall('setScopeVariables', [
                $this->scopeVariables($config['variables']),
            ]);
        }

        $container->setDefinition('psysh.shell', $definition);
    }

    private function scopeVariables(array $variables): array
    {
        foreach ($variables as &$spec) {
            if (\is_string($spec) && 0 === \strpos($spec, '@')) {
                $spec = new Reference(\substr($spec, 1));
            }
        }

        return $variables;
    }
}
