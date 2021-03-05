<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\NodeFactory;

use InvalidArgumentException;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\NodeTypes;
use phpDocumentor\Guides\Renderers\NodeRenderer;
use phpDocumentor\Guides\Renderers\NodeRendererFactory;
use function in_array;
use function is_subclass_of;
use function sprintf;

class NodeInstantiator
{
    /** @var string */
    private $type;

    /** @var string */
    private $className;

    /** @var NodeRenderer */
    private $nodeRenderer;

    /** @var Environment */
    private $environment;

    public function __construct(
        string $type,
        string $className,
        NodeRenderer $nodeRenderer,
        Environment $environment
    ) {
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
        $this->nodeRenderer = $nodeRenderer;
        $this->environment = $environment;
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
        /** @var Node $node */
        $node = new $this->className(...$arguments);
        $node->setNodeRenderer($this->nodeRenderer);
        $node->setEnvironment($this->environment);

        return $node;
    }
}
