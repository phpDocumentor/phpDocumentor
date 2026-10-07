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

namespace phpDocumentor\GuidesDirectives\Directives;

use phpDocumentor\Guides\Compiler\CompilerContextInterface;
use phpDocumentor\Guides\Compiler\DescriptorAwareCompilerContext;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Directives\Attributes\Directive;
use phpDocumentor\Guides\RestructuredText\Directives\BaseDirective;
use phpDocumentor\Guides\RestructuredText\Nodes\DirectiveNode;
use phpDocumentor\GuidesDirectives\Nodes\DirectiveOptionsList as OptionsNode;
use phpDocumentor\Query\Engine;

use function count;
use function iterator_to_array;

#[Directive(name: 'phpdoc-guides:directive-options-list')]
final class DirectiveOptionsList extends BaseDirective
{
    public function __construct(private Engine $engine)
    {
    }

    public function createNode(
        DirectiveNode $directiveNode,
        CompilerContextInterface|null $compilerContext = null,
    ): Node|null {
        if ($compilerContext instanceof DescriptorAwareCompilerContext === false) {
            return null;
        }

        $result = iterator_to_array($this->engine->perform(
            $compilerContext->getVersionDescriptor(),
            //phpcs:ignore Generic.Files.LineLength.TooLong
            '$.documentationSets[*][?(type(@) == \'ApiSetDescriptor\')].indexes.classes[*][?(@.attributes[?(@.attribute == "\phpDocumentor\Guides\RestructuredText\Directives\Attributes\Directive" && @.arguments[?(@.name == "name" && @.value == "\'' . $directiveNode->getDirective()->getData() . '\'")])])]',
        ));

        if (count($result) === 0) {
            return null;
        }

        return (new OptionsNode(''))->withDescriptor($result[0]);
    }
}
