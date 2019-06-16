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

namespace phpDocumentor\Transformer\Router;

use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\Pathfinder;

/**
 * Provides a queue of routers to determine the order in which they are executed.
 */
class Queue extends \SplPriorityQueue
{
    /**
     * Tries to match the given node with a rule defined in one of the routers.
     *
     * @param string|Descriptor $node
     *
     * @return Rule|null
     */
    public function match($node)
    {
        /** @var RouterAbstract $router */
        foreach (clone $this as $router) {
            $rule = $router->match($node);
            if ($rule) {
                return $rule;
            }
        }

        return null;
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
     * @throws \InvalidArgumentException if no artifact is provided and no routing rule matches.
     * @throws \UnexpectedValueException if the provided node does not contain anything.
     *
     * @return null|string returns the destination location or false if generation should be aborted.
     */
    public function destination(Descriptor $descriptor, Transformation $transformation): ?string
    {
        $path = $transformation->getTransformer()->getTarget() . DIRECTORY_SEPARATOR . $transformation->getArtifact();
        if (!$transformation->getArtifact()) {
            $rule = $this->match($descriptor);
            if (!$rule) {
                throw new \InvalidArgumentException(
                    'No matching routing rule could be found for the given node, please provide an artifact location, '
                    . 'encountered: ' . ($descriptor === null ? 'NULL' : get_class($descriptor))
                );
            }

            $rule = new ForFileProxy($rule);
            $url = $rule->generate($descriptor);
            if ($url === false || $url[0] !== DIRECTORY_SEPARATOR) {
                return null;
            }

            $path = $transformation->getTransformer()->getTarget()
                . str_replace('/', DIRECTORY_SEPARATOR, $url);
        }

        $finder = new Pathfinder();
        $destination = preg_replace_callback(
            '/{{([^}]+)}}/', // explicitly do not use the unicode modifier; this breaks windows
            function ($query) use ($descriptor, $finder) {
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

        // replace any \ with the directory separator to be compatible with the
        // current filesystem and allow the next file_exists to do its work
        $destination = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $destination);

        // create directory if it does not exist yet
        if (!file_exists(dirname($destination))) {
            mkdir(dirname($destination), 0777, true);
        }

        return $destination;
    }
}
