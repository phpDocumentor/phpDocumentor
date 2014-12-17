<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser;

use phpDocumentor\Fileset\Collection;

/**
 * Service that is helps with populating a Fileset Collection object with the options in the parser configuration so
 * that it can scan for all files and folders that should be included when parsing.
 */
final class Fileset
{
    /**
     * Populates the given file collection based on the configuration and returns it.
     *
     * @param Collection $collection
     * @param Configuration $configuration
     *
     * @return Collection
     */
    public function populate(Collection $collection, Configuration $configuration)
    {
        $collection->setAllowedExtensions($configuration->getExtensions());
        $collection->setIgnorePatterns($configuration->getFiles()->getIgnore());
        $collection->setIgnoreHidden($configuration->getFiles()->isIgnoreHidden());
        $collection->setFollowSymlinks(! $configuration->getFiles()->isIgnoreSymlinks());
        $collection->addFiles($this->getFileListing($configuration->getFiles()->getFiles()));
        $collection->addDirectories($this->getFileListing($configuration->getFiles()->getDirectories(), GLOB_ONLYDIR));

        return $collection;
    }

    /**
     * Finds all files (or directories if GLOB_ONLYDIR is passed as flag) that match the glob strings passed in
     * the paths argument.
     *
     * @param string[]     $paths
     * @param integer|null $flags an optional set of flags that will be passed to the glob function that assists with
     *     finding files and directories.
     *
     * @return string[]
     */
    private function getFileListing(array $paths, $flags = null)
    {
        $result = array();
        foreach ($paths as $glob) {
            if (!is_string($glob)) {
                continue;
            }

            $matches = glob($glob, $flags);
            if (! is_array($matches)) {
                $matches = array($matches);
            }

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
        }

        return $result;
    }
}
