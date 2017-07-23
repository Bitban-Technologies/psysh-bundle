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
        // Stub
        $command = $this->getCommand();

        // Verify
        self::assertSame('psysh:shell', $command->getName());
        self::assertSame(['sh'], $command->getAliases());
        self::assertSame('Run Psy Shell', $command->getDescription());
    }

    /** @test */
    public function it_valid_execute()
    {
        // Stub
        $command = $this->getCommand();

        // Execute
        $code = $this->executeCommand($command);

        // Verify
        self::assertSame(0, $code);
    }

    private function getCommand(): ShellCommand
    {
        $shell = self::createMock(Shell::class);
        $shell->expects(self::any())->method('run')->willReturn(0);

        return new ShellCommand($shell);
    }

    private function executeCommand($command): ?int
    {
        $input = self::createMock(InputInterface::class);
        $output = self::createMock(OutputInterface::class);

        $getExecuteMethod = function () use ($input, $output) {
            return $this->execute($input, $output);
        };

        return $getExecuteMethod->call($command);
    }
}
