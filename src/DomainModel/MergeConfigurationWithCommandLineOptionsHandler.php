<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel;

use phpDocumentor\Application\Configuration\ConfigurationFactory;
use phpDocumentor\Application\Configuration\Factory\CommandlineOptionsMiddleware;
use phpDocumentor\Configuration;
use phpDocumentor\DomainModel\MergeConfigurationWithCommandLineOptions;
use phpDocumentor\DomainModel\Uri;

final class MergeConfigurationWithCommandLineOptionsHandler
{
    /** @var ConfigurationFactory */
    private $configurationFactory;

    /** @var  CommandlineOptionsMiddleware */
    private $commandlineOptionsMiddleware;

    /**
     * MergeConfigurationWithCommandLineOptionsHandler constructor.
     *
     * @param ConfigurationFactory         $configurationFactory
     * @param CommandlineOptionsMiddleware $commandlineOptionsMiddleware
     */
    public function __construct(
        ConfigurationFactory $configurationFactory,
        CommandlineOptionsMiddleware $commandlineOptionsMiddleware
    ) {
        $this->configurationFactory         = $configurationFactory;
        $this->commandlineOptionsMiddleware = $commandlineOptionsMiddleware;
    }

    public function __invoke(MergeConfigurationWithCommandLineOptions $command)
    {
        if (isset($command->getOptions()['config'])
            && $command->getOptions()['config']
            && realpath($command->getOptions()['config'])
        ) {
            $uri = new Uri(realpath($command->getOptions()['config']));
            $this->configurationFactory->replaceLocation($uri);
        }

        $this->commandlineOptionsMiddleware->provideOptions($command->getOptions());
        $this->configurationFactory->clearCache();
    }
}
