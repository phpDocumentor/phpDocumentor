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

/**
 * Defines the contract between the Parser and a Parser Backend.
 *
 * PhpDocumentor's parser allows for having multiple backends that can process a file based on its metadata. This means
 * that, for example, phpDocumentor knows how to handle a PHP file based on its extension.
 *
 * The communication between the Parser and the Backend goes through two stages:
 *
 * 1. The parser boots and in sequence boots all registered backends using the {@see boot()} method.
 * 2. After the parser has collected all files  in the project it will, for each file, find a matching backend using
 *    the {@see matches()} method and if that method returns true call the {@see parse()} method.
 *
 * @see Parser::registerBackend()
 */
interface Backend
{
    /**
     * Initializes the current backend with the given Parser Configuration.
     *
     * @param Configuration $configuration
     *
     * @return void
     */
    public function boot(Configuration $configuration);

    /**
     * Determines whether this backend is capable of dealing with the given file by examining its metadata.
     *
     * @param \SplFileInfo $file
     *
     * @return boolean
     */
    public function matches(\SplFileInfo $file);

    /**
     * Processes the contents of the given file and stores the processed content.
     *
     * The parser, that invokes this method, does not expect any return value. Each backend is expected to know where
     * and how to store the analyzed results for the given file.
     *
     * @param \SplFileObject $file
     *
     * @return void
     */
    public function parse(\SplFileObject $file);
}
