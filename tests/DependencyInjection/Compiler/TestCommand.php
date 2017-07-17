<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\Tests\DependencyInjection\Compiler;

use Psy\Command\Command;
use Symfony\Component\Console\{
    Input\InputInterface,
    Output\OutputInterface
};

class TestCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('test')
            ->setDescription('Show test.')
            ->setHelp('Show test.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $output->writeln('test');
    }
}
