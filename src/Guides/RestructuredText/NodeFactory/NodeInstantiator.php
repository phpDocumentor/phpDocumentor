<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\NodeFactory;

use InvalidArgumentException;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\NodeTypes;
use function in_array;
use function is_subclass_of;
use function sprintf;

class NodeInstantiator
{
    /** @var string */
    private $type;

    /** @var string */
    private $className;

    public function __construct(string $type, string $className)
    {
        if (!in_array($type, NodeTypes::NODES, true)) {
            throw new InvalidArgumentException(
                sprintf('Node type %s is not a valid node type.', $type)
            );
        }

        if (!is_subclass_of($className, Node::class)) {
            throw new InvalidArgumentException(
                sprintf('%s class is not a subclass of %s', $className, Node::class)
            );
        }

        $this->type = $type;
        $this->className = $className;
    }

    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @param mixed[] $arguments
     */
    public function create(array $arguments) : Node
    {
        return new $this->className(...$arguments);
    }
}
