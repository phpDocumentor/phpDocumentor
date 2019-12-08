<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Router;

use Cocur\Slugify\Slugify;
use phpDocumentor\Reflection\Fqsen;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

    /** @var Slugify */
    private $slugify;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->slugify = new Slugify();
    }

    /**
     * Generates a URL from the given node or returns false if unable.
     */
    public function __invoke(Fqsen $fqsen): string
    {
        $fqsenParts = explode('::', (string) $fqsen);
        $className = $this->slugify($fqsenParts[0]);

        if (count($fqsenParts) === 1) {
            return $this->urlGenerator->generate(
                'class',
                ['className' => $className]
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

    private function slugify(string $string, bool $lowercase = false, string $default = '') : string
    {
        return $this->slugify->slugify($string, ['lowercase' => $lowercase]) ?: $default;
    }
}
