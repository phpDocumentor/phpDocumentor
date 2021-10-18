<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\GenericNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;

/**
 * A directive is like a function you can call or apply to a block
 * Il looks like:
 *
 * .. function:: main
 *     :arg1: value
 *     :arg2: otherValue
 *
 *     Some block !
 *
 *  The directive can define variables, create special nodes or change
 *  the node that directly follows it
 */
abstract class Directive
{
    /**
     * Get the directive name
     */
    abstract public function getName(): string;

    /**
     * Allow a directive to be registered under multiple names.
     *
     * Aliases can be used for directives whose name has been deprecated or allows for multiple spellings.
     *
     * @return string[]
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * This is the function called by the parser to process the directive, it can be overloaded
     * to do anything with the document, like tweaking nodes or change the environment
     *
     * The node that directly follows the directive is also passed to it
     *
     * @param MarkupLanguageParser $parser the calling parser
     * @param Node|null $node the node that follows the directive
     * @param string $variable the variable name of the directive
     * @param string $data the data of the directive (following ::)
     * @param string[] $options the array of options for this directive
     */
    public function process(
        MarkupLanguageParser $parser,
        ?Node $node,
        string $variable,
        string $data,
        array $options
    ): void {
        $document = $parser->getDocument();

        $processNode = $this->processNode($parser, $variable, $data, $options)
            // Ensure options are always available
            ->withOptions($options);

        if ($variable !== '') {
            $environment = $parser->getEnvironment();
            $environment->setVariable($variable, $processNode);
        } else {
            $document->addNode($processNode);
        }

        if ($node === null) {
            return;
        }

        $document->addNode($node);
    }

    /**
     * This can be overloaded to write a directive that just create one node for the
     * document, which is common
     *
     * The arguments are the same that process
     *
     * @param string[] $options
     */
    public function processNode(MarkupLanguageParser $parser, string $variable, string $data, array $options): Node
    {
        $this->processAction($parser, $variable, $data, $options);

        return new GenericNode($variable, $data);
    }

    /**
     * This can be overloaded to write a directive that just do an action without changing
     * the nodes of the document
     *
     * The arguments are the same that process
     *
     * @param string[] $options
     */
    public function processAction(MarkupLanguageParser $parser, string $variable, string $data, array $options): void
    {
    }
}
