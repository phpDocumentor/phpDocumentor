<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser;

use Doctrine\Common\EventManager;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\ParagraphNode;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\RestructuredText\Event\OnLinkParsedEvent;
use phpDocumentor\Guides\RestructuredText\Parser;

use Webmozart\Assert\Assert;
use function array_map;
use function array_shift;
use function count;
use function explode;
use function ltrim;
use function mb_strlen;
use function preg_match;
use function strlen;
use function substr;
use function trim;

class LineDataParser
{
    /** @var Parser */
    private $parser;

    /** @var EventManager */
    private $eventManager;

    public function __construct(Parser $parser, EventManager $eventManager)
    {
        $this->parser       = $parser;
        $this->eventManager = $eventManager;
    }

    public function parseLink(string $line): ?Link
    {
        // Links
        if (preg_match('/^\.\. _`(.+)`: (.+)$/mUsi', $line, $match) > 0) {
            return $this->createLink($match[1], $match[2], Link::TYPE_LINK);
        }

        // anonymous links
        if (preg_match('/^\.\. _(.+): (.+)$/mUsi', $line, $match) > 0) {
            return $this->createLink($match[1], $match[2], Link::TYPE_LINK);
        }

        // Short anonymous links
        if (preg_match('/^__ (.+)$/mUsi', trim($line), $match) > 0) {
            $url = $match[1];

            return $this->createLink('_', $url, Link::TYPE_LINK);
        }

        // Anchor links - ".. _`anchor-link`:"
        if (preg_match('/^\.\. _`(.+)`:$/mUsi', trim($line), $match) > 0) {
            $anchor = $match[1];

            return new Link($anchor, '#' . $anchor, Link::TYPE_ANCHOR);
        }

        if (preg_match('/^\.\. _(.+):$/mUsi', trim($line), $match) > 0) {
            $anchor = $match[1];

            return $this->createLink($anchor, '#' . $anchor, Link::TYPE_ANCHOR);
        }

        return null;
    }

    private function createLink(string $name, string $url, string $type): Link
    {
        $this->eventManager->dispatchEvent(
            OnLinkParsedEvent::ON_LINK_PARSED,
            new OnLinkParsedEvent($url, $type, $this->parser->getEnvironment()->getCurrentFileName())
        );

        return new Link($name, $url, $type);
    }

    public function parseDirectiveOption(string $line): ?DirectiveOption
    {
        if (preg_match('/^(\s+):(.+): (.*)$/mUsi', $line, $match) > 0) {
            return new DirectiveOption($match[2], trim($match[3]));
        }

        if (preg_match('/^(\s+):(.+):(\s*)$/mUsi', $line, $match) > 0) {
            return new DirectiveOption($match[2], true);
        }

        return null;
    }

    public function parseDirective(string $line): ?Directive
    {
        if (preg_match('/^\.\. (\|(.+)\| |)([^\s]+)::( (.*)|)$/mUsi', $line, $match) > 0) {
            return new Directive(
                $match[2],
                $match[3],
                trim($match[4])
            );
        }

        return null;
    }

    /**
     * @param string[] $lines
     *
     * @return ListItem[]
     */
    public function parseList(array $lines): array
    {
        $list          = [];
        $currentItem   = null;
        $currentPrefix = null;
        $currentOffset = 0;

        $createListItem = function (string $item, string $prefix): ListItem {
            // parse any markup in the list item (e.g. sublists, directives)
            $nodes = $this->parser->getSubParser()->parseLocal($item)->getNodes();
            if (count($nodes) === 1 && $nodes[0] instanceof ParagraphNode) {
                // if there is only one paragraph node, the value is put directly in the <li> element
                $nodes = [$nodes[0]->getValue()];
            }


            Assert::allIsInstanceOf($nodes, Node::class);

            return new ListItem($prefix, mb_strlen($prefix) > 1, $nodes);
        };

        foreach ($lines as $line) {
            if (preg_match(LineChecker::LIST_MARKER, $line, $m) > 0) {
                // a list marker indicates the start of a new list item,
                // complete the previous one and start a new one
                if ($currentItem !== null) {
                    $list[] = $createListItem($currentItem, $currentPrefix);
                }

                $currentOffset = strlen($m[0]);
                $currentPrefix = $m[1];
                $currentItem   = substr($line, $currentOffset) . "\n";

                continue;
            }

            // the list item offset is determined by the offset of the first text
            if (trim($currentItem) === '') {
                $currentOffset = strlen($line) - strlen(ltrim($line));
            }

            $currentItem .= substr($line, $currentOffset) . "\n";
        }

        if ($currentItem !== null) {
            $list[] = $createListItem($currentItem, $currentPrefix);
        }

        return $list;
    }

    /**
     * @param string[] $lines
     */
    public function parseDefinitionList(array $lines): DefinitionList
    {
        /** @var array{term: SpanNode, classifiers: list<SpanNode>, definition: string}|null $definitionListTerm */
        $definitionListTerm = null;
        $definitionList     = [];

        $createDefinitionTerm = function (array $definitionListTerm): DefinitionListTerm {
            // parse any markup in the definition (e.g. lists, directives)
            $definitionNodes = $this->parser->getSubParser()->parseLocal($definitionListTerm['definition'])->getNodes();
            if (count($definitionNodes) === 1 && $definitionNodes[0] instanceof ParagraphNode) {
                // if there is only one paragraph node, the value is put directly in the <dd> element
                $definitionNodes = [$definitionNodes[0]->getValue()];
            } else {
                // otherwise, .first and .last are added to the first and last nodes of the definition
                $definitionNodes[0]->setClasses($definitionNodes[0]->getClasses() + ['first']);
                $definitionNodes[count($definitionNodes) - 1]->setClasses(
                    $definitionNodes[count($definitionNodes) - 1]->getClasses() + ['last']
                );
            }

            Assert::allIsInstanceOf($definitionNodes, Node::class);

            return new DefinitionListTerm(
                $definitionListTerm['term'],
                $definitionListTerm['classifiers'],
                $definitionNodes
            );
        };

        $currentOffset = 0;
        foreach ($lines as $line) {
            // indent or empty line = term definition line
            if ($definitionListTerm !== null && (trim($line) === '') || $line[0] === ' ') {
                if ($currentOffset === 0) {
                    // first line of a definition determines the indentation offset
                    $definition    = ltrim($line);
                    $currentOffset = strlen($line) - strlen($definition);
                } else {
                    $definition = substr($line, $currentOffset);
                }

                $definitionListTerm['definition'] .= $definition . "\n";

            // non empty string at the start of the line = definition term
            } elseif (trim($line) !== '') {
                // we are starting a new term so if we have an existing
                // term with definitions, add it to the definition list
                if ($definitionListTerm !== null) {
                    $definitionList[] = $createDefinitionTerm($definitionListTerm);
                }

                $parts       = explode(' : ', trim($line));
                $term        = array_shift($parts);
                $classifiers = array_map(function (string $classifier): SpanNode {
                    return $this->parser->createSpanNode($classifier);
                }, array_map('trim', $parts));

                $currentOffset      = 0;
                $definitionListTerm = [
                    'term' => $this->parser->createSpanNode($term),
                    'classifiers' => $classifiers,
                    'definition' => '',
                ];
            }
        }

        // append the last definition of the list
        if ($definitionListTerm !== null) {
            $definitionList[] = $createDefinitionTerm($definitionListTerm);
        }

        return new DefinitionList($definitionList);
    }
}
