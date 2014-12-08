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

namespace phpDocumentor\Parser;

use phpDocumentor\Fileset\Collection;

class Fileset
{
    /**
     * Populates the given file collection based on the configuration and returns it.
     *
     * @param Collection $collection
     * @param \phpDocumentor\Configuration $configuration
     *
     * @return Collection
     */
    public function populate(Collection $collection, \phpDocumentor\Configuration $configuration)
    {
        $collection->setAllowedExtensions($configuration->getParser()->getExtensions());
        $collection->setIgnorePatterns($configuration->getFiles()->getIgnore());
        $collection->setIgnoreHidden($configuration->getFiles()->isIgnoreHidden());
        $collection->setFollowSymlinks(! $configuration->getFiles()->isIgnoreSymlinks());
        $collection->addFiles($this->getFileListing($configuration->getFiles()->getFiles()));
        $collection->addDirectories($this->getFileListing($configuration->getFiles()->getDirectories(), GLOB_ONLYDIR));

        return $collection;
    }

    /**
     * @param $files
     * @param $flags
     * @return array
     */
    private function getFileListing($files, $flags = null)
    {
        $result = array();
        foreach ($files as $glob) {
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
