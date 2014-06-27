<?php

namespace phpDocumentor\Plugin\Core\Transformer\Writer;

class Pathfinder
{
    /**
     * Combines the query and an object to retrieve a list of nodes that are to be used as node-point in a template.
     *
     * This method interprets the provided query string and walks through the given object to find the correct
     * element. This method will silently fail if an invalid query was provided; in such a case the given object
     * is returned.
     *
     * @param object $object
     * @param string $query
     *
     * @return \Traversable|mixed[]
     */
    public function find($object, $query)
    {
        if ($query) {
            $node = $this->walkObjectTree($object, $query);

            if (!is_array($node) && (!$node instanceof \Traversable)) {
                $node = array($node);
            }

            return $node;
        }

        return array($object);
    }

    /**
     * Walks an object graph and/or array using a twig query string.
     *
     * @param \Traversable|mixed $objectOrArray
     * @param string             $query         A path to walk separated by dots, i.e. `namespace.namespaces`.
     *
     * @return mixed
     */
    private function walkObjectTree($objectOrArray, $query)
    {
        $node = $objectOrArray;
        $objectPath = explode('.', $query);

        // walk through the tree
        foreach ($objectPath as $pathNode) {
            if (is_array($node)) {
                if (isset($node[$pathNode])) {
                    $node = $node[$pathNode];
                    continue;
                }
            } elseif (is_object($node)) {
                if (isset($node->$pathNode) || (method_exists($node, '__get') && $node->$pathNode)) {
                    $node = $node->$pathNode;
                    continue;
                } elseif (method_exists($node, $pathNode)) {
                    $node = $node->$pathNode();
                    continue;
                } elseif (method_exists($node, 'get' . $pathNode)) {
                    $pathNode = 'get' . $pathNode;
                    $node = $node->$pathNode();
                    continue;
                } elseif (method_exists($node, 'is' . $pathNode)) {
                    $pathNode = 'is' . $pathNode;
                    $node = $node->$pathNode();
                    continue;
                }
            }

            return null;
        }

        return $node;
    }
}
