<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Transformer\Router;

use phpDocumentor\Reflection\Fqsen;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

use function count;
use function explode;
use function strpos;

/**
 * Generates a relative URL with properties for use in the generated HTML documentation.
 */
class ClassBasedFqsenUrlGenerator
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var SluggerInterface */
    private $slugger;

    public function __construct(UrlGeneratorInterface $urlGenerator, SluggerInterface $slugger)
    {
        $this->urlGenerator = $urlGenerator;
        $this->slugger = $slugger;
    }

    /**
     * Generates a URL from the given node or returns false if unable.
     */
    public function __invoke(Fqsen $fqsen): string
    {
        $fqsenParts = explode('::', (string) $fqsen);
        $className = $this->slugger->slug($fqsenParts[0])->toString();

        if (count($fqsenParts) === 1) {
            return $this->urlGenerator->generate(
                'class',
                ['name' => $className]
            );
        }

        if (strpos($fqsenParts[1], '$') !== false) {
            $propertyName = explode('$', $fqsenParts[1]);

            return $this->urlGenerator->generate(
                'class',
                [
                    'name' => $className,
                    '_fragment' => 'property_' . $propertyName[1],
                ]
            );
        }

        if (strpos($fqsenParts[1], '()') !== false) {
            $methodName = explode('()', $fqsenParts[1]);

            return $this->urlGenerator->generate(
                'class',
                [
                    'name' => $className,
                    '_fragment' => 'method_' . $methodName[0],
                ]
            );
        }

        return $this->urlGenerator->generate(
            'class',
            [
                'name' => $className,
                '_fragment' => 'constant_' . $fqsenParts[1],
            ]
        );
    }
}
