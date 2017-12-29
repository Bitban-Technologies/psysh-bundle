<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\Command;

use Psy\Shell;
use Symfony\Component\Console\{
    Command\Command,
    Input\InputInterface,
    Output\OutputInterface
};

class ShellCommand extends Command
{
    /**
     * @var Shell
     */
    private $shell;

    public function __construct(Shell $shell)
    {
        parent::__construct();

        $this->shell = $shell;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('psysh:shell')
            ->setAliases(['sh'])
            ->setDescription('Run Psy Shell');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        return $this->shell->run();
    }
}
