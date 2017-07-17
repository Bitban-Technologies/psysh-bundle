<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\Tests\DependencyInjection\Compiler;

use AlexMasterov\PsyshBundle\DependencyInjection\Compiler\AddCommandPass;
use AlexMasterov\PsyshBundle\Tests\DependencyInjection\Compiler\TestCommand;
use PHPUnit\Framework\TestCase;
use Psy\{
    Command\Command,
    Shell
};
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AddCommandPassTest extends TestCase
{
    /**
     * @test
     */
    public function it_valid_processed()
    {
        $container = $this->container();
        $container->register(TestCommand::class)
            ->setAutoconfigured(true);

        $container->compile();

        self::assertInstanceOf(
            TestCommand::class,
            $container->get('psysh.shell')->find('test')
        );
    }

    private function container(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->register('psysh.shell', Shell::class);

        $container->addCompilerPass(new AddCommandPass());
        $container->registerForAutoconfiguration(Command::class)
            ->addTag('psysh.command');

        return $container;
    }
}
