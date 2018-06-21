<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\CilexCompatibilityLayer;

use phpDocumentor\Application;
use phpDocumentor\Command\Helper\ConfigurationHelper;
use phpDocumentor\Command\Helper\LoggerHelper;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CilexCompatibilityLayerBundle extends Bundle
{
    public function boot()
    {
        parent::boot();

        if ($this->container->has(Application::class)) {
            $this->container->get(Application::class);
        }
    }

    public function registerCommands(\Symfony\Component\Console\Application $application)
    {
//        $application->getHelperSet()->set(new LoggerHelper());
//        $application->getHelperSet()->set(new ConfigurationHelper($this->container->get('config')));

        $application->getDefinition()->addOption(
            new InputOption(
                'config',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Location of a custom configuration file'
            )
        );
        $application->getDefinition()->addOption(
            new InputOption('log', null, InputOption::VALUE_OPTIONAL, 'Log file to write to')
        );

        if ($this->container->has('phpdocumentor.compatibility.extra_commands')) {
            $commands = $this->container->get('phpdocumentor.compatibility.extra_commands');

            foreach ($commands as $command) {
                $application->add($command);
            }
        }
    }
}
