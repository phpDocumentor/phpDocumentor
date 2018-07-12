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

namespace phpDocumentor\Application\Console\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand as BaseCommand;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base command for phpDocumentor commands.
 *
 * Includes additional methods to forward the output to the logging events
 * of phpDocumentor.
 */
abstract class Command extends BaseCommand
{
    /**
     * Registers the current command.
     */
    public function setHelperSet(HelperSet $helperSet): void
    {
        parent::setHelperSet($helperSet);
    }

    /**
     * Executes a callable piece of code and writes an entry to the log detailing how long it took.
     */
    protected function writeTimedLog(OutputInterface $output, string $message, string $operation, array $arguments = []): void
    {
        $output->write(sprintf('%-66.66s .. ', $message));
        $timerStart = microtime(true);

        call_user_func_array($operation, $arguments);

        $output->writeln(sprintf('%8.3fs', microtime(true) - $timerStart));
    }

    /**
     * @return object
     */
    public function getService(string $name)
    {
        return $this->getContainer()->get($name);
    }

    /**
     * Returns the Progress bar helper.
     *
     * With this helper it is possible to display a progress bar and make it fill.
     *
     * @return HelperInterface|null a ProgressBar if available
     */
    protected function getProgressBar(InputInterface $input): ?HelperInterface
    {
        if (!$input->getOption('progressbar')) {
            return null;
        }

        return $this->getHelperSet()->get('progress');
    }
}
