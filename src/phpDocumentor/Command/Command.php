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
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base command for phpDocumentor commands.
 *
 * Includes additional methods to forward the output to the logging events
 * of phpDocumentor.
 */
class Command extends \Cilex\Command\Command
{
    public function setHelperSet(HelperSet $helperSet)
    {
        parent::setHelperSet($helperSet);

        $this->getHelper('phpdocumentor_logger')->addOptions($this);
    }

    /**
     * Returns boolean based on whether given path is absolute or not.
     *
     * @param string $path Given path
     *
     * @author Michael Wallner <mike@php.net>
     *
     * @link http://pear.php.net/package/File_Util/docs/latest/File/File_Util/File_Util.html#methodisAbsolute
     *
     * @todo consider moving this method to a more logical place
     *
     * @return boolean True if the path is absolute, false if it is not
     */
    protected function isAbsolute($path)
    {
        if (preg_match('/(?:\/|\\\)\.\.(?=\/|$)/', $path)) {
            return false;
        }

        // windows detection
        if (defined('OS_WINDOWS') ? OS_WINDOWS : !strncasecmp(PHP_OS, 'win', 3)) {
            return (($path[0] == '/') || preg_match('/^[a-zA-Z]:(\\\|\/)/', $path));
        }

        return ($path[0] == '/') || ($path[0] == '~');
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
