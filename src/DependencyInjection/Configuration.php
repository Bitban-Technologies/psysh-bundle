<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\DependencyInjection;

use Symfony\Component\Config\Definition\{
    Builder\ArrayNodeDefinition,
    Builder\TreeBuilder,
    ConfigurationInterface,
    Exception\InvalidConfigurationException
};

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('psysh');

        $rootNode
            ->children()
                ->enumNode('color_mode')
                    ->values(['auto', 'forced', 'disabled'])
                ->end()
                ->scalarNode('config_dir')->end()
                ->append($this->addArrayNode('commands'))
                ->scalarNode('data_dir')->end()
                ->append($this->addArrayNode('default_includes'))
                ->booleanNode('erase_duplicates')->end()
                ->append($this->addErrorLoggingLevelNode())
                ->scalarNode('history_file')->end()
                ->integerNode('history_size')
                    ->info('If set to zero (0), the history size is unlimited')
                ->end()
                ->scalarNode('manual_db_file')->end()
                ->scalarNode('pager')->treatNullLike('less')->end()
                ->booleanNode('require_semicolons')->end()
                ->scalarNode('runtime_dir')
                    ->info('Set the shell\'s temporary directory location')
                ->end()
                ->scalarNode('startup_message')->end()
                ->booleanNode('tab_completion')->end()
                ->append($this->addArrayNode('tab_completion_matchers'))
                ->enumNode('update_check')->defaultValue('never')
                    ->values(['never', 'always', 'daily', 'weekly', 'monthly'])
                ->end()
                ->booleanNode('bracketed_paste')->end()
                ->booleanNode('pcntl')->end()
                ->booleanNode('readline')->end()
                ->append($this->addVariablesNode())
                ->booleanNode('unicode')->end()
            ->end()
            ->validate()
                ->always()
                ->then($this->normalizer())
            ->end();

        return $treeBuilder;
    }

    private function normalizer(): callable
    {
        return static function (array $config): array {
            static $keys = [
                'bracketed_paste' => 'useBracketedPaste',
                'pcntl'           => 'usePcntl',
                'readline'        => 'useReadline',
                'unicode'         => 'useUnicode',
            ];

            // config_dir -> configDir
            $camelize = static function (string $value): string {
                return \str_replace('_', '', \lcfirst(\ucwords(\strtolower($value), '_')));
            };

            $normalized = [];
            foreach ($config as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                $key = $keys[$key] ?? $camelize($key);
                $normalized[$key] = $value;
            }

            return $normalized;
        };
    }

    private function addVariablesNode(): ArrayNodeDefinition
    {
        $node = $this->addArrayNode('variables');
        $node
            ->validate()
                ->always()
                ->then(static function ($variables) {
                    return \array_filter($variables, 'is_string');
                })
            ->end()
        ;

        return $node;
    }

    private function addErrorLoggingLevelNode(): ArrayNodeDefinition
    {
        $node = $this->addArrayNode('error_logging_level');
        $node
            ->validate()
                ->always()
                ->then(static function ($methods) {
                    $invalidMethods = \array_filter($methods, static function ($method) {
                        return false === \defined("E_{$method}");
                    });

                    if (empty($invalidMethods)) {
                        return \array_reduce($methods, static function ($level, $method) {
                            return $level |= \constant("E_{$method}");
                        });
                    }

                    throw new InvalidConfigurationException(\sprintf(
                        'The errors are not supported: "%s".',
                        \implode('", "', $invalidMethods)
                    ));
                })
            ->end()
        ;

        return $node;
    }

    private function addArrayNode(string $name): ArrayNodeDefinition
    {
        $node = new ArrayNodeDefinition($name);
        $node
            ->normalizeKeys(false)
            ->useAttributeAsKey('name')
            ->beforeNormalization()
                ->ifString()
                ->then(static function ($v) {
                    return \preg_split('/\s*,\s*/', $v, -1, \PREG_SPLIT_NO_EMPTY);
                })
            ->end()
            ->prototype('scalar')->end()
        ;

        return $node;
    }
}
