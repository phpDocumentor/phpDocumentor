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

namespace phpDocumentor\Guides\RestructuredText\Parser;

use ArrayObject;
use Doctrine\Common\EventManager;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\TitleNode;
use phpDocumentor\Guides\RestructuredText\Directives\Directive as DirectiveHandler;
use phpDocumentor\Guides\RestructuredText\Event\PostParseDocumentEvent;
use phpDocumentor\Guides\RestructuredText\Event\PreParseDocumentEvent;
use phpDocumentor\Guides\RestructuredText\Parser;

use function md5;
use function trim;

class DocumentParser
{
    /** @var bool public is temporary */
    public $nextIndentedBlockShouldBeALiteralBlock = false;

    /** @var ?TitleNode public is temporary */
    public $lastTitleNode;

    /** @var ArrayObject<int, TitleNode> public is temporary */
    public $openSectionsAsTitleNodes;

    /** @var Parser */
    private $parser;

    /** @var EventManager */
    private $eventManager;

    /** @var DocumentNode */
    private $document;

    /** @var LinesIterator */
    private $documentIterator;

    /** @var Productions\Rule */
    private $startingRule;

    /**
     * @param DirectiveHandler[] $directives
     */
    public function __construct(
        Parser $parser,
        EventManager $eventManager,
        array $directives
    ) {
        $this->parser = $parser;
        $this->eventManager = $eventManager;

        $this->documentIterator = new LinesIterator();
        $this->openSectionsAsTitleNodes = new ArrayObject();

        $this->startingRule = new Productions\DocumentRule($this, $parser, $eventManager, $directives);
    }

    public function parse(string $contents): DocumentNode
    {
        $preParseDocumentEvent = new PreParseDocumentEvent($this->parser, $contents);

        $this->eventManager->dispatchEvent(
            PreParseDocumentEvent::PRE_PARSE_DOCUMENT,
            $preParseDocumentEvent
        );

        $this->document = new DocumentNode(md5($contents));
        $this->parser->getReferenceBuilder()->scope($this->document);

        $this->documentIterator->load(
            $this->parser->getEnvironment(),
            trim($preParseDocumentEvent->getContents())
        );

        if ($this->startingRule->applies($this)) {
            $this->startingRule->apply($this->documentIterator, $this->document);
        }

        $this->eventManager->dispatchEvent(
            PostParseDocumentEvent::POST_PARSE_DOCUMENT,
            new PostParseDocumentEvent($this->document)
        );

        return $this->document;
    }

    public function getDocument(): DocumentNode
    {
        return $this->document;
    }

    public function getDocumentIterator(): LinesIterator
    {
        return $this->documentIterator;
    }
}
