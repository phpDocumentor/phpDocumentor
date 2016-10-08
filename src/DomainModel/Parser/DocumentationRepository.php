<?php
namespace phpDocumentor\DomainModel\Parser;

use phpDocumentor\DomainModel\Parser\Version\Number;

/**
 * This repository acts as a wrapper for documentation cache.
 */
interface DocumentationRepository
{
    /**
     * Find documentation by versionNumber.
     *
     * @param Number $versionNumber
     *
     * @return Documentation|null
     */
    public function findByVersionNumber(Number $versionNumber);

    public function hasForVersionNumber(Number $versionNumber) : bool;

    /**
     * Store the documentation object to the data store for later usage.
     *
     * @param Documentation $documentation
     */
    public function save(Documentation $documentation);
}
