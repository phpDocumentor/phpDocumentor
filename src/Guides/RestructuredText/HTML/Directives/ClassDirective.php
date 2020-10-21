<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\HTML\Directives;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Directives\SubDirective;
use phpDocumentor\Guides\RestructuredText\Parser;
use function array_map;
use function explode;

class ClassDirective extends SubDirective
{
    public function getName() : string
    {
        return 'class';
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
        if ($document === null) {
            return null;
        }

        $classes = explode(' ', $data);

        $normalizedClasses = array_map(
            static function (string $class) {
                return Environment::slugify($class);
            },
            $classes
        );

        $document->setClasses($normalizedClasses);

        if ($document instanceof DocumentNode) {
            $this->setNodesClasses($document->getNodes(), $classes);
        }

        return $document;
    }

    /**
     * @param Node[] $nodes
     * @param string[] $classes
     */
    private function setNodesClasses(array $nodes, array $classes) : void
    {
        foreach ($nodes as $node) {
            $node->setClasses($classes);

            if (!($node instanceof DocumentNode)) {
                continue;
            }

            $this->setNodesClasses($node->getNodes(), $classes);
        }
    }
}
