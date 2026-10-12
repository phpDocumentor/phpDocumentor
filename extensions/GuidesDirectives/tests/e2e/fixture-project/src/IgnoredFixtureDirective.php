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

use phpDocumentor\Guides\RestructuredText\Directives\Attributes\Directive;
use phpDocumentor\Guides\RestructuredText\Directives\BaseDirective;

/** An abstract class carrying the attribute, which must not be listed. */
#[Directive(name: 'abstract-directive')]
abstract class IgnoredFixtureDirective extends BaseDirective
{
}
