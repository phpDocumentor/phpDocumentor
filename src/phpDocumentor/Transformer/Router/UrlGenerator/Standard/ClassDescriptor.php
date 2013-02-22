<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mvriel
 * Date: 2/22/13
 * Time: 11:37 AM
 * To change this template use File | Settings | File Templates.
 */

namespace phpDocumentor\Transformer\Router\UrlGenerator\Standard;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
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
        return '/classes/'.str_replace('\\', '.', ltrim($node->getFullyQualifiedStructuralElementName(), '\\')).'.html';
    }
}
