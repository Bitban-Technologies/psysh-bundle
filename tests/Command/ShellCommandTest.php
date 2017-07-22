<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\Tests\Command;

use AlexMasterov\PsyshBundle\Command\ShellCommand;
use PHPUnit\Framework\TestCase;
use Psy\Shell;
use Symfony\Component\Console\{
    Input\InputInterface,
    Output\OutputInterface
};

final class ShellCommandTest extends TestCase
{
    /** @test */
    public function it_valid_configure()
    {
        $command = $this->command();

        // Verify
        self::assertSame('psysh:shell', $command->getName());
        self::assertSame(['sh'], $command->getAliases());
        self::assertSame('Run Psy Shell', $command->getDescription());
    }

    /** @test */
    public function it_valid_execute()
    {
        // Mock
        $input = self::createMock(InputInterface::class);
        $output = self::createMock(OutputInterface::class);

        $getExecuteMethod = function () use ($input, $output) {
            return $this->execute($input, $output);
        };

        // Verify
        self::assertNull($getExecuteMethod->call($this->command()));
    }

    private function command(): ShellCommand
    {
        $shell = self::createMock(Shell::class);
        $shell->expects(self::any())->method('run');

        return new ShellCommand($shell);
    }
}
