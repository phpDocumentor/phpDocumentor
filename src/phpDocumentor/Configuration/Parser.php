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
 * Configuration definition for the parser.
 */
class Parser
{
    /**
     * @var string name of the package when there is no @package tag defined
     * @Serializer\Type("string")
     */
    protected $defaultPackageName;

    /**
     * @var string destination location for the parser's output cache
     * @Serializer\Type("string")
     */
    protected $target;
}
