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

use phpDocumentor\Reflection\DocBlock\Tags\Reference\Fqsen;
use phpDocumentor\Transformer\Router\UrlGenerator\UrlGeneratorInterface;

/**
 * Generates a relative URL with properties for use in the generated HTML documentation.
 */
class FqsenDescriptor implements UrlGeneratorInterface
{
    /**
     * Generates a URL from the given node or returns false if unable.
     *
     * @param string|Fqsen $node
     *
     * @return string|false
     */
    public function __invoke($node)
    {
        if (!($node instanceof Fqsen)) {
            return false;
        }

        $converter = new QualifiedNameToUrlConverter();
        $fqsenParts = explode('::', (string) $node);

        $className = $fqsenParts[0];

        if (count($fqsenParts) === 1) {
            return '/classes/' . $converter->fromClass($className) . '.html';
        }

        if (strpos($fqsenParts[1], '$') !== false) {
            $propertyName = explode('$', $fqsenParts[1]);
            return '/classes/' . $converter->fromClass($className) . '.html#property_' . $propertyName[1];
        }

        if (strpos($fqsenParts[1], '()') !== false) {
            $methodName = explode('()', $fqsenParts[1]);
            return '/classes/' . $converter->fromClass($className) . '.html#method_' . $methodName[0];
        }

        return '/classes/' . $converter->fromClass($className) . '.html#constant_' . $fqsenParts[1];
    }
}
