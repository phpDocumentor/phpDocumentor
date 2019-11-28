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

use phpDocumentor\Descriptor;
use phpDocumentor\Transformer\Router\UrlGenerator\UrlGeneratorInterface as UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ConstantDescriptor implements UrlGenerator
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
     * @param string|Descriptor\ConstantDescriptor $node
     *
     * @return string
     */
    public function __invoke($node)
    {
        if ($this->isGlobalConstant($node)) {
            return $this->generateUrlForGlobalConstant($node);
        }

        return $this->generateUrlForClassConstant($node);
    }

    private function generateUrlForGlobalConstant(Descriptor\ConstantDescriptor $node): string
    {
        return $this->urlGenerator->generate(
            'global_constant',
            [
                'namespaceName' => $this->converter->fromNamespace($node->getNamespace()),
                'constantName' => $node->getName()
            ]
        );
    }

    private function generateUrlForClassConstant(Descriptor\ConstantDescriptor $node): string
    {
        return $this->urlGenerator->generate(
            'class_constant',
            [
                'className' => $this->converter->fromNamespace(
                    $node->getParent()->getFullyQualifiedStructuralElementName()
                ),
                'constantName' => $node->getName()
            ]
        );
    }

    private function isGlobalConstant(Descriptor\ConstantDescriptor $node): bool
    {
        return ($node->getParent() instanceof Descriptor\FileDescriptor || !$node->getParent());
    }
}
