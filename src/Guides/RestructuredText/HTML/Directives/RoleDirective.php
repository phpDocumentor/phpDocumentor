<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 * @author Ryan Weaver <ryan@symfonycasts.com> on the original DocBuilder.
 * @author Mike van Riel <me@mikevanriel.com> for adapting this to phpDocumentor.
 */

namespace phpDocumentor\Guides\RestructuredText\HTML\Directives;

use phpDocumentor\Guides\RestructuredText\Directives\SubDirective;

class RoleDirective extends SubDirective
{
    public function getName() : string
    {
        return 'role';
    }
}
