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

namespace phpDocumentor\GuidesDirectivesFixture;

use phpDocumentor\Guides\Compiler\CompilerContextInterface;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Directives\Attributes\Directive;
use phpDocumentor\Guides\RestructuredText\Directives\Attributes\Option;
use phpDocumentor\Guides\RestructuredText\Directives\BaseDirective;
use phpDocumentor\Guides\RestructuredText\Directives\OptionType;
use phpDocumentor\Guides\RestructuredText\Nodes\DirectiveNode;

/**
 * A directive used purely as a fixture for the guides-directives extension's
 * end-to-end test: its `#[Option]` attributes are what the
 * `phpdoc-guides:directive-options-list` directive documents.
 */
#[Directive(name: 'example-directive')]
#[Option(name: 'template', type: OptionType::String, description: 'The name of the template to render.')]
#[Option(name: 'force', type: OptionType::Boolean, default: false, description: 'Overwrite the output when it already exists.')]
final class ExampleDirective extends BaseDirective
{
    public function createNode(DirectiveNode $directiveNode, CompilerContextInterface|null $compilerContext = null): Node|null
    {
        return null;
    }
}
