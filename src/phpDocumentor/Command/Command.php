<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Command;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Base command for phpDocumentor commands.
 *
 * Includes additional methods to forward the output to the logging events
 * of phpDocumentor.
 */
class Command extends \Cilex\Command\Command
{
    /**
     * Registers the current command.
     *
     * @param HelperSet $helperSet
     */
    public function setHelperSet(HelperSet $helperSet)
    {
        parent::setHelperSet($helperSet);

        $this->getHelper('phpdocumentor_logger')->addOptions($this);
    }

    /**
     * Returns boolean based on whether given path is absolute or not.
     *
     * This method was taken from the FileLocator class of the Symfony Config component.
     *
     * @param string $path Given path
     *
     * @author Fabien Potencier <fabien@symfony.com>
     *
     * @todo consider moving this method to a more logical place
     *
     * @return boolean True if the path is absolute, false if it is not
     */
    protected function isAbsolute($path)
    {
        if ($path[0] == '/' || $path[0] == '\\'
            || (strlen($path) > 3 && ctype_alpha($path[0])
                && $path[1] == ':'
                && ($path[2] == '\\' || $path[2] == '/')
            )
            || null !== parse_url($path, PHP_URL_SCHEME)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Returns the Progress bar helper.
     *
     * With this helper it is possible to display a progress bar and make it fill.
     *
     * @param InputInterface $input
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
