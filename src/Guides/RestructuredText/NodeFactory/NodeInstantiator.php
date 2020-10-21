<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\NodeFactory;

use InvalidArgumentException;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\NodeTypes;
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

    /** @var NodeRendererFactory|null */
    private $nodeRendererFactory;

    /** @var Environment|null */
    private $environment;

    public function __construct(
        string $type,
        string $className,
        ?NodeRendererFactory $nodeRendererFactory = null,
        ?Environment $environment = null
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
        $this->nodeRendererFactory = $nodeRendererFactory;
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

        if ($this->environment !== null) {
            $node->setEnvironment($this->environment);
        }

        if ($this->nodeRendererFactory !== null) {
            $node->setNodeRendererFactory($this->nodeRendererFactory);
        }

        return $node;
    }
}
