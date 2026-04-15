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

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Reflection\Php\Expression;

interface EnumCaseInterface extends ElementInterface, ChildInterface, AttributedInterface
{
    public function setFile(FileInterface $file): void;

    public function getValue(): Expression|null;
}
