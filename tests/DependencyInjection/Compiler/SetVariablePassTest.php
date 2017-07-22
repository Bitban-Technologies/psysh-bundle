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
    /** @test */
    public function it_valid_processed_when_no_shell()
    {
        $container = $this->container();
        $container->removeDefinition('psysh.shell');

        // Execute
        $container->compile();

        // Verify
        self::assertFalse($this->hasSetScopeVariablesCall($container));

        $container = $this->container();

        // Execute
        $container->compile();

        // Verify
        self::assertFalse($this->hasSetScopeVariablesCall($container));
    }

    public function scopeVariables(): array
    {
        return [
            ['test',          stdClass::class, 'test'],
            [stdClass::class, stdClass::class, 'stdClass'],
            [TestCase::class, stdClass::class, 'phpunitFrameworkTestCase'],
        ];
    }

    /**
     * @test
     * @dataProvider scopeVariables
     */
    public function it_valid_processed_when_tagged($name, $class, $expected)
    {
        $container = $this->container();
        $container->register($name, $class)
            ->addTag('psysh.variable', []);

        // Execute
        $container->compile();

        // Verify
        self::assertContains(
            $expected,
            $container->get('psysh.shell')->getScopeVariableNames()
        );
    }

    /** @test */
    public function it_valid_processed_when_tagged_with_attribute()
    {
        $abttributeName = 'test';

        $container = $this->container();
        $container->register('service', stdClass::class)
            ->addTag('psysh.variable', ['name' => $abttributeName]);

        // Execute
        $container->compile();

        // Verify
        self::assertContains(
            $abttributeName,
            $container->get('psysh.shell')->getScopeVariableNames()
        );
    }

    /** @test */
    public function it_valid_processed_when_tagged_already_has_variables()
    {
        $variables = ['container' => 'service_container'];

        $container = $this->container();
        $container->getDefinition('psysh.shell')
            ->addMethodCall('setScopeVariables', [$variables]);

        $container->register('service', stdClass::class)
            ->addTag('psysh.variable', ['name' => 'test']);

        // Execute
        $container->compile();

        // Verify
        self::assertContains(
            key($variables),
            $container->get('psysh.shell')->getScopeVariableNames()
        );
    }

    private function hasSetScopeVariablesCall(ContainerBuilder $container): bool
    {
        return $container->hasDefinition('psysh.shell')
            && $container->getDefinition('psysh.shell')->hasMethodCall('setScopeVariables');
    }

    private function container(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new SetVariablePass());
        $container->register('psysh.shell', Shell::class);

        return $container;
    }
}
