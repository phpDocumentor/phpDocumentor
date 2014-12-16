<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Loader;

use phpDocumentor\Fileset\Collection;
use Symfony\Component\Process\Process;

class Git implements LoaderInterface
{
    /** @var string */
    private $temporaryFolder;

    public function __construct($temporaryFolder = null)
    {
        $this->temporaryFolder = $temporaryFolder ?: sys_get_temp_dir() . '/phpdoc-'  . md5(microtime(true));
    }

    public function match(Uri $location)
    {
        return ($location->getScheme() == 'git');
    }

    /**
     * @param string $location
     *
     * @return array {
     *   @element string[] 'files'?
     *   @element string[] 'directories'?
     * }
     */
    public function fetch(Uri $location)
    {
        if (! $this->match($location)) {
            throw new \InvalidArgumentException(
                'Invalid location "' . $location . '" was passed to the loader "' . __CLASS__ . '"'
            );
        }

        $destination = $this->temporaryFolder . '-' . md5((string)$location);

        $this->cloneRepository($location, $destination);
        $this->checkoutBranch($location->getOption('branch'), $destination);

        return array(
            'directories' => array($destination)
        );
    }

    /**
     * @param Uri $location
     * @param $destination
     */
    private function cloneRepository(Uri $location, $destination)
    {
        $process = new Process("git clone $location $destination");
        $process->run();
        if (! $process->isSuccessful()) {
            throw new \RuntimeException(
                "Failed to clone '$location', please verify your connection and that the 'git' executable is in your "
                . "path. Git returned: " . $process->getErrorOutput()
            );
        }
    }

    /**
     * @param string $branch
     * @param string $cloneLocation
     */
    private function checkoutBranch($branch, $cloneLocation)
    {
        if (! $branch) {
            return;
        }

        $process = new Process("git checkout $branch", $cloneLocation);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException("Failed to checkout '$branch', please verify if the branch exists");
        }
    }
}
