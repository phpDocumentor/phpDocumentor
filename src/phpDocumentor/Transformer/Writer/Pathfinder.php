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

use phpDocumentor\Descriptor\Descriptor;
use Traversable;
use function explode;
use function is_array;
use function is_object;
use function method_exists;

final class Pathfinder
{
    /**
     * Combines the query and an object to retrieve a list of nodes that are to be used as node-point in a template.
     *
     * This method interprets the provided query string and walks through the given object to find the correct
     * element. This method will silently fail if an invalid query was provided; in such a case the given object
     * is returned.
     *
     * @param Traversable<string, mixed>|Descriptor $object
     *
     * @return Traversable<mixed>|list<mixed>
     */
    public function find(object $object, string $query) : iterable
    {
        if ($query) {
            $node = $this->walkObjectTree($object, $query);

            if (!is_array($node) && (!$node instanceof Traversable)) {
                $node = [$node];
            }

            return $node;
        }

        return [$object];
    }

    /**
     * Walks an object graph and/or array using a twig query string.
     *
     * @param Traversable<string, mixed>|Descriptor $object
     * @param string $query A path to walk separated by dots, i.e. `namespace.namespaces`.
     *
     * @return mixed
     */
    private function walkObjectTree(object $object, string $query)
    {
        $node = $object;
        $elements = explode('.', $query);

        // walk through the tree
        foreach ($elements as $elementName) {
            if (is_array($node)) {
                if (isset($node[$elementName])) {
                    $node = $node[$elementName];
                    continue;
                }
            } elseif (is_object($node)) {
                if (isset($node->{$elementName}) || (method_exists($node, '__get') && $node->{$elementName})) {
                    $node = $node->{$elementName};
                    continue;
                }

                if (method_exists($node, $elementName)) {
                    $node = $node->{$elementName}();
                    continue;
                }

                if (method_exists($node, 'get' . $elementName)) {
                    $elementName = 'get' . $elementName;
                    $node = $node->{$elementName}();
                    continue;
                }

                if (method_exists($node, 'is' . $elementName)) {
                    $elementName = 'is' . $elementName;
                    $node = $node->{$elementName}();
                    continue;
                }
            }

            return null;
        }

        return $node;
    }
}
