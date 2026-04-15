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

namespace phpDocumentor\Extension;

use SplFileInfo;

interface ExtensionLoader
{
    public function supports(SplFileInfo $dir): bool;

    public function load(SplFileInfo $dir): ExtensionInfo|null;
}
