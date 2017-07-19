<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\Tests\DependencyInjection;

use AlexMasterov\PsyshBundle\PsyshBundle;
use AlexMasterov\PsyshBundle\Tests\DependencyInjection\ConfigurationTrait;
use PHPUnit\Framework\TestCase;
use Psy\Shell;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ExtensionTest extends TestCase
{
    use ConfigurationTrait;

    /** @test */
    public function it_valid_register()
    {
        $config = [
            'variables' => [
                'container' => '@service_container',
            ],
            'history_file' => sys_get_temp_dir() . '/psysh_history',
            'tab_completion_matchers' => [
                'Psy\TabCompletion\Matcher\MongoClientMatcher',
                'Psy\TabCompletion\Matcher\MongoDatabaseMatcher',
            ],
        ];

        $container = $this->container($config);

        self::assertTrue($container->has('psysh.shell'));
        self::assertInstanceOf(Shell::class, $container->get('psysh.shell'));
        self::assertArraySubset(
            array_keys($config['variables']),
            $container->get('psysh.shell')->getScopeVariableNames()
        );
    }

    private function container(array $config = []): ContainerBuilder
    {
        $container = new ContainerBuilder();

        // Apply compiler passes
        $bundle = new PsyshBundle();
        $bundle->build($container);

        $config = $this->processConfiguration($config);

        $getLoadInternal = function () use ($config, $container) {
            return $this->loadInternal($config, $container);
        };

        $getLoadInternal->call($bundle->getContainerExtension());

        return $container;
    }
}
