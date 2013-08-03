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

namespace phpDocumentor\Transformer\Router\UrlGenerator\Standard;

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Transformer\Router\UrlGenerator\UrlGeneratorInterface;

class ClassDescriptor implements UrlGeneratorInterface
{
    /**
     * Generates a URL from the given node or returns false if unable.
     *
     * @param DescriptorAbstract $node
     *
     * @return string|false
     */
    public function __invoke($node)
    {
        return '/classes/'
            . str_replace('\\', '.', ltrim($node->getFullyQualifiedStructuralElementName(), '\\')).'.html';
    }
}
