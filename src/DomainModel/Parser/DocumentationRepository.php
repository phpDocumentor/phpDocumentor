<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel\Parser;

use phpDocumentor\DomainModel\Parser\Documentation;
use phpDocumentor\DomainModel\Parser\Version\Number;
use Stash\Pool;

/**
 * This repository acts as a wrapper for documentation cache.
 */
final class DocumentationRepository
{
    /**
     * Cache namespace used for this repository.
     */
    const CACHE_NAMESPACE = 'Documentation';

    /**
     * Datastore used to cache Documentation
     *
     * @var Pool
     */
    private $dataStore;

    /**
     * Initializes the repository with the datastore to use.
     *
     * @param Pool $dataStore
     */
    public function __construct(Pool $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    /**
     * Find documentation by versionNumber.
     *
     * @param Number $versionNumber
     *
*@return Documentation|null
     */
    public function findByVersionNumber(Number $versionNumber)
    {
        $item = $this->dataStore->getItem($this->getItemName($versionNumber));

        if ($item->isMiss()) {
            return null;
        }

        return $item->get();
    }

    /**
     * Store the documentation object to the data store for later usage.
     *
     * @param Documentation $documentation
     */
    public function save(Documentation $documentation)
    {
        $item = $this->dataStore->getItem($this->getItemName($documentation->getVersionNumber()));
        $item->lock();
        $item->set($documentation);
    }

    /**
     * Convert VersionObject to item name.
     *
     * @param Number $versionNumber
     *
*@return string
     */
    private function getItemName(Number $versionNumber)
    {
        return static::CACHE_NAMESPACE . '\\' . $versionNumber;
    }
}
