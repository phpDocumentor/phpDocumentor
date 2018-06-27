<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Router\UrlGenerator\Standard;

use phpDocumentor\Descriptor;
use phpDocumentor\Transformer\Router\UrlGenerator\UrlGeneratorInterface;

class ConstantDescriptor implements UrlGeneratorInterface
{
    /** @var QualifiedNameToUrlConverter */
    private $converter;

    /**
     * Initializes this generator.
     */
    public function __construct()
    {
        $this->converter = new QualifiedNameToUrlConverter();
    }

    /**
     * Generates a URL from the given node or returns false if unable.
     *
     * @param string|Descriptor\ConstantDescriptor $node
     *
     * @return string|false
     */
    public function __invoke($node)
    {
        if (!($node instanceof Descriptor\ConstantDescriptor)) {
            return false;
        }

        $prefix = ($node->getParent() instanceof Descriptor\FileDescriptor || ! $node->getParent())
            ? $this->getUrlPathPrefixForGlobalConstants($node)
            : $this->getUrlPathPrefixForClassConstants($node);

        return $prefix . '.html#constant_' . $node->getName();
    }

    /**
     * Returns the first part of the URL path that is specific to global constants.
     *
     * @param Descriptor\ConstantDescriptor $node
     *
     * @return string
     */
    private function getUrlPathPrefixForGlobalConstants($node)
    {
        return '/namespaces/' . $this->converter->fromNamespace($node->getNamespace());
    }

    /**
     * Returns the first part of the URL path that is specific to class constants.
     *
     * @param Descriptor\ConstantDescriptor $node
     *
     * @return string
     */
    private function getUrlPathPrefixForClassConstants($node)
    {
        return '/classes/' . $this->converter->fromClass($node->getParent()->getFullyQualifiedStructuralElementName());
    }
}
