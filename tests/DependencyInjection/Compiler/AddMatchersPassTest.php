<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\Tests\DependencyInjection\Compiler;

use AlexMasterov\PsyshBundle\DependencyInjection\Compiler\AddMatchersPass;
use AlexMasterov\PsyshBundle\Tests\DependencyInjection\CanContainer;
use PHPUnit\Framework\TestCase;
use stdClass;

final class AddMatchersPassTest extends TestCase
{
    use CanContainer;

    /** @test */
    public function it_valid_processed_when_no_shell(): void
    {
        // Stub
        $container = $this->getContainer();

        // Execute
        $container->compile();

        // Verify
        self::assertFalse($this->hasAddMatchersCall($container));
    }

    /** @test */
    public function it_valid_processed_when_no_tags(): void
    {
        // Stub
        $container = $this->getContainer();
        $container->register('psysh.shell', stdClass::class);

        // Execute
        $container->compile();

        // Verify
        self::assertFalse($this->hasAddMatchersCall($container));
    }

    /** @test */
    public function it_valid_processed_when_tagged(): void
    {
        // Stub
        $matcher = 'test_matcher';

        $container = $this->getContainer();
        $container->register('psysh.shell', stdClass::class)->setPublic(true);
        $container->register('psysh.config', stdClass::class)->setPublic(true);
        $container->register($matcher, stdClass::class)->setPublic(true)
            ->addTag('psysh.matcher');

        // Execute
        $container->compile();

        // Verify
        self::assertTrue($this->hasAddMatchersCall($container));
        self::assertContains($matcher, $this->getMatchers($container));
    }

    private function getContainer()
    {
        $container = $this->container();
        $container->addCompilerPass(new AddMatchersPass());

        return $container;
    }

    private function hasAddMatchersCall($container): bool
    {
        return $this->hasDefinitionMethodCall('psysh.config', 'addMatchers', $container);
    }

    private function getMatchers($container): array
    {
        $matchers = $this->getDefinitionMethodArguments('psysh.config', 'addMatchers', $container);

        return array_map(
            static function ($matcher) {
                return (string) $matcher;
            },
            $matchers
        );
    }
}
