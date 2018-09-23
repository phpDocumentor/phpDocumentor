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
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CilexCompatibilityLayerBundle extends Bundle
{
    public function boot(): void
    {
        parent::boot();

        if ($this->container->has(Application::class)) {
            $this->container->get(Application::class);
        }
    }

    public function registerCommands(ConsoleApplication $application): void
    {
        if ($this->container->has('phpdocumentor.compatibility.extra_commands')) {
            $commands = $this->container->get('phpdocumentor.compatibility.extra_commands');

            foreach ($commands as $command) {
                $application->add($command);
            }
        }
    }
}
