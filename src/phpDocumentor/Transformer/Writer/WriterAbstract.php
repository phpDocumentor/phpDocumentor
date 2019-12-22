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
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Router\Router;
use phpDocumentor\Transformer\Transformation;
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
use function mkdir;
use function preg_replace_callback;
use function str_replace;
use function strpos;
use function trim;
use function var_dump;

/**
 * Base class for the actual transformation business logic (writers).
 */
abstract class WriterAbstract
{
    /**
     * This method verifies whether PHP has all requirements needed to run this writer.
     *
     * If one of the requirements is missing for this Writer then an exception of type RequirementMissing
     * should be thrown; this indicates to the calling process that this writer will not function.
     *
     * @throws Exception\RequirementMissing When a requirements is missing stating which one.
     */
    public function checkRequirements() : void
    {
        // empty body since most writers do not have requirements
    }

    /**
     * Checks if there is a space in the path.
     *
     * @throws InvalidArgumentException If path contains a space.
     */
    protected function checkForSpacesInPath(string $path) : void
    {
        if (strpos($path, ' ') !== false) {
            throw new InvalidArgumentException('No spaces allowed in destination path: ' . $path);
        }
    }

    /**
     * Abstract definition of the transformation method.
     *
     * @param ProjectDescriptor $project Document containing the structure.
     * @param Transformation $transformation Transformation to execute.
     */
    abstract public function transform(ProjectDescriptor $project, Transformation $transformation) : void;

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
    public function destination(Descriptor $descriptor, Transformation $transformation) : ?string
    {
        $path = $transformation->getTransformer()->getTarget() . DIRECTORY_SEPARATOR . $transformation->getArtifact();
        if (!$transformation->getArtifact()) {
            if (!$this->router()) {
                throw new InvalidArgumentException(
                    'The artifact location needs to be provided by this transformation; '
                    . 'the writer doesn\'t support automatically determining paths'
                );
            }

            $url = $this->router()->generate($descriptor);
            if (!$url) {
                throw new InvalidArgumentException(
                    'No matching routing rule could be found for the given node, please provide an artifact location, '
                    . 'encountered: ' . get_class($descriptor)
                );
            }

            if (!$url || $url[0] !== DIRECTORY_SEPARATOR) {
                return null;
            }

            $path = $transformation->getTransformer()->getTarget()
                . str_replace('/', DIRECTORY_SEPARATOR, $url);
        }

        $finder = new Pathfinder();
        $destination = preg_replace_callback(
            '/{{([^}]+)}}/', // explicitly do not use the unicode modifier; this breaks windows
            static function ($query) use ($descriptor, $finder) {
                // strip any surrounding \ or /
                $filepart = trim((string) current($finder->find($descriptor, $query[1])), '\\/');

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

        // create directory if it does not exist yet
        if (dirname($destination) && !file_exists(dirname($destination))) {
            mkdir(dirname($destination), 0777, true);
        }

        return $destination;
    }

    public function __toString() : string
    {
        return static::class;
    }

    protected function router() : ?Router
    {
        return null;
    }
}
