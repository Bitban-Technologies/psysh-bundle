<?php
declare(strict_types=1);

namespace AlexMasterov\PsyshBundle\Tests\DependencyInjection;

use AlexMasterov\PsyshBundle\Tests\DependencyInjection\ConfigurationTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTrait;

    /** @test */
    public function it_valid_processed()
    {
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
        ];

        self::assertProcessedConfigurationEquals($normalized, $config);
    }

    /** @test */
    public function it_throw_exception_on_invalid_error_logging_level()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = [
            'error_logging_level' => 'invalid_level',
        ];

        self::assertProcessedConfigurationEquals([], $config);
    }

    private static function assertProcessedConfigurationEquals($expected, $config): void
    {
        $actual = self::processConfiguration($config);

        self::assertEquals($expected, $actual);
    }
}
