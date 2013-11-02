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

namespace phpDocumentor\Configuration;

use JMS\Serializer\Annotation as Serializer;

/**
 * Represents the settings in the phpdoc.xml related to finding the files that are to be parsed.
 */
class Files
{
    /**
     * @var string[]
     * @Serializer\Type("array<string>")
     */
    protected $directories;

    /**
     * @var string[] files
     * @Serializer\Type("array<string>")
     */
    protected $files;

    /**
     * @var string[] ignore
     * @Serializer\Type("array<string>")
     */
    protected $ignore;
}
