<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\Tests\DependencyInjection\Compiler;

use AlexMasterov\PsyshBundle\DependencyInjection\Compiler\AddTabCompletionMatcherPass;
use PHPUnit\Framework\TestCase;
use Psy\TabCompletion\Matcher\{
    AbstractMatcher,
    MongoClientMatcher
};
use Psy\{
    Configuration,
    Shell
};
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Reference
};

final class AddTabCompletionMatcherPassTest extends TestCase
{
    /** @test */
    public function it_valid_processed_when_no_shell()
    {
        $container = $this->container();
        $container->removeDefinition('psysh.shell');

        // Execute
        $container->compile();

        // Verify
        self::assertFalse($this->hasAddTabCompletionMatchersCall($container));

        $container = $this->container();

        // Execute
        $container->compile();

        // Verify
        self::assertFalse($this->hasAddTabCompletionMatchersCall($container));
    }

    /** @test */
    public function it_valid_processed_when_tagged()
    {
        $container = $this->container();
        $container->register(MongoClientMatcher::class)
            ->setAutoconfigured(true);

        // Execute
        $container->compile();

        // Verify
        self::assertTrue($this->hasAddTabCompletionMatchersCall($container));
        self::assertContainsOnlyInstancesOf(
            MongoClientMatcher::class,
            $container->get('psysh.config')->getTabCompletionMatchers()
        );
    }

    private function hasAddTabCompletionMatchersCall(ContainerBuilder $container): bool
    {
        return $container->getDefinition('psysh.config')
            ->hasMethodCall('addTabCompletionMatchers');
    }

    private function container(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new AddTabCompletionMatcherPass());
        $container->register('psysh.config', Configuration::class);
        $container->register('psysh.shell', Shell::class)
            ->addArgument(new Reference('psysh.config'));
        $container->registerForAutoconfiguration(AbstractMatcher::class)
            ->addTag('psysh.matcher');

        return $container;
    }
}
