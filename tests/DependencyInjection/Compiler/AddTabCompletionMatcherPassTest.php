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
    /**
     * @test
     */
    public function it_valid_processed()
    {
        $container = $this->container();
        $container->register(MongoClientMatcher::class)
            ->setAutoconfigured(true);

        $container->compile();

        self::assertContainsOnlyInstancesOf(
            MongoClientMatcher::class,
            $container->get('psysh.config')->getTabCompletionMatchers()
        );
    }

    private function container(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->register('psysh.config', Configuration::class);
        $container->register('psysh.shell', Shell::class)
            ->addArgument(new Reference('psysh.config'));

        $container->addCompilerPass(new AddTabCompletionMatcherPass());
        $container->registerForAutoconfiguration(AbstractMatcher::class)
            ->addTag('psysh.matcher');

        return $container;
    }
}
