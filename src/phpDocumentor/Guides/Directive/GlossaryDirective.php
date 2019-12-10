<?php

/*
 * This file is part of the Docs Builder package.
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace phpDocumentor\Guides\Directive;

use Doctrine\RST\Directives\SubDirective;

/**
 * @deprecated
 */
class GlossaryDirective extends SubDirective
{
    public function getName(): string
    {
        return 'glossary';
    }
}
