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
use phpDocumentor\Guides\RestructuredText\Directives\BaseDirective;
use phpDocumentor\Guides\RestructuredText\Nodes\DirectiveNode;

/**
 * A directive with aliases and without options.
 *
 * Used by the end-to-end test of `phpdoc-guides:directive-list`.
 *
 * @deprecated this tag must not show up in the directive list.
 */
#[Directive(name: 'aliased-directive', aliases: ['aliased', 'alias-two'])]
final class AliasedDirective extends BaseDirective
{
    public function createNode(
        DirectiveNode $directiveNode,
        CompilerContextInterface|null $compilerContext = null,
    ): Node|null {
        return null;
    }
}
