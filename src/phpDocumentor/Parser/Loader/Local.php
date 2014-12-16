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

class Local implements LoaderInterface
{
    public function match(Uri $location)
    {
        return ($location->getScheme() == 'file' || $location->getScheme() == '');
    }

    /**
     * @param Uri $location
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

        if (! $location->getPath()) {
            throw new \InvalidArgumentException('No path was passed to scan');
        }

        $destination = new \SplFileInfo($location->getPath());
        $isDir = $destination->isDir();
        $destination = $this->getFileListing($location->getPath(), $isDir ? GLOB_ONLYDIR : null);

        $key = $isDir ? 'directories' : 'files';

        return array($key => $destination);
    }

    /**
     * Finds all files (or directories if GLOB_ONLYDIR is passed as flag) that match the glob strings passed in
     * the paths argument.
     *
     * @param string       $path
     * @param integer|null $flags an optional set of flags that will be passed to the glob function that assists with
     *     finding files and directories.
     *
     * @return string[]|null
     */
    private function getFileListing($path, $flags = null)
    {
        if (!is_string($path)) {
            return null;
        }

        $matches = glob($path, $flags);
        if (! is_array($matches)) {
            $matches = array($matches);
        }

        $result = array();
        foreach ($matches as $file) {
            if (empty($file)) {
                continue;
            }

            $file = realpath($file);
            if (empty($file)) {
                continue;
            }

            $result[] = $file;
        }

        return $result;
    }
}
