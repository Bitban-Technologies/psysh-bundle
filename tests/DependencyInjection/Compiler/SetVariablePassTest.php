<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\Tests\DependencyInjection\Compiler;

use AlexMasterov\PsyshBundle\DependencyInjection\Compiler\SetVariablePass;
use PHPUnit\Framework\TestCase;
use Psy\Shell;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SetVariablePassTest extends TestCase
{
    public function scopeVariables(): array
    {
        return [
            ['test',          stdClass::class, 'test'],
            [stdClass::class, stdClass::class, 'stdclass'],
            [TestCase::class, stdClass::class, 'phpunitFrameworkTestcase'],
        ];
    }

    /**
     * @test
     * @dataProvider scopeVariables
     */
    public function it_valid_processed($name, $class, $expected)
    {
        $container = $this->container();
        $container->register($name, $class)
            ->addTag('psysh.variable', []);

        $container->compile();

        self::assertContains(
            $expected,
            $container->get('psysh.shell')->getScopeVariableNames()
        );
    }

    /** @test */
    public function it_valid_processed_with_attribute()
    {
        $abttributeName = 'test';

        $container = $this->container();
        $container->register('service', stdClass::class)
            ->addTag('psysh.variable', ['name' => $abttributeName]);

        $container->compile();

        self::assertContains(
            $abttributeName,
            $container->get('psysh.shell')->getScopeVariableNames()
        );
    }

    /** @test */
    public function it_valid_processed_if_already_has_variables()
    {
        $variables = ['container' => 'service_container'];

        $container = $this->container();
        $container->getDefinition('psysh.shell')
            ->addMethodCall('setScopeVariables', [$variables]);

        $container->register('service', stdClass::class)
            ->addTag('psysh.variable', ['name' => 'test']);

        $container->compile();

        // scope variables are not overwritten
        self::assertContains(
            key($variables),
            $container->get('psysh.shell')->getScopeVariableNames()
        );
    }

    private function container(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->register('psysh.shell', Shell::class);

        $container->addCompilerPass(new SetVariablePass());

        return $container;
    }
}
