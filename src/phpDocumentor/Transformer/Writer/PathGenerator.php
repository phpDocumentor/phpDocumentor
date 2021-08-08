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

namespace phpDocumentor\Transformer\Writer;

use InvalidArgumentException;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Transformer\Router\Router;
use phpDocumentor\Transformer\Transformation;
use RuntimeException;
use Symfony\Component\String\UnicodeString;
use UnexpectedValueException;

use function array_map;
use function current;
use function explode;
use function get_class;
use function implode;
use function is_string;
use function preg_replace_callback;
use function sprintf;
use function strpos;
use function trim;

class PathGenerator
{
    /** @var Router */
    private $router;

    /** @var Pathfinder */
    private $pathfinder;

    public function __construct(Router $router, Pathfinder $pathfinder)
    {
        $this->router = $router;
        $this->pathfinder = $pathfinder;
    }

    /**
     * Uses the currently selected node and transformation to assemble the destination path for the file.
     *
     * Writers accept the use of a Query to be able to generate output for multiple objects using the same
     * template.
     *
     * The given node is the result of such a query, or if no query given the selected element, and the transformation
     * contains the destination file.
     *
     * Since it is important to be able to generate a unique name per element can the user provide a template variable
     * in the name of the file.
     * Such a template variable always resides between double braces and tries to take the node value of a given
     * query string.
     *
     * Example:
     *
     *   An artifact stating `classes/{{name}}.html` will try to find the
     *   node 'name' as a child of the given $node and use that value instead.
     *
     * @return string returns the destination location or false if generation should be aborted.
     *
     * @throws InvalidArgumentException If no artifact is provided and no routing rule matches.
     * @throws UnexpectedValueException If the provided node does not contain anything.
     */
    public function generate(Descriptor $descriptor, Transformation $transformation): string
    {
        $path = $this->determinePath($descriptor, $transformation);

        return $this->replaceVariablesInPath($path, $descriptor);
    }

    private function determinePath(Descriptor $descriptor, Transformation $transformation): string
    {
        $path = '/' . $transformation->getArtifact();
        if (!$transformation->getArtifact()) {
            $path = $this->router->generate($descriptor);
            if (!$path) {
                throw new InvalidArgumentException(
                    'No matching routing rule could be found for the given node, please provide an artifact location, '
                    . 'encountered: ' . get_class($descriptor)
                );
            }
        }

        return $path;
    }

    private function replaceVariablesInPath(string $path, Descriptor $descriptor): string
    {
        $destination = preg_replace_callback(
            '/{{([^}]*)}}/', // explicitly do not use the unicode modifier; this breaks windows
            function (array $query) use ($path, $descriptor) {
                $variable = $query[1];
                if (!$variable) {
                    throw new RuntimeException(
                        sprintf('Variable substitution in path %s failed, no variable was specified', $path)
                    );
                }

                // Find value in Descriptor's properties / methods
                $value = (string) current($this->pathfinder->find($descriptor, $variable));

                // strip any special characters and surrounding \ or /
                $filepart = trim(trim($value), '\\/');

                if ($filepart === '') {
                    throw new RuntimeException(
                        sprintf(
                            'Variable substitution in path %s failed, variable "%s" did not return a value',
                            $path,
                            $variable
                        )
                    );
                }

                // make it windows proof by transliterating to ASCII and by url encoding
                $filepart = (new UnicodeString($filepart))->ascii()->toString();

                return strpos($filepart, '/') !== false
                    ? implode('/', array_map('urlencode', explode('/', $filepart)))
                    : implode('\\', array_map('urlencode', explode('\\', $filepart)));
            },
            $path
        );

        if (!is_string($destination)) {
            throw new RuntimeException(sprintf('Variable substitution in path %s failed', $path));
        }

        return $destination;
    }
}
