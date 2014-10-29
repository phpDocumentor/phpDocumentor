<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Descriptor\TagDescriptor;

/**
 * Descriptor representing the example tag with a descriptor.
 */
class ExampleDescriptor extends TagDescriptor
{
    /** @var string $filePath the content of the example. */
    protected $filePath;

    /** @var int $lineCount the content of the example. */
    protected $lineCount;

    /** @var int $startingLine the content of the example. */
    protected $startingLine;

    /** @var string $example the content of the example. */
    protected $example;

    /**
     * Sets the location where the example points to.
     *
     * @param string $filePath
     *
     * @return void
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Returns the location where this example points to.
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Returns the location where this example points to.
     *
     * @return void
     */
    public function setStartingLine($startingLine)
    {
        $this->startingLine = $startingLine;
    }

    /**
     * Returns the location where this example points to.
     *
     * @return int
     */
    public function getStartingLine()
    {
        return $this->startingLine;
    }

    /**
     * Returns the location where this example points to.
     *
     * @return void
     */
    public function setLineCount($lineCount)
    {
        $this->lineCount = $lineCount;
    }

    /**
     * Returns the location where this example points to.
     *
     * @return int
     */
    public function getLineCount()
    {
        return $this->lineCount;
    }

    /**
     * Returns the content of the example.
     *
     * @return void
     */
    public function setExample($example)
    {
        $this->example = $example;
    }

    /**
     * Returns the content of the example.
     *
     * @return string
     */
    public function getExample()
    {
        return $this->example;
    }
}
