<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Command;

use Cilex\Provider\Console\Command as BaseCommand;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Base command for phpDocumentor commands.
 *
 * Includes additional methods to forward the output to the logging events
 * of phpDocumentor.
 */
class Command extends BaseCommand
{
    /**
     * Registers the current command.
     */
    public function setHelperSet(HelperSet $helperSet)
    {
        parent::setHelperSet($helperSet);

        $this->getHelper('phpdocumentor_logger')->addOptions($this);
    }

    /**
     * Returns the Progress bar helper.
     *
     * With this helper it is possible to display a progress bar and make it fill.
     *
     * @return ProgressHelper
     */
    protected function getProgressBar(InputInterface $input)
    {
        if (!$input->getOption('progressbar')) {
            return null;
        }

        return $this->getHelperSet()->get('progress');
    }
}
