<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\HTML\Directives;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Directives\SubDirective;
use phpDocumentor\Guides\RestructuredText\Parser;
use function uniqid;

/**
 * Wraps a sub document in a div with a given class
 */
class Wrap extends SubDirective
{
    /** @var string */
    protected $class;

    /** @var bool */
    protected $uniqid;

    public function __construct(string $class, bool $uniqid = false)
    {
        $this->class = $class;
        $this->uniqid = $uniqid;
    }

    public function getName() : string
    {
        return $this->class;
    }

    /**
     * @param string[] $options
     */
    public function processSub(
        Parser $parser,
        ?Node $document,
        string $variable,
        string $data,
        array $options
    ) : ?Node {
        if ($this->uniqid) {
            $id = uniqid($this->class);
        } else {
            $id = '';
        }

        $divOpen = $document->getEnvironment()->getRenderer()->render(
            'div-open.html.twig',
            [
                'id' => $id,
                'class' => $this->class,
            ]
        );

        return $parser->getNodeFactory()->createWrapperNode(
            $document,
            $divOpen,
            '</div>'
        );
    }
}
