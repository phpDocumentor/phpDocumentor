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

use phpDocumentor\Descriptor;
use phpDocumentor\Transformer\Router\UrlGenerator\UrlGeneratorInterface;

class ConstantDescriptor implements UrlGeneratorInterface
{
    /**
     * Generates a URL from the given node or returns false if unable.
     *
     * @param string|Descriptor\ConstantDescriptor $node
     *
     * @return string|false
     */
    public function __invoke($node)
    {
        $name = $node->getName();

        // global constant
        if ($node->getParent() instanceof Descriptor\FileDescriptor || ! $node->getParent()) {
            $namespaceName = $node->getNamespace();

            return '/namespaces/' . str_replace('\\', '.', ltrim($namespaceName, '\\')).'.html#constant_' . $name;
        }

        // class constant
        $className = $node->getParent()->getFullyQualifiedStructuralElementName();

        return '/classes/' . str_replace('\\', '.', ltrim($className, '\\')).'.html#constant_' . $name;
    }
}
