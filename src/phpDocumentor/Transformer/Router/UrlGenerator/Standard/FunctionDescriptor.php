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

use phpDocumentor\Transformer\Router\UrlGenerator\UrlGeneratorInterface;

class FunctionDescriptor implements UrlGeneratorInterface
{
    /**
     * Generates a URL from the given node or returns false if unable.
     *
     * @param \phpDocumentor\Descriptor\FunctionDescriptor $node
     *
     * @return string|false
     */
    public function __invoke($node)
    {
        $namespaceName = $node->getNamespace();
        $name          = $node->getName();

        return '/namespaces/' . str_replace('\\', '.', ltrim($namespaceName, '\\')).'.html#function_' . $name;
    }
}
