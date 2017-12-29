<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\Tests\DependencyInjection;

use AlexMasterov\PsyshBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\{
    Exception\InvalidConfigurationException,
    Processor
};

final class ConfigurationTest extends TestCase
{
    /** @test */
    public function it_valid_processed(): void
    {
        // Stub
        $config = [
            'variables'     => [
                'container' => '@service_container',
                '@service_container',
            ],
            'commands' => 'Psy\Command\WtfCommand',
            'default_includes' => [
                '/include/bootstrap.php',
            ],
            'error_logging_level'     => 'PARSE, NOTICE',
            'config_dir'              => '/config',
            'data_dir'                => '/data',
            'runtime_dir'             => '/tmp',
            'history_size'            => 50,
            'history_file'            => '/history',
            'manual_db_file'          => '/manual.sqlite',
            'tab_completion'          => true,
            'tab_completion_matchers' => [
                'Psy\TabCompletion\Matcher\MongoClientMatcher',
                'Psy\TabCompletion\Matcher\MongoDatabaseMatcher',
            ],
            'startup_message'         => '/hello',
            'require_semicolons'      => true,
            'erase_duplicates'        => true,
            'pcntl'                   => true,
            'readline'                => true,
            'unicode'                 => true,
            'color_mode'              => 'forced',
            'pager'                   => null,
            'bracketed_paste'         => true,
        ];

        $normalized = [
            'variables' => [
                'container' => '@service_container',
                '@service_container',
            ],
            'commands' => [
                'Psy\Command\WtfCommand',
            ],
            'defaultIncludes' => [
                '/include/bootstrap.php',
            ],
            'configDir'             => '/config',
            'dataDir'               => '/data',
            'runtimeDir'            => '/tmp',
            'historySize'           => 50,
            'historyFile'           => '/history',
            'manualDbFile'          => '/manual.sqlite',
            'tabCompletion'         => true,
            'tabCompletionMatchers' => [
                'Psy\TabCompletion\Matcher\MongoClientMatcher',
                'Psy\TabCompletion\Matcher\MongoDatabaseMatcher',
            ],
            'startupMessage'        => '/hello',
            'requireSemicolons'     => true,
            'eraseDuplicates'       => true,
            'usePcntl'              => true,
            'useReadline'           => true,
            'useUnicode'            => true,
            'colorMode'             => 'forced',
            'errorLoggingLevel'     => E_PARSE | E_NOTICE,
            'pager'                 => 'less',
            'updateCheck'           => 'never',
            'useBracketedPaste'     => true,
        ];

        // Execute
        $actual = $this->processConfiguration($config);

        // Verify
        self::assertEquals($normalized, $actual);
    }

    /** @test */
    public function it_throw_exception_on_invalid_error_logging_level(): void
    {
        // Stub
        $config = [
            'error_logging_level' => 'invalid_level',
        ];

        // Verify
        $this->expectException(InvalidConfigurationException::class);

        // Execute
        $this->processConfiguration($config);
    }

    private function processConfiguration(array $config = []): array
    {
        $configuration = new Configuration();
        $config = ['psysh' => $config];

        return (new Processor)->processConfiguration($configuration, $config);
    }
}
