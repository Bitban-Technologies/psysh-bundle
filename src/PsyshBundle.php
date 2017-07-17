<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle;

use AlexMasterov\PsyshBundle\DependencyInjection\{
    Compiler\AddCommandPass,
    Compiler\SetVariablePass,
    Extension
};
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PsyshBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddCommandPass());
        $container->addCompilerPass(new SetVariablePass());
    }

    /**
     * @inheritDoc
     */
    public function getContainerExtension()
    {
        return new Extension();
    }
}
