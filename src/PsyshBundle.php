<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle;

use AlexMasterov\PsyshBundle\DependencyInjection\{
    Compiler\AddCommandPass,
    Compiler\AddTabCompletionMatcherPass,
    Compiler\SetVariablePass,
    Extension
};
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PsyshBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddCommandPass());
        $container->addCompilerPass(new AddTabCompletionMatcherPass());
        $container->addCompilerPass(new SetVariablePass());
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return $this->extension
            ?? $this->extension = new Extension();
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function registerCommands(Application $application)
    {
    }
}
