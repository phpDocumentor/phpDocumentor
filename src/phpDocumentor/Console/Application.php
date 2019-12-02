<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Console;

use Jean85\PrettyVersions;
use OutOfBoundsException;
use PackageVersions\Versions;
use Symfony\Bundle\FrameworkBundle\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\KernelInterface;
use function file_get_contents;
use function ltrim;
use function sprintf;
use function trim;

final class Application extends BaseApplication
{
    public const VERSION = '@package_version@';

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        $this->setName('phpDocumentor');
        $this->setVersion($this->detectVersion());
    }

    protected function getCommandName(InputInterface $input) : ?string
    {
        // the regular setDefaultCommand option does not allow for options and arguments; with this workaround
        // we can have options and arguments when the first element in the argv options is not a recognized
        // command name.
        // We explicitly do not use the getFirstArgument of the $input variable since that skips options; we
        // explicitly want to know if the first thing after the php filename is a known command!
        if ((isset($_SERVER['argv'][1]) === false || $this->has($_SERVER['argv'][1]) === false)) {
            return 'project:run';
        }

        return $input->getFirstArgument();
    }

    protected function getDefaultInputDefinition() : InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();

        $inputDefinition->addOption(
            new InputOption(
                'config',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Location of a custom configuration file'
            )
        );
        $inputDefinition->addOption(
            new InputOption('log', null, InputOption::VALUE_OPTIONAL, 'Log file to write to')
        );

        return $inputDefinition;
    }

    /**
     * Returns the long version of the application.
     *
     * @return string The long application version
     */
    public function getLongVersion() : string
    {
        return sprintf('%s <info>%s</info>', $this->getName(), $this->getVersion());
    }

    private function detectVersion() : string
    {
        $version = static::VERSION;

        // prevent replacing the version by the PEAR building
        if (sprintf('%s%s%s', '@', 'package_version', '@') === static::VERSION) {
            $version = trim(file_get_contents(__DIR__ . '/../../../VERSION'));
            try {
                $version = PrettyVersions::getVersion(Versions::ROOT_PACKAGE_NAME)->getPrettyVersion();
                $version = sprintf('v%s', ltrim($version, 'v'));
            } catch (OutOfBoundsException $e) {
            }
        }
        return $version;
    }
}
