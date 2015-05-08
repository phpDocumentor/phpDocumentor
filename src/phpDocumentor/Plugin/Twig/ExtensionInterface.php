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

namespace phpDocumentor\Plugin\Twig;

use phpDocumentor\Descriptor\Interfaces;
use phpDocumentor\Transformer\Transformation;

/**
 * An interface shared by all Twig interfaces intended for phpDocumentor.
 */
interface ExtensionInterface
{
    /**
     * Registers the structure and transformation objects with this extension.
     *
     * The Structure and Transformation object can be used to get context from
     * and to provide additional information.
     *
     * @param ProjectInterface  $project        Represents the complete Abstract Syntax Tree.
     * @param Transformation    $transformation Represents the transformation meta data used in the current generation
     *     cycle.
     */
    public function __construct(ProjectInterface $project, Transformation $transformation);
}
