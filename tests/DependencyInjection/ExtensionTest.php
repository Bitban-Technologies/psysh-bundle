<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\Tests\DependencyInjection;

use AlexMasterov\PsyshBundle\Tests\DependencyInjection\ConfigurationTrait;
use AlexMasterov\PsyshBundle\{
    DependencyInjection\Extension,
    PsyshBundle
};
use PHPUnit\Framework\TestCase;
use Psy\Shell;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ExtensionTest extends TestCase
{
    /** @test */
    public function it_valid_register(): void
    {
        static $SERVICE_NAME = 'psysh.shell';

        // Stub
        $config = [
            'variables' => [
                'container' => '@service_container',
            ],
            'history_file' => sys_get_temp_dir() . '/psysh_history',
            'use_tab_completion' => true,
            'matchers' => [
                'Psy\TabCompletion\Matcher\MongoClientMatcher',
                'Psy\TabCompletion\Matcher\MongoDatabaseMatcher',
            ],
        ];

        // Execute
        $container = $this->loadExtension($config);

        // Verify
        self::assertTrue($container->has($SERVICE_NAME));

        $shell = $container->get($SERVICE_NAME);
        self::assertInstanceOf(Shell::class, $shell);

        $scopeVariables = $shell->getScopeVariableNames();
        foreach (array_keys($config['variables']) as $variable) {
            self::assertContains($variable, $scopeVariables);
        }
    }

    private function loadExtension(array $config = [], ContainerBuilder $container = null): ContainerBuilder
    {
        $container ?? $container = new ContainerBuilder();

        // Apply compiler passes
        $bundle = new PsyshBundle();
        $bundle->build($container);

        $extenstion = $bundle->getContainerExtension();

        $config = ['psysh' => $config];
        $extenstion->load($config, $container);

        return $container;
    }
}
