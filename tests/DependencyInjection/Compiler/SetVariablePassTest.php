<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\Tests\DependencyInjection\Compiler;

use AlexMasterov\PsyshBundle\DependencyInjection\Compiler\SetVariablePass;
use AlexMasterov\PsyshBundle\Tests\DependencyInjection\CanContainer;
use PHPUnit\Framework\TestCase;
use stdClass;

final class SetVariablePassTest extends TestCase
{
    use CanContainer;

    /** @test */
    public function it_valid_processed_when_no_shell()
    {
        // Stub
        $container = $this->getContainer();

        // Execute
        $container->compile();

        // Verify
        self::assertFalse($this->hasSetScopeVariablesCall($container));
    }

    /** @test */
    public function it_valid_processed_when_no_tags()
    {
        // Stub
        $container = $this->getContainer();
        $container->register('psysh.shell', stdClass::class);

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
        // Stub
        $container = $this->getContainer();
        $container->register('psysh.shell', stdClass::class);
        $container->register($name, $class)
            ->addTag('psysh.variable');

        // Execute
        $container->compile();

        // Verify
        self::assertTrue($this->hasSetScopeVariablesCall($container));
        self::assertArrayHasKey($expected, $this->getScopeVariables($container));
    }

    /** @test */
    public function it_valid_processed_when_tagged_with_attribute()
    {
        // Stub
        $abttributeName = 'test';

        $container = $this->getContainer();
        $container->register('psysh.shell', stdClass::class);
        $container->register('test_service', stdClass::class)
            ->addTag('psysh.variable', ['name' => $abttributeName]);

        // Execute
        $container->compile();

        // Verify
        self::assertTrue($this->hasSetScopeVariablesCall($container));
        self::assertArrayHasKey($abttributeName, $this->getScopeVariables($container));
    }

    /** @test */
    public function it_valid_processed_when_tagged_and_already_has_variables()
    {
        // Stub
        $variable = 'container';
        $abttributeName = 'test';

        $container = $this->getContainer();
        $container->register('psysh.shell', stdClass::class)
            ->addMethodCall('setScopeVariables', [[$variable => stdClass::class]]);
        $container->register('test_service', stdClass::class)
            ->addTag('psysh.variable', ['name' => $abttributeName]);

        // Execute
        $container->compile();

        // Verify
        $variables = $this->getScopeVariables($container);
        self::assertArrayHasKey($variable, $variables);
        self::assertArrayHasKey($abttributeName, $variables);
    }

    private function getContainer()
    {
        $container = $this->container();
        $container->addCompilerPass(new SetVariablePass());

        return $container;
    }

    private function hasSetScopeVariablesCall($container): bool
    {
        return $this->hasDefinitionMethodCall('psysh.shell', 'setScopeVariables', $container);
    }

    private function getScopeVariables($container): array
    {
        return $this->getDefinitionMethodArguments('psysh.shell', 'setScopeVariables', $container);
    }
}
