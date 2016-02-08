<?php
namespace phpDocumentor\DomainModel\Parser\Documentation\Api;

use phpDocumentor\DomainModel\Parser\Documentation\DocumentGroup\DocumentGroupFormat;
use phpDocumentor\Reflection\File;
use phpDocumentor\DomainModel\Parser\Documentation\DocumentGroup\Definition as DocumentGroupDefinitionInterface;

/**
 * Document group definition for Api documentation.
 */
interface Definition extends DocumentGroupDefinitionInterface
{
    /**
     * Returns the defined format.
     *
     * @return DocumentGroupFormat
     */
    public function getFormat();

    /**
     * Returns an array of paths to files to process.
     *
     * @return File[]
     */
    public function getFiles();
}
