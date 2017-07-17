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
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('psysh');

        $rootNode
            ->children()
                ->append($this->addVariablesNode())
                ->append($this->addErrorLoggingLevelNode())
                ->scalarNode('config_dir')->end()
                ->scalarNode('data_dir')->end()
                ->scalarNode('runtime_dir')
                    ->info('Set the shell\'s temporary directory location')
                ->end()
                ->integerNode('history_size')
                    ->info('If set to zero (0), the history size is unlimited')
                ->end()
                ->scalarNode('history_file')->end()
                ->scalarNode('manual_db_file')->end()
                ->booleanNode('tab_completion')->end()
                ->scalarNode('startup_message')->end()
                ->booleanNode('require_semicolons')->end()
                ->booleanNode('erase_duplicates')->end()
                ->booleanNode('pcntl')->end()
                ->booleanNode('readline')->end()
                ->booleanNode('unicode')->end()
                ->enumNode('color_mode')
                    ->values(['auto', 'forced', 'disabled'])
                ->end()
                ->scalarNode('pager')->treatNullLike('less')->end()
                ->enumNode('update_check')->defaultValue('never')
                    ->values(['never', 'always', 'daily', 'weekly', 'monthly'])
                ->end()
            ->end()
        ;

        $this->normalizeRootNode($rootNode);

        return $treeBuilder;
    }

    private function addVariablesNode(): ArrayNodeDefinition
    {
        $node = new ArrayNodeDefinition('variables');
        $node
            ->normalizeKeys(false)
            ->useAttributeAsKey('name')
            ->prototype('variable')->end()
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
        $node = new ArrayNodeDefinition('error_logging_level');
        $node
            ->beforeNormalization()
                ->ifString()
                ->then(static function ($v) {
                    return \preg_split('/\s*,\s*/', $v, -1, \PREG_SPLIT_NO_EMPTY);
                })
            ->end()
            ->prototype('scalar')->end()
            ->validate()
                ->always()
                ->then(static function ($errors) {
                    static $level = null;
                    static $missingErrors = [];
                    foreach (\array_unique($errors) as $error) {
                        $constant = \strtoupper("E_{$error}");
                        if (\defined($constant)) {
                            $level |= \constant($constant);
                        } else {
                            $missingErrors[] = $constant;
                        }
                    }

                    if (empty($missingErrors)) {
                        return $level;
                    }

                    throw new InvalidConfigurationException(\sprintf(
                        'The errors are not supported: "%s".',
                        \implode('", "', $missingErrors)
                    ));
                })
            ->end()
        ;

        return $node;
    }

    private function normalizeRootNode(ArrayNodeDefinition $rootNode): void
    {
        $normalizer = static function (array $config): array {
            static $keys = [
                'pcntl'    => 'usePcntl',
                'readline' => 'useReadline',
                'unicode'  => 'useUnicode',
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

        $rootNode->validate()->always()->then($normalizer)->end();
    }
}
