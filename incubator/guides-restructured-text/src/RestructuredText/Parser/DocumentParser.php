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
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\TitleNode;
use phpDocumentor\Guides\RestructuredText\Directives\Directive as DirectiveHandler;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;

use function md5;

class DocumentParser
{
    /** @var bool public is temporary */
    public $nextIndentedBlockShouldBeALiteralBlock = false;

    /** @var ?TitleNode public is temporary */
    public $lastTitleNode;

    /** @var ArrayObject<int, TitleNode> public is temporary */
    public $openSectionsAsTitleNodes;

    /** @var DocumentNode */
    private $document;

    /** @var LinesIterator */
    private $documentIterator;

    /** @var Productions\Rule */
    private $startingRule;
    private MarkupLanguageParser $parser;

    /**
     * @param DirectiveHandler[] $directives
     */
    public function __construct(
        MarkupLanguageParser $parser,
        array $directives
    ) {
        $this->documentIterator = new LinesIterator();
        $this->openSectionsAsTitleNodes = new ArrayObject();

        $this->startingRule = new Productions\DocumentRule($this, $parser, $directives);
        $this->parser = $parser;
    }

    public function parse(string $contents): DocumentNode
    {
        $this->document = new DocumentNode(md5($contents), $this->parser->getEnvironment()->getCurrentFileName());
        $this->documentIterator->load($contents);

        if ($this->startingRule->applies($this)) {
            $this->startingRule->apply($this->documentIterator, $this->document);
        }

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
