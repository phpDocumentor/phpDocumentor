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

namespace phpDocumentor\Transformer\Writer;

use InvalidArgumentException;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Transformer\Router\Router;
use phpDocumentor\Transformer\Transformation;
use RuntimeException;
use UnexpectedValueException;
use const DIRECTORY_SEPARATOR;
use function array_map;
use function current;
use function dirname;
use function explode;
use function extension_loaded;
use function file_exists;
use function get_class;
use function iconv;
use function implode;
use function is_string;
use function mkdir;
use function preg_replace_callback;
use function sprintf;
use function str_replace;
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
     * @return string|null returns the destination location or false if generation should be aborted.
     *
     * @throws InvalidArgumentException If no artifact is provided and no routing rule matches.
     * @throws UnexpectedValueException If the provided node does not contain anything.
     */
    public function generate(Descriptor $descriptor, Transformation $transformation) : ?string
    {
        $path = $this->determinePath($descriptor, $transformation);
        if ($path === null) {
            return null;
        }

        $destination = $transformation->getTransformer()->getTarget()
            . $this->replaceVariablesInPath($path, $descriptor);

        $this->ensureDirectoryExists($destination);

        return $destination;
    }

    private function replaceVariablesInPath(string $path, Descriptor $descriptor) : string
    {
        $destination = preg_replace_callback(
            '/{{([^}]+)}}/', // explicitly do not use the unicode modifier; this breaks windows
            function ($query) use ($descriptor) {
                // strip any surrounding \ or /
                $filepart = trim((string) current($this->pathfinder->find($descriptor, $query[1])), '\\/');

                // make it windows proof
                if (extension_loaded('iconv')) {
                    $filepart = iconv('UTF-8', 'ASCII//TRANSLIT', $filepart);
                }

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

    private function determinePath(Descriptor $descriptor, Transformation $transformation) : ?string
    {
        $path = DIRECTORY_SEPARATOR . $transformation->getArtifact();
        if (!$transformation->getArtifact()) {
            $url = $this->router->generate($descriptor);
            if (!$url) {
                throw new InvalidArgumentException(
                    'No matching routing rule could be found for the given node, please provide an artifact location, '
                    . 'encountered: ' . get_class($descriptor)
                );
            }

            $path = $url[0] === DIRECTORY_SEPARATOR
                ? str_replace('/', DIRECTORY_SEPARATOR, $url)
                : null;
        }

        return $path;
    }

    private function ensureDirectoryExists(string $destination) : void
    {
        if (!dirname($destination) || file_exists(dirname($destination))) {
            return;
        }

        mkdir(dirname($destination), 0777, true);
    }
}
