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

namespace phpDocumentor\Transformer\Router\UrlGenerator;

use phpDocumentor\Reflection\DocBlock\Tags\Reference\Fqsen;
use phpDocumentor\Transformer\Router\UrlGenerator\UrlGenerator as UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Generates a relative URL with properties for use in the generated HTML documentation.
 */
class FqsenDescriptor implements UrlGenerator
{
    private $urlGenerator;
    private $converter;

    public function __construct(UrlGeneratorInterface $urlGenerator, QualifiedNameToUrlConverter $converter)
    {
        $this->urlGenerator = $urlGenerator;
        $this->converter = $converter;
    }

    /**
     * Generates a URL from the given node or returns false if unable.
     *
     * @param string|Fqsen $node
     *
     * @return string|false
     */
    public function __invoke($node)
    {
        assert($node instanceof Fqsen);
        $fqsenParts = explode('::', (string) $node);
        $className = $this->converter->fromClass($fqsenParts[0]);

        if (count($fqsenParts) === 1) {
            return $this->urlGenerator->generate(
                'class',
                [
                    'className' => $className,
                ]
            );
        }

        if (strpos($fqsenParts[1], '$') !== false) {
            $propertyName = explode('$', $fqsenParts[1]);
            return $this->urlGenerator->generate(
                'property',
                [
                    'className' => $className,
                    'propertyName' => $propertyName[1]
                ]
            );
        }

        if (strpos($fqsenParts[1], '()') !== false) {
            $methodName = explode('()', $fqsenParts[1]);
            return $this->urlGenerator->generate(
                'method',
                [
                    'className' => $className,
                    'methodName' => $methodName[0]
                ]
            );
        }

        return $this->urlGenerator->generate(
            'class_constant',
            [
                'className' => $className,
                'constantName' => $fqsenParts[1]
            ]
        );
    }
}
