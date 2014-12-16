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
use phpDocumentor\Parser\Loader\Git;
use phpDocumentor\Parser\Loader\LoaderInterface;
use phpDocumentor\Parser\Loader\Local;
use phpDocumentor\Parser\Loader\Uri;

/**
 * Service that is helps with populating a Fileset Collection object with the options in the parser configuration so
 * that it can scan for all files and folders that should be included when parsing.
 */
final class Loader
{
    /** @var LoaderInterface[] */
    private $handlers = array();

    /**
     * Registers the default handlers so that local files and files in git repositories can be loaded.
     *
     * @uses self::registerHandler()
     *
     * @return void
     */
    public function registerDefaultHandlers()
    {
        $this->registerHandler(new Local());
        $this->registerHandler(new Git());
    }

    /**
     * Loads the file listing as defined in the configuration and populates the Fileset Collection with the results.
     *
     * This function does not load the actual file contents but it initializes the given Fileset Collection so that
     * an accurate file listing is returned. The returned Fileset Collection can then be iterated to get an accurate
     * listing of files.
     *
     * @param Collection    $collection
     * @param Configuration $configuration
     *
     * @return Collection
     */
    public function load(Collection $collection, Configuration $configuration)
    {
        if (empty($this->handlers)) {
            $this->registerDefaultHandlers();
        }

        $this->setFilters($collection, $configuration);
        $this->loadLocations($collection, $configuration);

        return $collection;
    }

    /**
     * @param LoaderInterface $handler
     */
    public function registerHandler(LoaderInterface $handler)
    {
        $this->handlers[get_class($handler)] = $handler;
    }

    /**
     * Tries to find a loader that matches the given location string.
     *
     * @param Uri $location
     *
     * @return LoaderInterface|null
     */
    private function findHandlerForLocation(Uri $location)
    {
        foreach ($this->handlers as $loader) {
            if ($loader->match($location)) {
                return $loader;
            }
        }

        return null;
    }

    /**
     * @param Collection $collection
     * @param $location
     */
    private function loadLocation(Collection $collection, $location)
    {
        $loader = $this->findHandlerForLocation($location);
        if (!$loader) {
            throw new \InvalidArgumentException(
                'The location "' . $location . '" can not be loaded, no loader is registered that knows how to load'
                . ' this type of location.'
            );
        }

        $paths = $loader->fetch($location);
        if (isset($paths['files']) && $paths['files']) {
            $collection->addFiles($paths['files']);
        }
        if (isset($paths['directories']) && $paths['directories']) {
            $collection->addDirectories($paths['directories']);
        }
    }

    /**
     * @param Collection $collection
     * @param Configuration $configuration
     */
    private function loadLocations(Collection $collection, Configuration $configuration)
    {
        $locations = array_merge($configuration->getFiles()->getFiles(), $configuration->getFiles()->getDirectories());
        foreach ($locations as $location) {
            $this->loadLocation($collection, new Uri($location));
        }
    }

    /**
     * @param Collection $collection
     * @param Configuration $configuration
     */
    private function setFilters(Collection $collection, Configuration $configuration)
    {
        $collection->setAllowedExtensions($configuration->getExtensions());
        $collection->setIgnorePatterns($configuration->getFiles()->getIgnore());
        $collection->setIgnoreHidden($configuration->getFiles()->isIgnoreHidden());
        $collection->setFollowSymlinks(!$configuration->getFiles()->isIgnoreSymlinks());
    }
}
