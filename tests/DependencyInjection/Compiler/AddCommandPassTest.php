<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\Tests\DependencyInjection\Compiler;

use AlexMasterov\PsyshBundle\DependencyInjection\Compiler\AddCommandPass;
use AlexMasterov\PsyshBundle\Tests\DependencyInjection\Compiler\TestCommand;
use PHPUnit\Framework\TestCase;
use Psy\{
    Command\Command,
    Configuration,
    Shell
};
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Reference
};

final class AddCommandPassTest extends TestCase
{
    /** @test */
    public function it_valid_processed_then_no_shell()
    {
        $container = $this->container();
        $container->removeDefinition('psysh.shell');

        // Execute
        $container->compile();

        // Verify
        self::assertFalse($this->hasAddCommandsCall($container));

        $container = $this->container();

        // Execute
        $container->compile();

        // Verify
        self::assertFalse($this->hasAddCommandsCall($container));
    }

    /** @test */
    public function it_valid_processed_when_tagged()
    {
        $container = $this->container();
        $container->register(TestCommand::class)
            ->setAutoconfigured(true);

        // Execute
        $container->compile();

        // Verify
        self::assertTrue($this->hasAddCommandsCall($container));
        self::assertInstanceOf(
            TestCommand::class,
            $container->get('psysh.shell')->find('test')
        );
    }

    private function hasAddCommandsCall(ContainerBuilder $container): bool
    {
        return $container->getDefinition('psysh.config')
            ->hasMethodCall('addCommands');
    }

    private function container(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new AddCommandPass());
        $container->register('psysh.config', Configuration::class);
        $container->register('psysh.shell', Shell::class)
            ->addArgument(new Reference('psysh.config'));
        $container->registerForAutoconfiguration(Command::class)
            ->addTag('psysh.command');

        return $container;
    }
}
