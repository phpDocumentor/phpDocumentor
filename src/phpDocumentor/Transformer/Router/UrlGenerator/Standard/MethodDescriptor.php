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
use phpDocumentor\Transformer\Router\UrlGenerator\UrlGeneratorInterface as UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Generates a relative URL with methods for use in the generated HTML documentation.
 */
class MethodDescriptor implements UrlGenerator
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
     * @param string|Descriptor\MethodDescriptor $node
     *
     * @return string
     */
    public function __invoke($node)
    {
        return $this->urlGenerator->generate(
            'method',
            [
                'className' => $this->converter->fromClass(
                    $node->getParent()->getFullyQualifiedStructuralElementName()
                ),
                'methodName' => $node->getName()
            ]
        );
    }
}
