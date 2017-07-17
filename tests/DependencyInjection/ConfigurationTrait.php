<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\Tests\DependencyInjection;

use AlexMasterov\PsyshBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

trait ConfigurationTrait
{
    protected static function processConfiguration(array $config = []): array
    {
        $configuration = new Configuration();
        $config = ['psysh' => $config];

        return (new Processor)->processConfiguration($configuration, $config);
    }
}
